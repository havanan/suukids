<?php

namespace App\Http\Controllers\Admin;

use App\Models\VTPOSTConfig;
use App\Models\VTPOSTService;
use App\Models\VTPOSTStore;
use App\Repositories\VTPostRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StockVTPostController extends Controller
{
    public function __construct(VTPostRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $inventories = VTPOSTStore::all();
        $services = VTPOSTService::all();

        $data = VTPOSTConfig::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
        return view('admin.stock.vtpost_config', compact($inventories, $services, $data));
    }

    public function store(Request $request)
    {
        try {
            $param = $request->only('group_address_id', 'service_code');

            if (empty($param['inventory_address_id'])) {
                return back()->with('error', 'Vui lòng chọn kho');
            }

            if (empty($param['service_code'])) {
                return back()->with('error', 'Vui lòng chọn dịch vụ');
            }

            $param['shop_id'] = \getCurrentUser()->shop_id;
            $existConfig = VTPOSTConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
            if (empty($existConfig)) {
                VTPOSTConfig::query()->create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }

            return redirect()->route('admin.stock.vtpost_config')->with('success', 'Dữ liệu đã được update');
        } catch (Exception $e) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }
}
