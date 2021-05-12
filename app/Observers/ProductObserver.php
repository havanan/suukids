<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "saving" event.
     *
     * @param  \App\Product  $Product
     * @return void
     */
    public function saving(Product $product)
    {
        $product->shop_id = getCurrentUser()->shop_id;
    }
}
