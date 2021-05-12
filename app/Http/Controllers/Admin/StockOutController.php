<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockGroup;
use App\Models\StockOut;
use App\Models\StockProduct;
use App\Models\Supplier;
use App\Models\ProductUnit;
use App\Models\Shop;
use Carbon\Carbon;
use App\Http\Requests\Admin\StockOutRequest;
use DB;
use Validator;
use Auth;

class StockOutController extends Controller
{
    /**
     * index
     *

     */
    public function index(Request $request)
    {
        $stockOut = StockOut::all();
        $suppliers = Supplier::all();
        $stockGroups = StockGroup::all();
        $dataSearch = [];
        if($request->isMethod('post')){
            $dataSearch = $request->all();
            $builder = StockOut::query();
            $builder = $builder->leftJoin("stock_products",function($join){
                $join->on("stock_products.stock_id","=","stock_out.id")
                    ->where("stock_products.type","=",STOCK_OUT);
            });
            $builder = $builder->select('stock_out.*');
            if(!empty($request->get('bill_number'))){
                $billNumber = $request->get('bill_number');
                $builder = $builder->where('stock_out.bill_number', 'LIKE', "%$billNumber%");
            }
            if(!empty($request->get('note'))){
                $note = $request->get('note');
                $builder = $builder->where('stock_out.note', 'LIKE', "%$note%");
            }
            if(!empty($request->get('create_date_from'))){
                $createDateFrom = Carbon::createFromFormat(config('app.date_format'), $request->get('create_date_from'))->format('Y-m-d');
                $builder = $builder->whereDate('stock_out.create_day', '>=', $createDateFrom);
            }
            if(!empty($request->get('create_date_to'))){
                $createDateTo = Carbon::createFromFormat(config('app.date_format'), $request->get('create_date_to'))->format('Y-m-d');
                $builder = $builder->where('stock_out.create_day', '<=', $createDateTo);
            }
            if(!empty($request->get('supplier_id'))){
                $supplierId = $request->get('supplier_id');
                $builder = $builder->where('stock_out.supplier_id', '=', "$supplierId");
            }
            if(!empty($request->get('warehouse_id'))){
                $warehouseId = $request->get('warehouse_id');
                $builder = $builder->where('stock_products.stock_group_id', '=', "$warehouseId");
            }
            $stockOut = $builder->orderBy('create_day','desc')->get();
        }
        return view('admin.stock.stock_out_list',['stockOut'=>$stockOut,'suppliers'=>$suppliers,'stockGroups'=>$stockGroups,'dataSearch'=>$dataSearch]);
    }

