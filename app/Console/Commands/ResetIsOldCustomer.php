<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use DB;

class ResetIsOldCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset_is_old_customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đổi trường is_old_customer cho đúng';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /*
        DB::beginTransaction();
        try {
            $orders = Order::query()->with('order_products')->select('id', 'phone', 'create_date')->where('create_date', '>=', '2020-06-01')->get();
            foreach ($orders as $order) {
                $productIds = $order->order_products->pluck('product_id');
                $isDuplicatedOrderQuery = DB::table('orders')
                    ->join('order_products', function($join) use ($order)
                    {
                        $join->on('orders.id', '=', 'order_products.order_id')
                            ->whereIn('orders.status_id', STATUS_DON_HANG_THANH_CONG)
                            ->where('orders.phone', $order->phone);
                    })->whereIn('product_id', $productIds)->where('orders.shop_id', 23)->where('orders.id', '!=', $order->id)->where('orders.create_date', '<=', $order->create_date);
                $isDuplicatedOrder = $isDuplicatedOrderQuery->first();
                if (!empty($isDuplicatedOrder)) {
                    $order->is_old_customer = 1;
                    $order->save();
                    echo 'order: '. $order->id . ' ds cu';
                } else {
                    $order->is_old_customer = null;
                    $order->save();
                    echo 'order: '. $order->id . ' ds moi';
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
        */
    }
}
