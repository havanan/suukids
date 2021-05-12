<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Ward;

class AddressController extends Controller
{
    public function apiSearchDistrict(Request $request) {
        try {
            $provinceId = $request->get('province_id');
            $name = $request->get('name');
            $districts = District::query()->where('_province_id', $provinceId);
            if ($name) {
                $districts = $districts->where('_name', 'LIKE', "%$name%");
            }
                $districts = $districts->where('status', 1)->get();
            return response()->json([
                'results' => $districts->map(function($district) {
                    return [
                        'id' => $district->id,
                        'text' => $district->_name,
                    ];
                }),
                "pagination" => [
                    "more" => false
                ]
            ]);
        } catch(\Exception $ex) {
            return $this->responseWithErrorMessage($ex->getMessage());
        }

    }

    public function apiSearchWard(Request $request) {
        try {
            $districtId = $request->get('district_id');
            $name = $request->get('name');
            $wards = Ward::query()->where('_district_id', $districtId);
                if ($name){
                    $wards = $wards->where('_name', 'LIKE', "%$name%");
                }
                $wards = $wards->where('status', 1)
                ->get();

            return response()->json([
                'results' => $wards->map(function($ward) {
                    return [
                        'id' => $ward->id,
                        'text' => $ward->_name,
                    ];
                }),
                "pagination" => [
                    "more" => false
                ]
            ]);
        } catch(\Exception $ex) {
            return $this->responseWithErrorMessage($ex->getMessage());
        }

    }
}