    /**
     * create
     *

     */
    public function create()
    {
        $billNumber = intval(StockOut::max("id")) + 1;
        $billNumber = $billNumber < 10 ? "PX0" . $billNumber : "PX" . $billNumber;
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $stockGroups = StockGroup::pluck('name', 'id')->toArray();
        if(empty($stockGroups)){
            $stockGroups = [
                '' => 'Chưa thêm kho'
            ];
        }
        $entity = new StockOut();
        $type = STOCK_OUT_PRODUCT;
        return view('admin.stock.stock_out_create')->with(compact('billNumber', 'suppliers', 'stockGroups', 'entity','type'));
    }
    /**
     * edit
     *

     */
    public function edit($id = null)
    {
        $entity = StockOut::findOrFail($id);
        $billNumber = $entity->bill_number;
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $stockGroups = StockGroup::pluck('name', 'id')->toArray();
        if(empty($stockGroups)){
            $stockGroups = [
                '' => 'Chưa thêm kho'
            ];
        }
        // dd($entity->internal_export);
        $type = $entity->internal_export == INTERNAL_EXPORT ? MOVE_PRODUCT : STOCK_OUT_PRODUCT;

        $stockProducts = StockProduct::with('product')->where(["stock_id" => $id, 'type' => STOCK_OUT])->get();
        return view('admin.stock.stock_out_create')->with(compact('billNumber', 'suppliers', 'stockGroups', 'entity','stockProducts','type'));
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
            $validator = new StockOutRequest($stockData,$request->get('id'));
            $validatorStatus = $validator->validate();
            $type = $request->type;
            $stockData['internal_export'] = $type == MOVE_PRODUCT ? INTERNAL_EXPORT:NORMAL_EXPORT;
            $totalWarehouse = StockGroup::where('is_default','=',ACTIVE)->first();
            if ($validatorStatus !== true) {
                return $this->responseWithErrorMessage($validatorStatus);
            }
            DB::beginTransaction();

            if(empty($request->get('id'))){
                $stockOut = new StockOut();
            } else{
                $stockOut = StockOut::findOrFail($request->get('id'));
            }
            // dd($params);
            $stockOut->fill($stockData);
            $stockOut->save();
            $stockProductData = [];
            if (!empty($params['product_id'])) {
                foreach ($params['product_id'] as $key => $productId) {
                    if (!Product::where('id', $productId)->exists()) {
                        continue;
                    }

                    $productEntity = [
                        'stock_id' => $stockOut->id,
                        'product_id' => $productId,
                        'quantity' => $params['product_quantity'][$key],
                        'unit_id' => $params['product_unit_id'][$key],
                        'unit_name' => $params['product_unit_name'][$key],
                        'price' => $params['product_price'][$key],
                        'total' => $params['product_total'][$key],
                        'stock_group_id' => $params['product_stock_group_id'][$key],
                        'to_stock_group_id' => isset($params['product_to_stock_group_id'][$key]) ? $params['product_to_stock_group_id'][$key] : null,
                        'type' => STOCK_OUT,
                    ];
                    array_push($stockProductData, $productEntity);
                    $rules = [
                        'quantity' => 'numeric|min:1',
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
                    // dd($type);
                    if($type == MOVE_PRODUCT && !$params['product_to_stock_group_id'][$key]){
                        $mess= 'Bạn chưa chọn kho xuât';
                        return response(json_encode(['status' => 'NG', 'row' => $row, 'message' => $mess]), HTTP_STATUS_SUCCESS);
                    }elseif($type == MOVE_PRODUCT && $totalWarehouse->id == $params['product_to_stock_group_id'][$key]){
                        $mess= 'Kho xuất phải khác kho tổng';
                        return response(json_encode(['status' => 'NG', 'row' => $row, 'message' => $mess]), HTTP_STATUS_SUCCESS);
                    }
                }
            }
            StockProduct::where("stock_id",$stockOut->id)->where('type',STOCK_OUT)->delete();
            StockProduct::insert($stockProductData);
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
     * delete stock out
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
            StockProduct::whereIn("stock_id",$arrStockId['id'])->where('type',STOCK_OUT)->delete();
            StockOut::whereIn("id",$arrStockId['id'])->delete();
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
                    $checkProduct = Product::query()->currentShop()->select('stock_products.product_id')
                                        ->join('stock_products', 'stock_products.product_id', '=', 'products.id')
                                        ->where(['code'=> $value->ma_san_pham,'name'=>$value->ten_san_pham,'type'=>STOCK_IN])
                                        ->first();
                    $checkUnitName = ProductUnit::where(['name'=>$value->don_vi])->first();
                    $row = $key + 1;
                    $messages = '';
                    // dd($checkProduct);
                    if (!$checkProduct) {
                        continue;
                    }
                    if (!$checkUnitName) {
                        $newProductUnit = new ProductUnit();
                        $newProductUnit->name = $value->don_vi;
                        $newProductUnit->save();
                    }
                    $product = [
                        'product_code' => $value->ma_san_pham,
                        'product_id' => $checkProduct ? $checkProduct->product_id : '',
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
                $type = $request->type;
                $stockGroups = StockGroup::pluck('name', 'id')->toArray();
                $returnHTML = view('admin.stock.template_stockout_import',['stockGroups'=>$stockGroups,'products'=> $products,'type'=>$type])->render();
                return response(json_encode(['status' => 'SUCCESS', 'html' => $returnHTML]), HTTP_STATUS_SUCCESS);
            }
        }
    }

    //xuất nội bộ
    public function moveProduct(){
        $billNumber = intval(StockOut::max("id")) + 1;
        $billNumber = $billNumber < 10 ? "PX0" . $billNumber : "PX" . $billNumber;
        $suppliers = Supplier::pluck('name', 'id')->toArray();
        $stockGroups = StockGroup::pluck('name', 'id')->toArray();
        if(empty($stockGroups)){
            $stockGroups = [
                '' => 'Chưa thêm kho'
            ];
        }
        $entity = new StockOut();
        $type = MOVE_PRODUCT;
        return view('admin.stock.stock_out_create')->with(compact('billNumber', 'suppliers', 'stockGroups', 'entity','type'));
    }

    //view
    public function view($id){
        $entity = StockOut::findOrFail($id);
        $shop = Shop::query()->whereKey(getCurrentUser()->shop_id)->first();
        $stockProducts = StockProduct::with('product')->where(["stock_id" => $id, 'type' => STOCK_OUT])->get();
        $user = Auth::guard('users')->user();
        $type = STOCK_OUT;
        return view('admin.stock.view_stock_out')->with(compact('entity','stockProducts','user','type','shop'));
    }
}
