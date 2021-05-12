<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VTPOSTStore extends Model
{
    protected $table = 'vtpost_store';

    protected $fillable = [
        'group_address_id',
        'customer_id',
        'name',
        'phone',
        'address',
        'province_id',
        'district_id',
        'ward_id',
    ];
}
