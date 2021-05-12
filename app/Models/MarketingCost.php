<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\OrderSource;
use App\Models\User;

class MarketingCost extends Model
{
    protected $table = 'marketing_cost';
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function created_info() {
        return $this->belongsTo(User::class,'created_by');
    }
    public function source() {
        return $this->belongsTo(OrderSource::class);
    }
}
