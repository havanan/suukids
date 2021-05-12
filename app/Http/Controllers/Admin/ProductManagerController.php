<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use App\Models\ProductBundle;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductManagerRequest;

class ProductManagerController extends Controller
{
    //
    public function index(Request $request){
        $type = $request->has('type') && $request->type == 1 ? STOP_BUSINESS : BUSINESS;
        $builder = Product::query();

        $builder->select('products.*',DB::raw('COUNT(order_products.id) count_order'));
        $builder->leftJoin('order_products', function ($join) {
            $join->whereRaw('order_products.product_id = products.id');
        });

        $builder->where('products.status', '=', "$type");
        $builder->where('products.shop_id', '=',getCurrentUser()->shop_id);
        $builder->whereNull('products.deleted_at');
        if(!empty($request->get('search'))){
            $search = $request->get('search');
            $builder->where('products.name', 'LIKE', "%$search%");
            $builder->orWhere('products.code', 'LIKE', "%$search%");
            $builder->orWhere('products.price', 'LIKE', "%$search%");
            $builder->orWhere('products.cost_price', 'LIKE', "%$search%");
        }

        $productList = $builder->orderBy('products.created_at','desc')->groupBy('products.id')->paginate(15);

        $productUnit = ProductUnit::pluck('name', 'id')->toArray();
        $productBundle = ProductBundle::pluck('name', 'id')->toArray();
        // dd($productList);
        return view('admin.product.manager_product')->with(compact('productUnit','productBundle','productList'));
    }

    public function delete(Request $request){
        $id = $request->get('id');
        $info = Product::findOrFail($id);
        if($info->orders->count() > 0){
            return  'false';
        }
        $now = \Carbon\Carbon::now();
        $info->deleted_at = $now->toDateTimeString();
        if ($info->save()){
            return 'true';
        }
        else{
            return  'false';
        }
    }

    public function store(Request $request){
        DB::beginTransaction();
        try {
            $postData = $request->all();
            //cập nhật
            $update = true;
            $create = true;
            if (isset($postData['edit'])) {
                $update = $this->update($postData['edit']);
            }
            //tạo mới
            if (isset($postData['new'])) {
                $create = $this->create($postData['new']);
            }
            if($update && $create){
                DB::commit();
                return redirect()->route('admin.manager.products')->with('success','Sửa sản phẩm thành công');
            }

        } catch (\Exception $ex) {
            DB::rollBack();
            return  back()->with('error','Thao tác thất bại');

        }
        return  back()->with('error','Thao tác thất bại');
    }
    /**
     * Update the specified resource in storage.
     * @param  $params
     * @return array
     */
    public function create($param){
        $path = 'product';
        $productNew = [];
        foreach ($param as $key => $product) {
            $validator = new ProductManagerRequest($product,null);
            $validatorStatus = $validator->validate();

            if ($validatorStatus !== true) {
                return false;
            }
            if(isset($product['product_image'])){
                $product_image = $this->uploadImage($product['product_image'],$path);
                $product['product_image'] = $product_image;
                $product['shop_id'] = getCurrentUser()->shop_id;
            }
            $product['on_hand'] = 0;
            $productNew[] = $product;
        }
        if(Product::insert($productNew)){
            return true;
        }
        return false;
    }
    /**
     * Update the specified resource in storage.
     * @param  $params
     * @return array
     */
    public function update($param){
        $path = 'product';
        $delOldImg = [];
        foreach ($param as $id => $product) {
            $validator = new ProductManagerRequest($product,$id);
            $validatorStatus = $validator->validate();

            if ($validatorStatus !== true) {
                return false;
            }
            $productEdit = Product::findOrFail($id);
            if(isset($product['product_image'])){
                $delOldImg[] = $productEdit->product_image;
                $product_image = $this->uploadImage($product['product_image'],$path);
                $product['product_image'] = $product_image;

            }

            $product['status'] = isset($product['status']) ? STOP_BUSINESS : BUSINESS;
            $product['on_hand'] = 0;
            $productEdit->fill($product);
            if(!$productEdit->save()){
                return false;
            }
        }
        //remove image old
        if(!empty($delOldImg)){
            foreach ($delOldImg as $key => $imgOld) {
                $this->deleteFile($imgOld,$path);
            }
        }
        return true;
    }

    public function validateBeforeSave(Request $request){
        $postData = $request->all();
        $newData = $request->new ? $request->new : [];
        $updateData = $request->edit ? $request->edit : [] ;
        $data = array_merge($updateData, $newData);
        if (!empty($data)) {
            $row = 0;
            foreach ($data as $value) {
                // dd($value);
                $id = isset($value['id']) ? $value['id'] : '';
                $row += 1;
                $validator = new ProductManagerRequest($value,$id);
                $validatorStatus = $validator->validate();
                if ($validatorStatus !== true) {
                    $message = $validatorStatus[array_keys($validatorStatus)[0]][0];
                    return response(json_encode(['status' => 'NG', 'row' => $row, 'message' => $message]), HTTP_STATUS_SUCCESS);
                }
            }
        }
        return $this->statusOK();
    }

    public function exportExcel(Request $request)
    {
        $type = $request->has('type') && $request->type == 1 ? STOP_BUSINESS : BUSINESS;
        $user_name = Auth::user()->account_id;
        $file_name = 'product_'.Carbon::now()->timestamp.'_'.$user_name.'_emails';
        $i = 1;
        \Excel::create($file_name, function($excel) use ($type) {
             Product::where('status','=',$type)->whereNull('deleted_at')->orderBy('id','desc')
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
                               'Giá'=>$user->price,
                               'Mầu sắc' => $user->color,
                               'Size' => $user->size,
                               'Rộng' => 0,
                               'Cao' => 0,
                               'Kg' => $user->weight,
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

        })->export('xlsx');
    return back();
    }
}
