<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;

class Supplier extends Model
{

    protected $table = 'suppliers';
    protected $fillable = ['name','code','phone','address','prefecture', 'shop_id'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
}
