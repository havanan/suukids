<?php

namespace App\Repositories\Admin\Profile;


use App\Models\OrderStatus;
use App\Repositories\BaseRepository;

class OrderStatusRepository extends BaseRepository
{
    /**
     * OrderStatusRepository constructor.
     * @param OrderStatus $orderStatus
     */
    public function __construct(OrderStatus $orderStatus)
    {
        $this->model = $orderStatus;
    }

    public function getStatusOrderByLevel() {
        return $this->model::query()->currentShop()->orderBy('level')->get();
    }

}