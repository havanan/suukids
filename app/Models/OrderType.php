<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CurrentShopScope;


class OrderType extends Model
{
    use CurrentShopScope;
    protected $table = 'order_types';

}