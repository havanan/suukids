<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\ShopScope;


class CustomerGroup extends Model
{
    //
    protected $table = 'customer_groups';

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new ShopScope);
    }
}
