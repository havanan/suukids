<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductUnit::all();
        return view('admin.product.unit', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *@param $params
     * @return array
     */
    public function create($params)
    {
        $data = [];
        if (empty($params)) {
            return $data;
        }
        $i = 0;
        foreach ($params as $item) {
            if ($item['name'] != null) {
                $data[$i]['name'] = $item['name'];
                $data[$i]['shop_id'] = getCurrentUser()->shop_id;
                $i++;
            }
        }
        if (!empty($data)) {
            ProductUnit::insert($data);
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
        if (empty($params)) {
            return $results;
        }
        foreach ($params as $item) {
            if (isset($item['id'])) {
                //update with name
                if (isset($item['name']) && $item['name'] != null) {
                    $wtName[] = $this->updateWithName($item);
                }
            }
        }
        // all good
        return $results;

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
            //c???p nh???t
            $this->update($data);
            //t???o m???i
            $this->create($new);
            DB::commit();
            return back()->with('success', 'D??? li???u ???? ???????c update');
        } catch (\Exception $e) {
            // dd($e);
            // something went wrong
            DB::rollback();
            return back()->with('error', 'Thao t??c th???t b???i');
        }

    }
    /**
     * Delete resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @return boolean
     */
    public function delete(Request $request)
    {
        $id = $request->get('id');
        $productUnit = ProductUnit::findOrFail($id);
        DB::beginTransaction();
        Product::where('unit_id', $id)
            ->update(['unit_id' => null]);
        if ($productUnit->delete()) {
            DB::commit();
            return 'true';
        } else {
            return 'false';
        }
    }
    /**
     * Update resource with name and status in storage.
     * @param  $params
     * @return array
     */
    public function updateWithName(array $params)
    {
        $input = [
            'name' => $params['name'],
        ];
        ProductUnit::where('id', $params['id'])->update($input);
        return $params['id'];
    }
}