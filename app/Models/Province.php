<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\District;

class Province extends Model
{
    protected $table = 'province';

    public $timestamps = false;
    public function districts(){
        return $this->hasMany(District::class,'_province_id');
    }
    protected $fillable = [
        '_name', '_code', 'ems_code', 'vtpost_province_code', 'vtpost_province_id', 'province_slug', 'status'
    ];

}
