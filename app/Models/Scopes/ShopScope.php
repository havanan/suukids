<?php

namespace App\Models\Scopes;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ShopScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (auth()->check() && !Auth::guard('superadmin')->check()) {
            $builder->whereNull($model->getTable() . '.shop_id')->orWhere($model->getTable() . '.shop_id', getCurrentUser()->shop_id);
        }
    }
}