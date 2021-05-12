<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class OrderSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('order_sources')->truncate();
        $datas = [
            [
                'name' => 'FB comment',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'FB inbox',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'Tạo tay',
                'default_select' => 1,
                'is_system' => 1,
            ],
            [
                'name' => 'Zalo',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'Hotline',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'Shoppe',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'Chưa rõ nguồn',
                'default_select' => 0,
                'is_system' => 1,
            ],
            [
                'name' => 'sLandingPages.com',
                'default_select' => 0,
                'is_system' => 1,
            ]
        ];
        DB::table('order_sources')->insert($datas);
    }
}
