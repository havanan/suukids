<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Traits\CurrentShopScope;
use App\Models\Scopes\ShopScope;

class DeliveryMethod extends Model
{
    use CurrentShopScope;
    //
    protected $table = 'delivery_methods';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ShopScope);
    }
}
