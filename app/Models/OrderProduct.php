<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Order;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $fillable = ['order_id', 'product_id', 'price', 'quantity', 'weight', 'stock_product_id'];

    function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }

    function order() {
        return $this->belongsTo(Order::class, 'order_id');
    }
}