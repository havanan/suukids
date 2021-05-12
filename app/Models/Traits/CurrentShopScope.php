<?php

namespace App\Models\Traits;

trait CurrentShopScope {
    public function scopeCurrentShop($query)
    {
        return $query->whereNull('shop_id')->orWhere('shop_id', getCurrentUser()->shop_id);
    }

    public function scopeOnlyCurrentShop($query) {
        return $query->where('shop_id', getCurrentUser()->shop_id);
    }
}
