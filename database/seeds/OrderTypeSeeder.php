<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class OrderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_types')->truncate();
        $data = [
            ['name' => 'SALE(Số mới)'],
            ['name' => 'CSKH'],
            ['name' => 'Tối ưu'],
            ['name' => 'Đặt lại'],
            ['name' => 'Đặt lại lần 1'],
            ['name' => 'Đặt lại lần 2'],
            ['name' => 'Đặt lại lần 3'],
            ['name' => 'Đặt lại lần 4'],
            ['name' => 'Đặt lại lần 5'],

        ];
        DB::table('order_types')->insert($data); // Query Builder approach
    }
}
