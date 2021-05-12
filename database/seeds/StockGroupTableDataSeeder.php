<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StockGroupTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('stock_groups')->truncate();
        $data = [
            ['name' => 'Kho tổng',
                'is_main' => 0,
                'is_default' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];
        DB::table('stock_groups')->insert($data);
    }
}