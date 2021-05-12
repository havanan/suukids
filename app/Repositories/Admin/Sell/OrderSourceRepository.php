<?php

namespace App\Repositories\Admin\Sell;


use App\Models\OrderSource;
use App\Repositories\BaseRepository;

class OrderSourceRepository extends BaseRepository
{

    public function __construct(OrderSource $model)
    {
        $this->model = $model;
    }

    public function getAll() {
        return $this->model::query()->currentShop()->get();
    }
    public function pluckAll(){
        return OrderSource::pluck('name','id')->toArray();
    }

}
