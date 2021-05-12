<?php

namespace App\Http\Controllers\Admin\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\VTPRepository;
use App\Models\Shop;
use App\Models\Province;
use App\Models\District;
use App\Models\Ward;

class VtpController extends Controller
{
    protected $vtpRepository;

    public function __construct(VTPRepository $vtpRepository) {
        $this->vtpRepository = $vtpRepository;
    }
    public function login() {
        $res = $this->vtpRepository->login(['USERNAME'=>request()->input('username'),'PASSWORD'=>request()->input('password')]);
        if ($res['success']) {
            $shop = Shop::findOrFail(auth()->user()->shop_id);
            $settings = json_decode($shop->settings,true);
            $settings['vtp'] = isset($settings['vtp'])?$settings['vtp']:['enabled'=>false];
            $settings['vtp']['token'] = $res['data']['token'];
            $settings['vtp']['userId'] = $res['data']['userId'];
            $shop->update(['settings' => json_encode($settings, JSON_UNESCAPED_UNICODE)]);
            $shop = Shop::findOrFail(auth()->user()->shop_id);
            $shop->settings = json_decode($shop->settings,true);
            return response()->json(['success'=>true,'data'=>$res['data'],'msg'=>'Cập nhật thành công','shop'=>$shop]);
        }
        return response()->json(['success'=>false,'msg'=>$res['msg']]);
    }
    public function listProvince() {
        $res = $this->vtpRepository->getListProvince(request()->only('provinceId'));
        if ($res['success']) {
            foreach($res['data'] as $key=>$val) {
                $province = Province::where('_name',$val['PROVINCE_NAME'])->first();
                if ($province) {
                    $province->update(['vtp_code' => $val['PROVINCE_CODE'], 'vtp_id' => $val['PROVINCE_ID']]);
                }
            }
            return response()->json(['success'=>true,'data'=>$res['data'],'msg'=>'Cập nhật thành công']);
        }
        return response()->json(['success'=>false,'msg'=>$res['msg']]);
    }
    public function listDistrict() {
        $res = $this->vtpRepository->getListDistrict(request()->only('provinceId'));
        if ($res['success']) {
            foreach($res['data'] as $key=>$val) {
                $province = Province::where('vtp_id',$val['PROVINCE_ID'])->first();
                if ($province) {
                    $words = explode(' ',$val['DISTRICT_NAME']);
                    $prefix = array_shift($words);
                    $name = implode(' ', $words);
                    if (in_array($prefix, ['QUẬN','HUYỆN'])){
                        $district = District::where('_province_id',$province->id)->where('_name','like',$name)->where('_prefix','like', $prefix)->first();
                        if ($district) {
                            $district->update(['vtp_code' => $val['DISTRICT_VALUE'], 'vtp_id' => $val['DISTRICT_ID']]);
                        }
                    }
                }
            }
            return response()->json(['success'=>true,'data'=>$res['data'],'msg'=>'Cập nhật thành công']);
        }
        return response()->json(['success'=>false,'msg'=>$res['msg']]);
    }
    public function listWard() {
        if (!request()->input('districtId')){
            return response()->json(['success'=>false,'msg'=>'Vui lòng chọn quận huyện']);
        }
        $res = $this->vtpRepository->getListWard(request()->only('districtId'));
        if ($res['success']) {
            foreach($res['data'] as $key=>$val) {
                $district = District::where('vtp_id',$val['DISTRICT_ID'])->first();
                if ($district) {
                    $words = explode(' ',$val['WARDS_NAME']);
                    $prefix = array_shift($words);
                    $name = implode(' ', $words);
                    if (in_array($prefix, ['PHƯỜNG','XÃ','THỊ TRẤN'])){
                        $ward = Ward::where('_district_id',$district->id)->where('_name','like',$name)->where('_prefix','like', $prefix)->first();
                        if ($ward) {
                            $ward->update(['vtp_code' => $val['WARDS_ID'], 'vtp_id' => $val['WARDS_ID']]);
                        }
                    }
                }
            }
            return response()->json(['success'=>true,'data'=>$res['data'],'msg'=>'Cập nhật thành công']);
        }
        return response()->json(['success'=>false,'msg'=>$res['msg']]);
    }
    public function listBuuCuc() {
        $res = $this->vtpRepository->getListBuuCuc();
        if ($res['success']) {
            $updated = 0;
            foreach($res['data'] as $key=>$val) {
                if ($updated > 100) break;
                $province = Province::where('_name','like',$val['TEN_TINH'])->first();
                if ($province) {
                    $words = explode(' ',$val['TEN_QUANHUYEN']);
                    $prefix = array_shift($words);
                    $name = implode(' ', $words);
                    $district = District::where('_province_id', $province->id)->where('name','like',$name)->first();
                    if ($district) {
                        $words = explode(' ',$val['TEN_PHUONGXA']);
                        $prefix = array_shift($words);
                        $name = implode(' ', $words);
                        $ward = Ward::where('_district_id', $district->id)->where('_name','like',$name)->first();
                        if ($ward) {
                            $posts = json_decode($ward->vtp_post,true)?:[];
                            if (!isset($posts[$val['MA_BUUCUC']])) {
                                $posts[$val['MA_BUUCUC']] = $val;
                            }
                            $ward->update(['vtp_post' => json_encode($posts,JSON_UNESCAPED_UNICODE)]);
                            $updated += 1;
                        }
                    }
                }
            }
            return response()->json(['success'=>true,'updated'=>$updated,'data'=>$res['data'],'msg'=>'Cập nhật thành công']);
        }
        return response()->json(['success'=>false,'msg'=>$res['msg']]);
    }
}
