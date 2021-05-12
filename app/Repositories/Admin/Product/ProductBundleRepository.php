<?php

namespace App\Repositories\Admin\Product;


use App\Models\ProductBundle;
use App\Repositories\BaseRepository;

class ProductBundleRepository extends BaseRepository
{
    /**
     * ProductBundleRepository constructor.
     *
     * @param  ProductBundle  $model
     */
    public function __construct(ProductBundle $model)
    {
        $this->model = $model;
    }

    public function getAll() {
        return $this->model::query()->currentShop()->get();
    }

}