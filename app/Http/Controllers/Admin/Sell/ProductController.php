<?php

namespace App\Http\Controllers\Admin\Sell;


use App\Http\Controllers\Controller;
use App\Repositories\Admin\Product\ProductRepository;
use Illuminate\Http\Request;
use App\Models\StockProduct;

class ProductController extends Controller {

    protected $repository;
    /**
     * ProductController constructor.
     */
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function apiSearch(Request $request) {
        try {
            $name = $request->get('name');
            $products = $this->repository->searcByName($name);
            return response()->json([
                'results' => $products->map(function($product) {
                    return [
                        'id' => $product->id,
                        'text' => "[Mã: ". $product->code ."]" . " " . $product->name . " | " . $product->price . " đ",
                        'price' => $product->price,
                        'name' => $product->name,
                        'size' => $product->size,
                        'color' => $product->color,
                    ];
                }),
                "pagination" => [
                    "more" => false
                ]
            ]);
        } catch(\Exception $ex) {
            return [
                'results' => [],
                "pagination" => [
                    "more" => false
                ]
            ];
        }   
    }

    public function apiGetOnHandInfo(Request $request) {
        try {
            $warehouseId = $request->get('warehouse_id');
            $productId = $request->get('product_id');

            $data = StockProduct::query()->where('product_id', $productId)->where('stock_group_id', $warehouseId)->firstOrFail();
            
            return response()->json([
                'quantity' => $data->quantity
            ]);
        } catch(\Exception $ex) {
            return $this->responseWithErrorMessage($ex->getMessage());
        }
    }
}