<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CurrentShopScope;
use App\Models\Scopes\ShopScope;

class OrderSource extends Model
{
    use CurrentShopScope;

    protected $fillable = ['name','default_select','is_system', 'shop_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ShopScope);
    }
}
