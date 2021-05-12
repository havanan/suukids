<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CurrentShopScope;
use App\Models\Scopes\ShopScope;

class ProductBundle extends Model
{
    use CurrentShopScope;
    protected $fillable = ['name'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
}
