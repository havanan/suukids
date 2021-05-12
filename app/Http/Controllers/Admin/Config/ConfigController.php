<?php

namespace App\Http\Controllers\Admin\Config;


use App\Models\EMSToken;
use App\Models\Province;
use App\Models\ShippingConfig;
use App\Models\Shop;
use App\Models\VTPOSTConfig;
use App\Models\VTPOSTService;
use App\Models\VTPOSTStore;
use App\Models\Ward;
use App\Repositories\VTPostRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\EMSRepository;
use App\Repositories\VTPRepository;
use App\Models\EMSInventory;
use App\Models\EMSService;
use App\Models\EMSConfig;
use App\Models\District;
use App\Models\CloudfoneConfig;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use function GuzzleHttp\Psr7\get_message_body_summary;

class ConfigController extends Controller
{
    protected $emsRepository;

    public function __construct(EMSRepository $emsRepository,VTPRepository $vtpostRepository) {
        $this->emsRepository = $emsRepository;
        $this->vtpostRepository = $vtpostRepository;
    }

    public function emsIndex() {
        $user = getCurrentUser();
        $shopConfig = Shop::query()->whereKey($user->shop_id)->first();
        if ($shopConfig->shipping == 'ems') {
            if (EMSInventory::all()->count() == 0) {
                Artisan::call('ems:sync_inventories');
            }
            $inventories = $this->emsRepository->getListInventory();
            $services = EMSService::all();
            $data = EMSConfig::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();

            return view('admin.config.ems_config', compact('inventories', 'services', 'data'));
        } elseif ($shopConfig->shipping == 'vtp') {
            if (VTPOSTService::all()->count() == 0 ) {
                Artisan::call('vtpost:sync_service');
            }
            $stores = VTPOSTStore::all();
            $services = VTPOSTService::all();
            $config = VTPOSTConfig::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
            $data = compact(
                'stores',
                'services',
                'config'
            );
            return view('admin.config.vtpost_config', $data);
        }
    }

