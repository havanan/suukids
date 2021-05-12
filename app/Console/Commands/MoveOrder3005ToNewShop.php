<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Customer;
use DB;

class MoveOrder3005ToNewShop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:move_30_05_to_new_shop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        DB::beginTransaction();
        try {
            $oldShopId = 23;
            $newShopId = 25;
            $orders = Order::query()->where('create_date', '2020-05-30')->where('status_id', COMPLETE_ORDER_STATUS_ID)->where('shop_id', $oldShopId)->get();
            foreach ($orders as $order) {
                $orderProducts = $order->order_products;
                if (empty($orderProducts)) {
                    continue;
                }
                
                $orderProduct = $orderProducts->first();
                if ($orderProduct->product_id != 8) {
                    continue;
                }
                
                $customer = $order->customer;
                $existCustomer = Customer::query()->where('phone', $customer->phone)->where('shop_id', $newShopId)->first();
                if (empty($existCustomer)) {
                    $newCustomerId = DB::table('customers')->insertGetId([
                        'name' => $customer->name,
                        'phone' => $customer->phone,
                        'phone2' => $customer->phone2,
                        'shop_id' => $newShopId
                    ]);
                    
                    $order->user_created = 97;
                    $order->customer_id = $newCustomerId;
                }
                
                $orderProduct->product_id = 23;
                $orderProduct->save();
                
                $order->shop_id = $newShopId;
                $order->save();
            }
            
            DB::commit();
            
            echo 'success';
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
        
    }
}
