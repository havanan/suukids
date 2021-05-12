<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockProduct extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
    protected $guarded = ['id'];
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }
    public function stockGroup()
    {
        return $this->hasOne('App\Models\StockGroup', 'id', 'stock_group_id');
    }
}