    public function saveEms(Request $request) {
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

            return redirect()->route('admin.config.ems.index')->with('success', 'Dữ liệu đã được update');
        } catch (Exception $e) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }
    public function vtpIndex() {
        $shop = Shop::findOrFail(auth()->user()->shop_id);
        $settings = json_decode($shop->settings,true);
        $province = Province::all();
        if (request()->input('provinceId')) {
            $district = District::where('_province_id',request()->input('provinceId')?:-1)->get();
        }
        else {
            $district = [];
        }
        if (request()->input('districtId')) {
            $ward = Ward::where('_district_id',request()->input('districtId')?:-1)->get();
        }
        else {
            $ward = [];
        }
        $selected_district = District::where('id', request()->input('districtId'))->first();
        $selected_province = Province::where('id', request()->input('provinceId') ?: ($selected_district ? $selected_district->_province_id : ''))->first();
        return view('admin.config.vtp_config',compact('settings','province','district','ward','selected_province','selected_district'));
    }

    public function emsSaveTokenView()
    {
        $data = EMSToken::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
        return view('admin.config.ems_token_config', compact('data'));
    }
    public function saveTokenEMS(Request $request)
    {
        try {
            $param = $request->only('token');
            if (empty($param['token'])) {
                return back()->with('error', 'Vui lòng nhập token EMS');
            }

            $param['shop_id'] = getCurrentUser()->shop_id;
            $existConfig = EMSToken::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();
            if (empty($existConfig)) {
                EMSToken::query()->create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }

            return redirect()->route('admin.config.ems.viewSaveToken')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $exception) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }

    public function cloudfoneIndex() {
        $data = CloudfoneConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
        return view('admin.config.cloudfone_config', compact('data'));
    }

    public function saveCloudfone(Request $request) {
        try {
            $param = $request->only('service_name', 'auth_user', 'auth_key');

            if (empty($param['service_name'])) {
                return back()->with('error', 'Vui lòng nhập Service Name');
            }

            if (empty($param['auth_user'])) {
                return back()->with('error', 'Vui lòng nhập AuthUser');
            }

            if (empty($param['auth_key'])) {
                return back()->with('error', 'Vui lòng nhập AuthKey');
            }

            $param['shop_id'] = \getCurrentUser()->shop_id;
            $existConfig = CloudfoneConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
            if (empty($existConfig)) {
                CloudfoneConfig::query()->create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }

            return redirect()->route('admin.config.cloudfone.index')->with('success', 'Dữ liệu đã được update');
        } catch (Exception $e) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }

    public function vtpostAccountIndex()
    {
        $data = ShippingConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
        return view('admin.config.vtpost_account_config', compact('data'));
    }

    public function vtpostAccountSave(Request $request)
    {
        $params = $request->except('_token');
        $params['shop_id'] = getCurrentUser()->shop_id;
        try {
            if (empty($params['vtpost_username'])) {
                return back()->with('error', 'Vui lòng đăng nhập tài khoản Viettel Post');
            }

            if (empty($params['vtpost_password'])) {
                return back()->with('error', 'Vui lòng mật khẩu tài khoản Viettel Post');
            }

            $existConfig = ShippingConfig::query()->where('shop_id', getCurrentUser()->shop_id)->first();
            if (empty($existConfig)) {
                ShippingConfig::query()->create($params);
            } else {
                $existConfig->fill($params);
                $existConfig->save();
            }
            return redirect()->route('admin.config.vtpost.index')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $e) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }

    public function vtpostShopIndex()
    {
        $provinces = Province::query()->whereNotNull('vtpost_province_id')->get();
        $data = compact(
            'provinces'
        );

        return view('admin.config.vtpost_create_shop', $data);
    }

    public function vtpostShopCreate(Request $request)
    {
        $param = $request->except('_token');
        $param['shop_id'] = getCurrentUser()->shop_id;
        try {
            if (empty($param['name'])) {
                return back()->with('error', 'Vui lòng nhập tên shop');
            }

            if (empty($param['phone'])) {
                return back()->with('error', 'Vui lòng nhập số điện thoại');
            }

            if (empty($param['address'])) {
                return back()->with('error', 'Vui lòng nhập địa chỉ');
            }

            if (empty($param['province_id'])) {
                return back()->with('error', 'Vui lòng chọn tỉnh / thành phố');
            }

            if (empty($param['district_id'])) {
                return back()->with('error', 'Vui lòng chọn quận / huyện');
            }

            if (empty($param['ward_id'])) {
                return back()->with('error', 'Vui lòng chọn xã / phường');
            }
            $ward = Ward::query()->whereKey($param['ward_id'])->first();
            $inventoryData = [
                'phone' => $param['phone'],
                'name' => $param['name'],
                'address' => $param['address'],
                'wards_id' => $ward->vtpost_ward_id,
            ];

            $response = $this->vtpostRepository->createStore($inventoryData);
            foreach ($response as $shop) {
                $vtShop = VTPOSTStore::query()->where('group_address_id', '=', $shop->groupaddressId)->first()->count();
                if ($vtShop != 0) {
                    VTPOSTStore::create([
                        'group_address_id' => $shop->groupaddressId,
                        'customer_id' => $shop->cusId,
                        'name' => $shop->name,
                        'phone' => $shop->phone,
                        'address' => $shop->address,
                        'province_id' => $shop->provinceId,
                        'district_id' => $shop->districtId,
                        'ward_id' => $shop->wardsId,
                    ]);
                } else {
                    continue;
                }
            }
            if (empty($existConfig)) {
                VTPOSTConfig::create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }

            return redirect()->route('admin.config.ems.index')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $exception) {
            Log::error($exception);
            return back()->with('error', 'Thao tác thất bại');
        }
    }

    public function vtpostConfigSave(Request $request)
    {
        $param = $request->except('_token');
        $param['shop_id'] = getCurrentUser()->shop_id;
        try {
            if (empty($param['group_address_id'])) {
                return back()->with('error', 'Vui lòng chọn kho');
            }

            if (empty($param['service_code'])) {
                return back()->with('error', 'Vui lòng chọn dịch vụ');
            }

            $existConfig = VTPOSTConfig::query()->where('shop_id', '=', getCurrentUser()->shop_id)->first();

            if (empty($existConfig)) {
                VTPOSTConfig::create($param);
            } else {
                $existConfig->fill($param);
                $existConfig->save();
            }

            return redirect()->route('admin.config.ems.index')->with('success', 'Dữ liệu đã được update');
        } catch (\Exception $exception) {
            return back()->with('error', 'Thao tác thất bại');
        }
    }
}
