<?php

namespace App\Repositories\Admin\Product;


use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{

    public function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function searcByName($key) {
        return $this->model::query()->active()->currentShop()
            ->where(function ($query) use ($key) {
                $query->where('name', 'LIKE', "%".$key."%")
                    ->orWhere('code', 'LIKE', "%".$key."%");
            })->get();
    }
}