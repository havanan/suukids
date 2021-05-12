<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    protected $table = 'shipping_configs';

    protected $fillable = [
        'vtpost_username',
        'vtpost_password',
        'shop_id',
        'user_id'
    ];
}
