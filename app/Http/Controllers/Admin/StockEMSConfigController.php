<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Repositories\EMSRepository; 
use App\Models\EMSInventory; 
use App\Models\EMSService; 
use App\Models\EMSConfig; 

class StockEMSConfigController extends Controller {
    
    protected $repository;
    
    public function __construct(EMSRepository $repository) {
        $this->repository = $repository;
    }
    
    public function index() {
        $inventories = EMSInventory::all();
        $services = EMSService::all();
        $data = EMSConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
        return view('admin.stock.ems_config', compact('inventories', 'services', 'data'));
    }
    
    public function store(Request $request) {
        try {
            $param = $request->only('inventory_id', 'service_id');
            
            if (empty($param['inventory_id'])) {
                return back()->with('error', 'Vui lòng chọn kho');
            }
            
            if (empty($param['service_id'])) {
                return back()->with('error', 'Vui lòng chọn dịch vụ');
            }
            
            $param['shop_id'] = \getCurrentUser()->shop_id;
            $existConfig = EMSConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
            if (empty($existConfig)) {
                EMSConfig::query()->create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }
            
            return redirect()->route('admin.stock.ems_config')->with('success', 'Dữ liệu đã được update');
        } catch (Exception $e) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }
}