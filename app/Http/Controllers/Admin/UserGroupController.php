<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\UserGroup;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserGroupController extends Controller
{
   public function index(){
       $data = UserGroup::orderBy('id')->get();
       $users = User::currentShop()->orderBy('name')
//           ->where('shop_manager_flag',ACTIVE)
           ->where('status',ACTIVE)
           ->where('shop_id',getCurrentUser()->shop_id)
           ->get();
       return view('admin.user_group.index',compact('data','users'));
   }
   public function save(Request $request){
       $data = $request->get('data');
       $new = $request->get('new');
       DB::beginTransaction();
       try {
            // cập nhật
           $ids = $this->update($data);
           // xóa
            $this->deleteAll($ids);
           // tạo mới
           $this->create($new);
           DB::commit();
           return  back()->with('success','Dữ liệu đã được update');
       } catch (\Exception $e) {
           // something went wrong
           DB::rollback();
           return  back()->with('error','Thao tác thất bại');
       }
   }
    /**
     * Show the form for creating a new resource.
     *@param $params
     * @return array
     */
    public function create($params)
    {
        $data = [];
        if (empty($params)){
            return $data;
        }
        $i = 0;
        foreach ($params as $item){
            if ($item['name'] != null){
                $data[$i]['name'] = $item['name'];
                $data[$i]['admin_user_id'] = $item['admin_user_id'];
                $data[$i]['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $data[$i]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
                $data[$i]['shop_id'] = getCurrentUser()->shop_id;
                $i++;
            }
        }
        if (!empty($data)){
            UserGroup::insert($data);
        }
        return $data;
    }
    /**
     * Update the specified resource in storage.
     * @param  $params
     * @return array
     */
    public function update($params)
    {

        $wtName = [];
        $results = [];
        if (empty($params)){
            return  $results;
        }
        foreach ($params as $key => $item){
            if (isset($item['id'])){
                $input = [
                    'name' => $item['name'],
                    'admin_user_id' => $item['admin_user_id'],
                ];
                UserGroup::where('id',$item['id'])->update($input);
                $results [$key] = $item['id'];
            }
        }
        // all good
        return  $results;

    }

    /**
     * Delete resource in storage.
     * @param  array $ids
     * @return array
     */
    public function deleteAll($ids){

        if (empty($ids)){
            return null;
        }
        $delete = UserGroup::whereNotIn('id',$ids)->delete();
        if ($delete){
            return $ids;
        }
        else{
            return  null;
        }

    }
}
