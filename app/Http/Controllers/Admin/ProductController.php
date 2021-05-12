<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Common;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductUnit;
use App\Models\StockIn;
use App\Models\StockProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExportLogHelper;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $units = ProductUnit::orderBy('id','desc')->pluck('name','id');
        $bundles = ProductBundle::orderBy('id','desc')->pluck('name','id');
        return view('admin.product.index',compact('units','bundles'));
    }

    public function getList(Request $request){

        $params= $request->all();
        $data = $this->getData($params);
        return $data;
    }
    public function getData($params){
        $paginate = Common::toPagination($params);
        $data = Product::orderBy($paginate['sort'], $paginate['order'])
            ->leftJoin('product_units','product_units.id','products.unit_id')
            ->leftJoin('product_bundles','product_bundles.id','products.bundle_id')
            ->select('products.*','product_units.name as product_unit','product_bundles.name as product_bundle');
        if (isset($params['keyword'])){
            $data = $data->where(function ($query) use ($params){
                $query->where('products.name','like','%'.$params['keyword'].'%');
                $query->orWhere('products.code','like','%'.$params['keyword'].'%');
            });
        }
        if (isset($params['unit_id'])){
            $data = $data->where('products.unit_id',$params['unit_id']);
        }
        if (isset($params['bundle_id'])){
            $data = $data->where('products.bundle_id',$params['bundle_id']);
        }
        if (isset($params['status'])){
            $data = $data->where('products.status',$params['status']);
        }
        $data = $data->where('products.shop_id', getCurrentUser()->shop_id)->paginate($paginate['limit']);

        $data = Common::toJson($data);
        return $data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $units = ProductUnit::orderBy('name')->get();
        $bundles = ProductBundle::orderBy('name')->get();

        $warning_days = !empty(old('warning_days')) ? old('warning_days') :  3 ;

        return view('admin.product.create',compact('units','bundles', 'warning_days'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $avatar = $request->file('product_image');
        $params = $request->all();
        $path = 'product';
        $product_image = null;
        unset($params['_token'],$params['product_image']);

        if ($avatar){
            // Upload ảnh mới
            $product_image = $this->uploadImage($avatar,$path);
            $params['product_image'] = $product_image;
        }
        $params['code'] = htmlspecialchars($request->get('code'));
        $params['name'] = htmlspecialchars($request->get('name'));
        $params['color'] = htmlspecialchars($request->get('color'));
        $params['size'] = htmlspecialchars($request->get('size'));
        $params['price'] = $this->formatCurrency($params['price']);
        $params['cost_price'] = $this->formatCurrency($params['cost_price']);
        $params['shop_id'] = getCurrentUser()->shop_id;
        DB::beginTransaction();
        try {
            $poduct = Product::create($params);
            // Tạo phiếu nhập kho
            $params['product_id'] = $poduct->id;
            $stock_in = $this->createStockIn($params);

            $params['stock_id'] = $stock_in->id;
            // tạo liên kết sản phẩm vs bill
            if (isset($params['product_id'])){
                $this->createStockProduct($params);
            }
            DB::commit();
            return redirect()->route('admin.product.index')->with('success','Nhập sản phẩm thành công');
        }catch (\Exception $e) {
            $this->deleteImageWithPath($product_image);
            // something went wrong
            DB::rollback();
            return  back()->with('error','Thao tác thất bại');
        }

    }
    public function formatCurrency($number){
        return floatval(str_replace(',','',$number));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $info = Product::findOrFail($id);
        $units = ProductUnit::orderBy('name')->get();
        $bundles = ProductBundle::orderBy('name')->get();
        $warning_days = 3;
        if (!empty($info->warning_days)) {
            $warning_days = $info->warning_days;
        } else if (!empty(old('warning_days'))) {
            $warning_days = old('warning_days');
        }
        return view('admin.product.create',compact('units','bundles','info', 'warning_days'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $info = Product::findOrFail($id);
        $avatar = $request->file('product_image');
        $params = $request->all();
        $path = 'product';
        $product_image = null;
        unset($params['_token'],$params['_method'],$params['product_image']);
        if ($avatar){
            // Upload ảnh mới
            $product_image = $this->uploadImage($avatar,$path);
            $params['product_image'] = $product_image;
        }
        $params['code'] = htmlspecialchars($request->get('code'));
        $params['name'] = htmlspecialchars($request->get('name'));
        $params['color'] = htmlspecialchars($request->get('color'));
        $params['size'] = htmlspecialchars($request->get('size'));
        $params['price'] = $this->formatCurrency($params['price']);
        $params['cost_price'] = $this->formatCurrency($params['cost_price']);
        $params['shop_id'] = getCurrentUser()->shop_id;
        DB::beginTransaction();
        try {
            Product::where('id',$id)->update($params);
            DB::commit();
            // Xóa ảnh cũ
            $old_product_image = $info->product_image;
            if ($old_product_image != null){
                $this->deleteFile($old_product_image,$path);
            }
            return redirect()->route('admin.product.index')->with('success','Sửa sản phẩm thành công');
        }catch (\Exception $e) {
            $this->deleteImageWithPath($product_image);
            // something went wrong
            DB::rollback();
            return  back()->with('error','Thao tác thất bại');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $info = Product::findOrFail($id);
        if ($info->delete()){
            $product_image = $info->product_image;
            $this->deleteImageWithPath($product_image);
            return redirect()->route('admin.product.index')->with('success','Xóa sản phẩm thành công');
        }else{
            return  back()->with('error','Xóa thất bại');
        }

    }
    public function bundle(){
        return view('admin.product.bundle');

    }
    public function unit(){
        return view('admin.product.unit');
    }

    public function createStockIn($params){
        if (empty($params)){
            return null;
        }
        $maxId = StockIn::max('id');
        $maxId += 1;
        $input = [
            'create_day' => Carbon::now()->format('Y-m-d'),
            'note' => 'Nhập kho khi thêm sản phẩm mới',
            'total' => $params['cost_price'],
            'bill_number' => 'PN'.$maxId
        ];
        $stock_in = StockIn::create($input);

        return $stock_in;
    }
    public function createMultiStockProduct($products,$stock_id){

        if (count($products) <= 0){
            return null;
        }

        $input = [];
        foreach ($products as $key => $product){
            $input[$key]['stock_id'] = $stock_id;
            $input[$key]['product_id'] = $product->id;
            $input[$key]['type'] = STOCK_IN;
            $input[$key]['price'] = $product->cost_price;
            $input[$key]['total'] = $product->cost_price*$product->on_hand;
            $input[$key]['quantity'] =  $product->on_hand;
            $input[$key]['shop_id'] = getCurrentUser()->shop_id;
        }
        return StockProduct::insert($input);
    }
    public function createStockProduct($params){
        $unitInfo = ProductUnit::find($params['unit_id']);

        $input = [
            'stock_id' => $params['stock_id'],
            'product_id' => $params['product_id'],
            'type' => STOCK_IN,
            'price' => $params['cost_price'],
            'total' => $params['cost_price']*$params['on_hand'],
            'quantity' => (int) $params['on_hand'],
            'unit_id' => (int) $params['unit_id'],
            'unit_name' => isset($unitInfo->name) ? $unitInfo->name : null,
            'stock_group_id' => DEFAULT_STOCK_GROUP
        ];
       $create = StockProduct::create($input);
       return $create;
    }
    public function importExcel(Request $request)
    {
        if($request->hasFile('excel_file')){
            $path = $request->file('excel_file')->getRealPath();
            $data = \Excel::load($path)->get();
            $headerRow = $data->first()->keys()->toArray();
            $countRows = $data->count();
            if($countRows > LIMIT_COUNT_ROWS_IMPORT){
                $messages = 'Nhập tối đa '.LIMIT_COUNT_ROWS_IMPORT.' sản phẩm';
                return back()->with('error',$messages);
            }
            if($headerRow !== Excel_IMPORT_FORMAT){
                return back()->with('error','File nhập vào sai định dạng');
            }

            if($data->count() <= 0){
                return back()->with('error','File nhập vào không có dữ liệu');
            }
            // bóc tách data từ file excel
            $response = $this->getItemExcel($data);
            if ($response == null){
                return redirect()->route('admin.product.index')->with('error','Nhập sản phẩm thất bại, Vui lòng kiểm tra lại các trường trong file vừa nhập');
            }
            $products= $response['products'];
            $total_price = $response['total_price'];
            DB::beginTransaction();
            try {
                // Tạo đơn vị sp
                $create_units = $this->createMultiUnit($response['units']);
                // Tạo loại sp
                $create_bundles = $this->createMultiBundle($response['bundles']);

                foreach ($products as $key => $item){
                    $products[$key]['unit_id'] = $this->getIdOfGroup($item['unit_id'],$create_units);
                    $products[$key]['bundle_id'] = $this->getIdOfGroup($item['bundle_id'],$create_bundles);
                    $products[$key]['shop_id'] = getCurrentUser()->shop_id;
                }
                if (empty($products)){
                    return back()->with('error','Nhập sản phẩm thất lỗi, Vui lòng thử lại');
                }
                Product::insert($products);
                $lastProduct = Product::query()->currentShop()->orderBy('id', 'desc')->take(count($products))->get();
                //Tạo hóa đơn nhập hàng
                $stockIn = $this->createStockIn(['cost_price' => $total_price]);
                //Tạo sp trong hóa đơn
                $stock_in_id = $stockIn->id;
                $this->createMultiStockProduct($lastProduct,$stock_in_id);
                DB::commit();
                return back()->with('success','Nhập sản phẩm thành công');
            }catch (\Exception $e) {
                // dd($e);
                // something went wrong
                DB::rollback();
                return back()->with('error','Nhập sản phẩm thất bại, Vui lòng thử lại');
            }
        }
    }
    public function exportExcel(Request $request)
    {

        ExportLogHelper::addLogExportExcel('Xuất excel từ danh sách sản phẩm', 'Đã xuất excel từ danh sách sản phẩm vào lúc '. Carbon::now(), url()->current(), $request->ip());
        $user_name = Auth::user()->account_id;
        $file_name = 'product_'.Carbon::now()->timestamp.'_'.$user_name.'_emails';
        $i = 1;
        $abc = [];
          \Excel::create($file_name, function($excel) {
             Product::orderBy('id','desc')
                ->leftJoin('product_units','product_units.id','products.unit_id')
                ->leftJoin('product_bundles','product_bundles.id','products.bundle_id')
                ->where('products.shop_id', getCurrentUser()->shop_id)
                ->select('products.id','products.name','code','price','cost_price','on_hand','color','size',
                    'product_units.name as product_unit','product_bundles.name as product_bundle')
                ->chunk(100,function ($users) use ($excel){
                // Tạo Sheet
                    if (count($users) > 0){
                        $data = [];
                        foreach ($users as $key => $user){
                           $input = [
                               'STT' => $key+1,
                               'Mã' => $user->code,
                               'Tên sản phẩm dịch vụ' => $user->name,
                               'Mầu sắc' => $user->color,
                               'Size' => $user->size,
                               'Rộng' => 0,
                               'Cao' => 0,
                               'Kg' => 0,
                               'Đơn vị' => $user->product_unit,
                               'Phân loại' => $user->product_bundle,
                           ];
                           array_push($data,$input);
                        }
                    }
                $excel->sheet('Worksheet', function($sheet) use ($data) {
                    $sheet->fromArray($data, null, 'A1', true);
                });
            });

        })->download('xls');

    return back();
    }
    public function getItemExcel($data){
        $response = [];
        $products= [];
        $units = [];
        $bundles = [];
        $total_price = 0;
        foreach ($data as $key => $value) {
            if ($value->ma_san_pham == null){
                return null;
            }
            // product input
            $cost_price = isset($value->gia) ? (int) $value->gia : 0;
            $product = [
                'name' => $value->ten_san_pham,
                'code' => $value->ma_san_pham,
                'price' => INACTIVE,
                'cost_price' => $cost_price,
                'on_hand' => $value->so_luong,
                'color' => $value->mau,
                'size' => $value->size,
                'unit_id' => $value->don_vi,
                'bundle_id' => $value->loai,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')

            ];
            array_push($products, $product);
            //unit
            $unit = $value->don_vi;
            array_push($units, $unit);
            //bundle
            $bundle = $value->loai;
            array_push($bundles, $bundle);
            $total_price += $cost_price;
        }
        // Bỏ trường thừa trong mảng
        $response['units'] = array_unique($units);
        $response['bundles'] = array_unique($bundles);
        $response['products'] = $products;
        $response['total_price'] = $total_price;
        return $response;
    }
    public function createMultiUnit($params){
        $data = [];
        if (count($params) <= 0){
            return $data;
        }
        $now = Carbon::now()->format('Y-m-d H:i:s');

        foreach ($params as $key => $item){
            if ($item != null){
                $input = [
                    'created_at' => $now,
                    'updated_at' => $now,
                    'name' => $item
                ];
                $match = [
                    'name' => $item
                ];
                $unit = ProductUnit::updateOrCreate($match,$input);
                $data[$unit->id] = $unit->name;
            }
        }
        return $data;
    }
    public function createMultiBundle($params){
        $data = [];
        if (count($params) <= 0){
            return $data;
        }
        $now = Carbon::now()->format('Y-m-d H:i:s');
        foreach ($params as $key => $item){
            if ($item != null){
                $input = [
                    'created_at' =>  $now,
                    'updated_at' => $now,
                    'name' => $item
                ];
                $match = [
                    'name' => $item
                ];
                $bundle = ProductBundle::updateOrCreate($match,$input);
                $data[$bundle->id] = $bundle->name;
            }
        }
        return $data;
    }
    public function getIdOfGroup($name,$group){
        $id = null;
        if (empty($group)){
            return $id;
        }
        foreach ($group as $key => $item){
            if ($item == $name){
                $id = $key;
            }
        }
        return $id;
    }
}
