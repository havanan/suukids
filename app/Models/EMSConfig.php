<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EMSConfig extends Model
{
    // use SoftDeletes;
    
    protected $table = 'ems_configs';

    protected $fillable = ['inventory_id', 'service_id', 'shop_id']; 
}
