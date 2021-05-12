<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VTPOSTConfig extends Model
{
    protected $table = 'vtpost_configs';

    protected $fillable = [
        'group_address_id',
        'service_code',
        'shop_id'
    ];
}
