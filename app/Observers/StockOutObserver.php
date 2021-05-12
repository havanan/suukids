<?php

namespace App\Observers;

use App\Models\StockOut;

class StockOutObserver
{
    /**
     * Handle the StockOut "saving" event.
     *
     * @param  StockOut $entity
     * @return void
     */
    public function saving(StockOut $entity)
    {
        $entity->shop_id = getCurrentUser()->shop_id;
    }
}
