<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockInRequest;
use App\Models\Product;
use App\Models\StockGroup;
use App\Models\StockIn;
use App\Models\StockProduct;
use App\Models\Operation;
use App\Models\Supplier;
use App\Models\ProductUnit;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Auth;

class StockInController extends Controller
{
    /**
     * index
     *

     */
    public function index(Request $request)
    {

        $stockIn = StockIn::with(['stockProduct'])->orderBy('create_day','desc')->get();
        $suppliers = Supplier::all();
        $stockGroups = StockGroup::all();
        $dataSearch = [];
        if($request->isMethod('post')){
            $dataSearch = $request->all();
            $builder = StockIn::query();
            $builder = $builder->leftJoin("stock_products",function($join){
                $join->on("stock_products.stock_id","=","stock_in.id")
                    ->where("stock_products.type","=",STOCK_IN);
            });
            $builder = $builder->select('stock_in.*');
            if(!empty($request->get('bill_number'))){
                $billNumber = $request->get('bill_number');
                $builder = $builder->where('stock_in.bill_number', 'LIKE', "%$billNumber%");
            }
            if(!empty($request->get('note'))){
                $note = $request->get('note');
                $builder = $builder->where('stock_in.note', 'LIKE', "%$note%");
            }
            if(!empty($request->get('create_date_from'))){
                $create_from = Carbon::createFromFormat(config('app.date_format'), $request->get('create_date_from'))->format('Y-m-d');
                $builder = $builder->whereDate('stock_in.create_day', '>=', $create_from);
            }
            if(!empty($request->get('create_date_to'))){
                $createDateTo = Carbon::createFromFormat(config('app.date_format'), $request->get('create_date_to'))->format('Y-m-d');
                $builder = $builder->whereDate('stock_in.create_day', '<=', $createDateTo);
            }
            if(!empty($request->get('supplier_id'))){
                $supplierId = $request->get('supplier_id');
                $builder = $builder->where('stock_in.supplier_id', '=', "$supplierId");
            }
            if(!empty($request->get('warehouse_id'))){
                $warehouseId = $request->get('warehouse_id');
                $builder = $builder->where('stock_products.stock_group_id', '=', "$warehouseId");
            }
            $stockIn = $builder->orderBy('create_day','desc')->get();
        }

        // dd($stockIn);
        return view('admin.stock.stock_in_list',['stockIn'=>$stockIn,'suppliers'=>$suppliers,'stockGroups'=>$stockGroups,'dataSearch'=>$dataSearch]);
        // return view('admin.stock.stock_in_list');
    }
    /**
     * create
     *
     */
    public function create()
    {
        $shop_id = isset(auth()->user()->shop_id) ? auth()->user()->shop_id : null;
        $billNumber = intval(StockIn::max("id")) + 1;
        $billNumber = $billNumber < 10 ? "PN0" . $billNumber : "PN" . $billNumber;
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $stockGroups = StockGroup::pluck('name', 'id')->toArray();
        $units = ProductUnit::query()->pluck('name','id')->toArray();
        if(empty($stockGroups)){
            $stockGroups = [
                '' => 'Chưa thêm kho'
            ];
        }
        $entity = new StockIn();
        return view('admin.stock.stock_in_import')->with(compact('billNumber', 'suppliers', 'stockGroups', 'entity','units'));
    }

    /**
     * edit
     *

     */
    public function edit($id = null)
    {
        $entity = StockIn::findOrFail($id);
        $billNumber = $entity->bill_number;
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $stockGroups = StockGroup::pluck('name', 'id')->toArray();
        $units = ProductUnit::query()->pluck('name','id')->toArray();
        if(empty($stockGroups)){
            $stockGroups = [
                '' => 'Chưa thêm kho'
            ];
        }
        $stockProducts = StockProduct::with('product')->where(["stock_id" => $id, 'type' => STOCK_IN])->get();
        // dd($stockProducts);
        return view('admin.stock.stock_in_import')->with(compact('billNumber', 'suppliers', 'stockGroups', 'entity','stockProducts','units'));
    }

