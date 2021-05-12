<?php

namespace App\Http\Controllers\Admin;

use App\Models\OrderSource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class OrderSourceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = OrderSource::query()->currentShop()->get();
        return view('admin.order_source.index',compact('data'));
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
                $data[$i]['default_select'] = isset($item['default_select']) ? ACTIVE : INACTIVE;
                $data[$i]['shop_id'] = getCurrentUser()->shop_id;
                $i++;
            }
        }
        if (!empty($data)){
            OrderSource::insert($data);
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
        $selected = [];
        $unSelect = [];
        $wtName = [];
        $results = [];
        if (empty($params)){
            return  $results;
        }
            foreach ($params as $item){
                if (isset($item['id'])){
                    //update with name
                    if (isset($item['name']) && $item['name'] != null){
                        $wtName[] = $this->updateWithName($item);
                    }
                    //select ids to set selected & un select
                    else{
                        if (isset($item['default_select']) && $item['default_select'] == ACTIVE){
                            $selected[] = $item['id'];
                        }else{
                            $unSelect[] = $item['id'];
                        }
                    }
                }
            }
            //update selected
            $results =  $this->updateStatus($selected,$unSelect);
            $results['wt_name'] = $wtName;
            // all good
        return  $results;
    }
    /**
     * Store a newly created resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $data = $request->get('data');
        $new = $request->get('new');
        DB::beginTransaction();
        try {
        //cập nhật
        $this->update($data);
        //tạo mới
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
     * Delete resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function delete(Request $request){
        $id = $request->get('id');
        $info = OrderSource::findOrFail($id);
        if ($info->delete()){
            return 'true';
        }
        else{
            return  'false';
        }

    }
    /**
     * Update resource with name and status in storage.
     * @param  $params
     * @return array
     */
    public function updateWithName(array $params){
        $input = [
            'name' => $params['name'],
            'default_select' => isset($params['default_select']) ? ACTIVE : INACTIVE
        ];
        OrderSource::where('id',$params['id'])->update($input);
        return $params['id'];
    }
    /**
     * Update resource with only status storage.
     * @param  array
     * @return array
     */
    public function updateStatus(array $selected,array $unSelect){
        if (!empty($selected)){
            OrderSource::whereIn('id',$selected)->update(['default_select' => ACTIVE]);
        }
        //update un select
        if (!empty($unSelect)){
            OrderSource::whereIn('id',$unSelect)->update(['default_select' => INACTIVE]);
        }
        $results = [
            'selected' => $selected,
            'un_select' => $unSelect
        ];
        return $results;
    }

}
