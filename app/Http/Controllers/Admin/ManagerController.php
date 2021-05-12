<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use App\Models\DeliveryMethod;
use App\Models\Shop;
use DB;
use Validator;

class ManagerController extends Controller
{
    //
    public  function status(Request $request){
        $listStatus = OrderStatus::query()->currentShop()->orderBy('position','asc')->get();
        $statusSystem = OrderStatus::query()->where('is_default',ACTIVE)->orwhere('is_customize',ACTIVE)->currentShop()->get();
        if($request->isMethod('post')){
            try {
                $data = $request->all();
                // dd($data);
                DB::beginTransaction();
                foreach($data['edit'] as $id => $oldStatus){
                    $statusData = OrderStatus::find($id);
                    if(empty($oldStatus['name'])){
                        return response(json_encode(['status' => 'NG', 'message' => 'Vui lòng nhập tên trạng thái!']), HTTP_STATUS_SUCCESS);
                    }
                    $statusData->name = $oldStatus['name'];
                    $statusData->position = $oldStatus['position'];
                    $statusData->color = $oldStatus['color'];
                    $statusData->level = $oldStatus['level'];
                    $statusData->no_revenue_flag = $oldStatus['no_revenue_flag'];
                    $statusData->no_reach_flag = $oldStatus['no_reach_flag'];
                    $statusData->save();
                }
                if(isset($data['new']) && !empty($data['new'])){
                    foreach($data['new'] as $index => $newStatus){
                        if(empty($newStatus['name'])){
                            return response(json_encode(['status' => 'NG', 'message' => 'Vui lòng nhập tên trạng thái!']), HTTP_STATUS_SUCCESS);
                        }
                        $statusData = new OrderStatus;
                        $statusData->name = $newStatus['name'];
                        $statusData->no_revenue_flag = isset($newStatus['no_revenue_flag']) ? $newStatus['no_revenue_flag'] : 0;
                        $statusData->no_reach_flag = isset($newStatus['no_reach_flag']) ? $newStatus['no_reach_flag'] : 0;
                        $statusData->is_customize = ACTIVE;
                        $statusData->shop_id = getCurrentUser()->shop_id;
                        $statusData->save();
                    }
                }
                if(!empty($data['removeStatus'])){
                    $removeId = explode(",", $data['removeStatus']);
                    OrderStatus::where('is_customize',ACTIVE)->whereIn('id', $removeId)->delete();
                }
                DB::commit();
                return $this->statusOK();
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->responseWithErrorMessage($ex->getMessage());
            }

        }
        return view('admin.manager.status',['listStatus'=>$listStatus,'statusSystem'=>$statusSystem]);
    }

    public function delivery(Request $request){
        $listDelivery = DeliveryMethod::query()->currentShop()->get();
        if($request->isMethod('post')){
            $data = $request->all();
            //update
            if(!empty($data['oldDelivery'])){
                foreach($data['oldDelivery'] as $oldDelivery){
                    //format id:naem
                    $delivery = explode(":", $oldDelivery);
                    $deliveryData = DeliveryMethod::find($delivery[0]);
                    $deliveryData->name = $delivery[1];
                    $deliveryData->save();
                }
            }
            //create
            if(!empty($data['name'])){
                foreach($data['name'] as $name){
                    $deliveryData = new DeliveryMethod;
                    $deliveryData->name = $name;
                    $deliveryData->shop_id = getCurrentUser()->shop_id;
                    $deliveryData->save();
                }
            }
            //delete
            if(!empty($data['removeDelivery'])){
                $removeId = explode(",", $data['removeDelivery']);
                DeliveryMethod::whereIn('id', $removeId)->delete();
            }
            return back()->with('success','Dữ liệu đã được update');
        }
        return view('admin.manager.delivery',['listDelivery'=>$listDelivery]);
    }

    public function shopInfo(Request $request){
        $shop = Shop::findOrFail(getCurrentUser()->shop_id);
        if($request->isMethod('post')){
            try {
                $data = $request->only('id','name','phone','address');
                DB::beginTransaction();
                $rules = [
                    'name' => 'required',
                    'address' => 'required',
                    'phone' => 'required',
                ];
                $messages = [
                    'name.required' => 'Vui lòng nhập tên shop!',
                    'address.required' => 'Vui lòng nhập địa chỉ shop!',
                    'phone.required' => 'Vui lòng nhập số điện thoại shop!',
                ];
                $validator = Validator::make($data, $rules, $messages);
                if ($validator->fails()) {
                    $errors = $validator->errors()->messages();
                    if (!empty($errors)) {
                        return response(json_encode(['status' => 'NG','message' => array_values($errors)]), HTTP_STATUS_SUCCESS);
                    }
                }
                $shopInfo = Shop::find($data['id']);
                unset($data['id']);
                $shopInfo->fill($data);
                $shopInfo->save();

                DB::commit();
                return $this->statusOK();
            } catch (\Exception $ex) {
                DB::rollBack();
                return $this->responseWithErrorMessage($ex->getMessage());
            }
        }
        return view('admin.manager.shop',['shop'=>$shop]);
    }
}
