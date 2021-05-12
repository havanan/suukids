<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Ward;
use App\Models\Province;

class District extends Model
{
    protected $table = 'district';

    public $timestamps = false;
    protected $fillable = [
        '_name', '_prefix', '_province_id', 'ems_code', 'vtp_code','vtp_id', 'vtpost_district_id', 'vtpost_district_value', 'district_slug', 'status'
    ];
    public function wards(){
        return $this->hasMany(Ward::class,'_district_id');
    }
    public function province(){
        return $this->belongsTo(Province::class,'_province_id');
    }
}
