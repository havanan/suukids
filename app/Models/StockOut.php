<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;

class StockOut extends Model
{
    protected $table = 'stock_out';
    protected $guarded = ['id'];
    protected $fillable = ['create_day','bill_number','deliver_name','receiver_name','note','total','internal_export','supplier_id', 'shop_id'];
    public function supplier()
    {
        return $this->hasOne(Supplier::class,'id','supplier_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
}
