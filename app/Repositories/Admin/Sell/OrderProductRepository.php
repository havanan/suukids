<?php

namespace App\Repositories\Admin\Sell;


use App\Models\OrderProduct;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderProductRepository extends BaseRepository
{
    public function __construct(OrderProduct $model)
    {
        $this->model = $model;
    }

    public function create($data, $orderId) {
        $data['order_id']=$orderId;
        $data['weight'] = !empty($data['weight']) ? $data['weight'] : 0;
        $data['price'] = convertPriceToInt($data['price']);
        $data['stock_product_id'] = $data['warehouse_id'];
        return $this->model::query()->create($data);
    }

    public function deleteByOrderId($orderId) {
        return $this->model::query()->where('order_id', $orderId)->delete();
    }
    function queryGetProduct($params){

        $query = OrderProduct::leftJoin('orders','orders.id','order_products.order_id')
            ->leftJoin('products','products.id','order_products.product_id')
            ->leftJoin('order_status','order_status.id','orders.status_id')
            ->leftJoin('stock_products','stock_products.id','order_products.stock_product_id')
            ->leftJoin('product_units','product_units.id','products.unit_id')
            ->where('orders.shop_id', getCurrentUser()->shop_id);

        if (isset($params['from']) && $params['from'] != null) {
            $query = $query->where('orders.create_date', '>=', Carbon::createFromFormat('d/m/Y', $params['from'])->startOfDay());
        }
        if (isset($params['to']) && $params['to'] != null) {
            $query = $query->where('orders.create_date', '<=', Carbon::createFromFormat('d/m/Y', $params['to'])->endOfDay());
        }
        if (isset($params['code']) && $params['code'] != null) {
            $query = $query->where('products.code', $params['code']);
        }
        if (isset($params['bundle_id']) && $params['bundle_id'] != null) {
            $query = $query->where('orders.bundle_id', $params['bundle_id']);
        }
        if (isset($params['status_id']) && $params['status_id'] != null) {
            $query = $query->where('orders.status_id', $params['status_id']);
        }
        if (isset($params['sale_id']) && $params['sale_id'] != null) {
            $query = $query->where(function ($q) use ($params){
                $q->where('orders.assigned_user_id', $params['sale_id'])
                    ->orWhere('orders.user_created', $params['sale_id']);
            });
        }
        if (isset($params['stock_id']) && $params['stock_id'] != null) {
            $query = $query->where('stock_products.stock_id', $params['stock_id']);
        }
        if (isset($params['has_revenue']) && $params['has_revenue'] != null) {
            $query = $query->where('order_status.no_revenue_flag', $params['has_revenue']);
        }
        return $query;
    }
    public function getProductReport($params){
        $data = $this->queryGetProduct($params);
        $data = $data->select(
            'products.name',
            'products.code',
            'product_units.name as unit_name',
            DB::raw('Sum(order_products.quantity) as quantity'),
            DB::raw('Sum(order_products.quantity * order_products.price) as price')
        )
            ->groupBy(
                'order_products.product_id'
            )
            ->get();
//        dd($data,$params);
        return $data;
    }
}
