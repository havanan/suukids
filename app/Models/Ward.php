<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\District;

class Ward extends Model
{
    protected $table = 'ward';

    public $timestamps = false;
    protected $fillable = [
        '_name', '_prefix', '_province_id', '_district_id', 'ems_code', 'vtp_post', 'vtp_id', 'vtp_code', 'vtpost_ward_id', 'ward_slug', 'status'
    ];
    public function district(){
        return $this->belongsTo(District::class,'_district_id');
    }
}
