<?php

namespace App\Repositories\Admin\Warehouse;


use App\Models\StockGroup;
use App\Repositories\BaseRepository;

class StockGroupRepository extends BaseRepository
{
    public function __construct(StockGroup $model)
    {
        $this->model = $model;
    }
    public function getAll(){
        return $this->model::query()->currentShop()->get();
    }
    public function getArrAll(){
        return StockGroup::where(function ($q) {
            $q->where('shop_id',auth()->user()->shop_id)->orWhere('shop_id',null);
        })->pluck('name','id')->toArray();
    }
}
