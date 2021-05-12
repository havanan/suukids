<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EMSOrderStatus extends Model
{
    // use SoftDeletes;
    
    protected $table = 'ems_order_status';

    protected $fillable = ['code', 'name', 'is_complete', 'is_refund']; 
}
