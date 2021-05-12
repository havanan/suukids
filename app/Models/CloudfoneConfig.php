<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CloudfoneConfig extends Model
{
    // use SoftDeletes;
    
    protected $table = 'cloudfont_configs';

    protected $fillable = ['shop_id', 'service_name', 'auth_user', 'auth_key']; 
}
