<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use  App\Models\User;
use App\Models\Traits\CurrentShopScope;
use App\Models\Scopes\ShopScope;


class UserGroup extends Model
{
    use CurrentShopScope;

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new ShopScope);
    }
    
    public function users() {
        return $this->hasMany(User::class, 'user_group_id');
    }
}
