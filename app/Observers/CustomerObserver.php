<?php

namespace App\Observers;

use App\Models\Customer;

class CustomerObserver
{
    /**
     * Handle the Customer "saving" event.
     *
     * @param  Customer $entity
     * @return void
     */
    public function saving(Customer $entity)
    {
        $entity->shop_id = getCurrentUser()->shop_id;
    }
}
