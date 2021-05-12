<?php

namespace App\Models;

use App\Models\Scopes\ShopScope;
use Illuminate\Database\Eloquent\Model;

use DB;

use App\Models\Traits\CurrentShopScope;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use CurrentShopScope;
    use SoftDeletes;

    //
    protected $table = 'customers';

    protected $guarded = ["id"];

    public $timestamps = true;


    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function tags_arr()
    {
        return array_filter(explode(',',$this->tags));
    }

    public function callHistories()
    {
        return $this->hasMany(CallHistory::class);
    }
    public function noteHistories()
    {
        return $this->hasMany(CallHistory::class);
    }
    public function customerGroup()
    {
        return $this->belongsTo(CustomerGroup::class, 'customer_group_id');
    }
    public function province() {
        return $this->belongsTo(Province::class, 'prefecture');
    }
    public function contactUser() {
        return $this->belongsTo(Customer::class, 'contact_id');
    }
}
