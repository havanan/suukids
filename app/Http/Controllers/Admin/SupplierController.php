<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\Province;
use DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = Supplier::all();
        $provinces = Province::all();
        return view('admin.stock.stock_out_supplier',['supplier'=>$supplier,'provinces'=>$provinces]);
    }

    /**
     * Show the form for creating a new resource.
     *@param $params
     * @return array
     */
    public function create($params)
    {
        $supplier = [];
        if (empty($params)){
            return $supplier;
        }

        foreach ($params as $item){
            $data['code'] = $item['code'];
            $data['name'] = $item['name'];
            $data['phone'] = $item['phone'];
            $data['address'] = $item['address'];
            $data['prefecture'] = $item['prefecture'];
            $data['shop_id'] = getCurrentUser()->shop_id;
            $supplier[] = $data;
        }
        
        if (!empty($supplier)){
            Supplier::insert($supplier);
        }
        return $supplier;
    }
    /**
     * Update the specified resource in storage.
     * @param  $params
     * @return array
     */
    public function update($params)
    {
        if (empty($params)){
            return;
        }
        
        foreach ($params as $item){
            if($item['id']){
                $updateSupplier = Supplier::findOrFail($item['id']);
                $updateSupplier->code = $item['code'];
                $updateSupplier->name = $item['name'];
                $updateSupplier->phone = $item['phone'];
                $updateSupplier->address = $item['address'];
                $updateSupplier->prefecture = $item['prefecture'];
                $updateSupplier->save();
            }
        }
    }
    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $update = $request->get('data');
        $code = [];
        if (!empty($update)) {
            $code = array_map(function($data){
                return $data['code'];
            }, $update);
        }
        $new = $request->get('new');
        $codeNew = [];
        if (!empty($new)) {
            $code += array_map(function($data){
                return $data['code'];
            }, $new);
        }
        
        if(count($code) != count(array_unique($code))){
            $mess = 'Mã nhà cung cấp không được trùng.Vui lòng kiểm tra lại';
            return response(json_encode(['status' => 'NG', 'message' => $mess]), HTTP_STATUS_SUCCESS);
        }
        
        $remove = $request->get('removeSupplier');
        DB::beginTransaction();
        try {
        //cập nhật
        $this->update($update);
        //tạo mới
        $this->create($new);
        //xoa
        $this->delete($remove);
            DB::commit();
            return $this->statusOK();
        } catch (\Exception $e) {
            // something went wrong
            DB::rollback();
            return $this->responseWithErrorMessage($ex->getMessage());
        }

    }
    /**
     * Delete resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function delete($remove){
        if($remove){
            $removeId = explode(",", $remove);
            Supplier::whereIn('id', $removeId)->delete();
        }
        return back()->with('success','Dữ liệu đã được update');

    }
}
