<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\CurrentShopScope;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Scopes\ShopScope;

class OrderStatus extends Model
{
    use CurrentShopScope;
    use SoftDeletes;
    
    protected $table = 'order_status';

    protected $fillable = ['name', 'no_revenue_flag', 'no_reach_flag',
                            'is_system', 'is_default', 'is_customize', 'level',
                           'position', 'color', 'created_at', 'updated_at', 'shop_id']; 

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::addGlobalScope(new ShopScope);
    // }

    public function order()
    {
        return $this->hasOne(Order::class,'id','status_id');
    }
}
