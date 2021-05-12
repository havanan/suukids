<?php

namespace App\Repositories\Admin\Sell;


use App\Models\DeliveryMethod;
use App\Repositories\BaseRepository;

class DeliveryMethodRepository extends BaseRepository
{
    /**
     * DeliveryMethodRepository constructor.
     * @param DeliveryMethod $deliveryMethod
     */
    public function __construct(DeliveryMethod $deliveryMethod)
    {
        $this->model = $deliveryMethod;
    }

    public function getAll() {
        return $this->model::query()->currentShop()->get();
    }
    public function getArrAll(){
        return $this->model::query()->currentShop()->pluck('name','id')->toArray();
    }
}
