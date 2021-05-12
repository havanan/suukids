<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EMSService extends Model
{
    // use SoftDeletes;
    
    protected $table = 'ems_services';

    protected $fillable = ['ems_code', 'name']; 
}