    /**
     * store
     *
     */
    public function store(Request $request)
    {
        try {
            $params = $request->all();
            $stockData = $request->only(['create_day', 'bill_number', 'deliver_name', 'receiver_name', 'note', 'supplier_id', 'total']);
            $stockData['shop_id'] = getCurrentUser()->shop_id;
            $validator = new StockInRequest($stockData,$request->get('id'));
            $validatorStatus = $validator->validate();
            if ($validatorStatus !== true) {
                return $this->responseWithErrorMessage($validatorStatus);
            }
            DB::beginTransaction();
            if(empty($request->get('id'))){
                $stockIn = new StockIn();
            } else{
                $stockIn = StockIn::findOrFail($request->get('id'));
            }
            $stockIn->fill($stockData);
            $stockIn->save();
            $stockProductData = [];
            $operationData = [];
            //check sản phẩm tồn tại
            if (!empty($params['product_id'])) {
                foreach ($params['product_id'] as $key => $productId) {
                    if (!Product::where('id', $productId)->exists()) {
                        continue;
                    }
                    $productEntity = [
                        'stock_id'       => $stockIn->id,
                        'product_id'     => $productId,
                        'quantity'       => $params['product_quantity'][$key],
                        'expired_date'   => !empty($params['product_expired_date'][$key]) ? Carbon::createFromFormat(config('app.date_format'), $params['product_expired_date'][$key])->format('Y-m-d') : null,
                        'unit_id'        => $params['product_unit_id'][$key],
                        'unit_name'      => $params['product_unit_name'][$key],
                        'price'          => $params['product_price'][$key],
                        'total'          => $params['product_total'][$key],
                        'stock_group_id' => $params['product_stock_group_id'][$key],
                        'type'           => STOCK_IN,
                    ];
                    array_push($stockProductData, $productEntity);
                    $before_entity = Operation::where('product_id', $productId)->latest()->first();
                    $before_quantity = 0;
                    if ($before_entity) {
                        $before_quantity = $before_entity->before_quantity + $before_entity->quantity;
                    }
                    array_push($operationData, [
                        'stock_id'        => $stockIn->id,
                        'product_id'      => $productId,
                        'quantity'        => $params['product_quantity'][$key],
                        'expired_date'    => !empty($params['product_expired_date'][$key]) ? Carbon::createFromFormat(config('app.date_format'), $params['product_expired_date'][$key])->format('Y-m-d') : null,
                        'unit_id'         => $params['product_unit_id'][$key],
                        'unit_name'       => $params['product_unit_name'][$key],
                        'price'           => $params['product_price'][$key],
                        'total'           => $params['product_total'][$key],
                        'stock_group_id'  => $params['product_stock_group_id'][$key],
                        'type'            => STOCK_IN,
                        'created_at'      => date('Y-m-d H:i:s'),
                        'before_quantity' => $before_quantity
                    ]);
                    $rules = [
                        'quantity' => 'numeric|min:1',
                        'expired_date' => 'date|nullable',
                        'unit_id' => 'exists:product_units,id|nullable',
                        'price' => 'numeric|min:0',
                        'total' => 'numeric|min:0',
                        'stock_group_id' => 'exists:stock_groups,id|nullable',
                    ];
                    $messages = [];
                    $row = $key + 1;
                    $validator = Validator::make($productEntity, $rules, $messages);
                    if ($validator->fails()) {
                        $errors = $validator->errors()->messages();
                        if (!empty($errors)) {
                            return response(json_encode(['status' => 'NG', 'row' => $row, 'message' => array_values($errors)]), HTTP_STATUS_SUCCESS);
                        }
                    }

                }
            }
            StockProduct::where("stock_id",$stockIn->id)->where('type',STOCK_IN)->delete();
            StockProduct::insert($stockProductData);
            Operation::insert($operationData);
            DB::commit();
            return $this->statusOK();

        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->responseWithErrorMessage($ex->getMessage());
        }
        return $this->statusOK();
    }



