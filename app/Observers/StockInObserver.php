<?php

namespace App\Observers;

use App\Models\StockIn;

class StockInObserver
{
    /**
     * Handle the StockIn "saving" event.
     *
     * @param  StockIn $entity
     * @return void
     */
    public function saving(StockIn $entity)
    {
        $entity->shop_id = getCurrentUser()->shop_id;
    }
}
