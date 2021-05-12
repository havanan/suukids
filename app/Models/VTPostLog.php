<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VTPostLog extends Model
{
    protected $table = 'vtpost_logs';

    protected $fillable = ['order_id', 'address', 'product_name', 'price', 'user_send', 'status', 'message'];
}
