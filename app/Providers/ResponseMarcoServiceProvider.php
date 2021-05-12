<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\Response;

class ResponseMarcoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('needCallCount', function () {
            $user = getCurrentUser();
            $count = $needCall = OrderProduct::query()->leftJoin('products', function($join) {
                $join->on('order_products.product_id', '=', 'products.id');
            })->leftJoin('orders', function ($join) {
                $join->on('order_products.order_id', '=', 'orders.id');
            })->leftJoin('customers', function ($join) {
                $join->on('orders.customer_id', '=', 'customers.id');
            })->whereNotNull('orders.complete_date')->whereNotNull('products.customer_care_days')
            ->whereRaw(
                'DATE_ADD(orders.complete_date, INTERVAL products.customer_care_days DAY) <= DATE_ADD(NOW(), INTERVAL 3 DAY)')
            ->whereRaw(
                'DATE_ADD(orders.complete_date, INTERVAL products.customer_care_days DAY) >= NOW()')
            ->where('order_products.called', 0)
            ->where(function ($subQuery) use ($user) {
                if ($user->isAdmin()) {
                    $subQuery->where('user_created', $user->id)
                    ->orWhere('upsale_from_user_id', $user->id)
                    ->orWhere('assigned_user_id', $user->id)
                    ->orWhere('close_user_id', $user->id)
                    ->orWhere('delivery_user_id', $user->id)
                    ->orWhere('user_created', $user->id)
                    ->orWhere('marketing_id', $user->id);
                }
            })
            ->where('orders.shop_id', getCurrentUser()->shop_id)
            ->count();
            return $count;
        });
    }
}
