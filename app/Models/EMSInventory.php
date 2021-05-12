<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EMSInventory extends Model
{
    // use SoftDeletes;

    protected $table = 'ems_inventories';

    protected $fillable = ['ems_id', 'name', 'username', 'address', 'shop_id'];
}
