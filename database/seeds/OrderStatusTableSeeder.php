<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_status')->truncate();
        $data = [
            ['name' => 'Gọi máy bận', 'color' => '#dabc51', 'no_revenue_flag' => 0, 'no_reach_flag' => 1, 'is_system' => 1, 'is_default' => 0, 'level' => 1, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Chưa xác nhận', 'color' => '#76ec39', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 0, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Hủy', 'color' => '#7a7980', 'no_revenue_flag' => 1, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 1, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Chuyển hàng', 'color' => '#FF0000', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 3, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Xác Nhận - Chốt đơn', 'color' => '#cc6633', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 2, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Chuyển hoàn', 'color' => '#000000', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 4, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Thành công', 'color' => '#996699', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 5, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Không nghe điện', 'color' => '#337ab7', 'no_revenue_flag' => 0, 'no_reach_flag' => 1, 'is_system' => 1, 'is_default' => 0, 'level' => 1, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Kế toán mặc định', 'color' => '#ff0099', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 1, 'is_default' => 0, 'level' => 2, 'position' => 0, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Đã thu tiền', 'color' => '#00a65a', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 0, 'is_default' => 1, 'level' => 5, 'position' => 1, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            ['name' => 'Đã trả hàng về kho', 'color' => '#331211', 'no_revenue_flag' => 0, 'no_reach_flag' => 0, 'is_system' => 0, 'is_default' => 1, 'level' => 5, 'position' => 2, 'created_at' => Carbon::now()->format('Y-m-d H:i:s')],
            //...
        ];
        DB::table('order_status')->insert($data); // Query Builder approach
    }
}