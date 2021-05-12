<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;
use App\Models\Traits\CurrentShopScope;

class Product extends Model
{
    protected $guarded = ["id"];
    use CurrentShopScope;
    public function productUnit()
    {
        return $this->hasOne(ProductUnit::class, 'id', 'unit_id');
    }
    public function orders() {
        return $this->belongsToMany(Order::class, 'order_products', 'product_id', 'order_id');
    }
    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }
}