    /**
     * getProduct
     *

     */
    public function getProduct(Request $request)
    {
        $params = $request->only(['code', 'name']);
        if (empty($params)) {
            return $this->statusNG();
        }
        $products = Product::select("products.*", "product_units.name as unit_name")
            ->leftJoin('product_units', 'product_units.id', '=', 'products.unit_id')
            ->where(function ($query) use ($params) {
                if (isset($params['code'])) {
                    $query->where('code', '=', $params['code']);
                }
                if (isset($params['name'])) {
                    $query->where('name', 'like', $params['name'] . "%");
                }
            })->where('products.shop_id', getCurrentUser()->shop_id)->get();
        return response(json_encode(['status' => "OK", 'data' => $products]), HTTP_STATUS_SUCCESS);

    }

    /**
     * delete stock in
     *

     */
    public function delete(Request $request)
    {
        $arrStockId = $request->only(['id']);
        if (empty($arrStockId)) {
            return $this->statusNG();
        }
        try {

            DB::beginTransaction();
            StockProduct::whereIn("stock_id",$arrStockId['id'])->where('type',STOCK_IN)->delete();
            StockIn::whereIn("id",$arrStockId['id'])->delete();
            DB::commit();
            return $this->statusOK();

        } catch (\Exception $ex) {
            DB::rollBack();
            return $this->responseWithErrorMessage($ex->getMessage());

        }
        return $this->statusOK();
    }
    /**
     * inport excel file
     *

     */
    public function inportExcel(Request $request)
    {
        if($request->hasFile('file')){
            $path = $request->file('file')->getRealPath();
            $data = \Excel::load($path)->get();
            $headerRow = $data->first()->keys()->toArray();
            $countRows = $data->count();
            if($countRows > LIMIT_COUNT_ROWS_IMPORT){
                $messages = 'Nhập tối đa '.LIMIT_COUNT_ROWS_IMPORT.' sản phẩm';
                return response(json_encode(['status' => 'NG_COUNT', 'message' => $messages]), HTTP_STATUS_SUCCESS);
            }
            if($headerRow !== Excel_IMPORT_FORMAT){
                return $this->statusNG();
            }
            if($data->count()){
                $products= [];
                foreach ($data as $key => $value) {
                    $checkProduct = Product::query()->currentShop()->where(['code'=> $value->ma_san_pham,'name'=>$value->ten_san_pham])->first();
                    $checkUnitName = ProductUnit::where(['name'=>$value->don_vi])->first();
                    // dd($checkProduct);
                    $row = $key + 1;
                    $messages = '';
                    if (!$checkProduct) {
                        continue;
                        // $messages = 'Mã sản phẩm hoặc tên sản phẩm không chính xác';
                        // return response(json_encode(['status' => 'NG', 'row' => $row, 'message' => $messages]), HTTP_STATUS_SUCCESS);
                    }
                    if (!$checkUnitName) {
                        $newProductUnit = new ProductUnit();
                        $newProductUnit->name = $value->don_vi;
                        $newProductUnit->save();
                    }
                    $product = [
                        'product_code' => $value->ma_san_pham,
                        'product_id' => $checkProduct ? $checkProduct->id : '',
                        'product_name' => $value->ten_san_pham,
                        'price' => $value->gia,
                        'color' => $value->mau,
                        'size' => $value->size,
                        'type' => $value->loai,
                        'unit_name' => $value->don_vi,
                        'unit_id'=>$checkUnitName? $checkUnitName->id:'',
                        'quantity' => $value->so_luong,
                    ];
                    array_push($products, $product);
                }

                $stockGroups = StockGroup::pluck('name', 'id')->toArray();
                $returnHTML = view('admin.stock.template_stockin_import',['stockGroups'=>$stockGroups,'products'=> $products])->render();
                // dd($returnHTML)
                return response(json_encode(['status' => 'SUCCESS', 'html' => $returnHTML]), HTTP_STATUS_SUCCESS);
            }
        }
    }
    //view
    public function view($id){
        $entity = StockIn::findOrFail($id);
        $stockProducts = StockProduct::with('product')->where(["stock_id" => $id, 'type' => STOCK_IN ])->get();
        $user = Auth::guard('users')->user();
        $type = STOCK_IN;
        $shop = Shop::query()->whereKey($user->shop_id)->first();
        return view('admin.stock.view_stock_out')->with(compact('entity','stockProducts','user','type','shop'));
    }
}
