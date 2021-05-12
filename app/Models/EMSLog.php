<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class EMSLog extends Model
{
    // use SoftDeletes;
    
    protected $table = 'ems_logs';

    protected $fillable = ['order_id', 'address', 'product_name', 'price', 'user_send', 'status', 'message']; 
}
