<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;


class StockIn extends Model
{
    protected $table = 'stock_in';
    protected $guarded = ['id'];

    public function supplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier_id');
    }
    public function stockProduct()
    {
        return $this->hasOne(StockProduct::class, 'stock_id','id')->where('type',STOCK_IN);
    }
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
}
