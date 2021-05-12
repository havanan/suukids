<?php

namespace App\Models;

use App\Models\Traits\CurrentShopScope;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;

class StockGroup extends Model
{
    use CurrentShopScope;
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
}
