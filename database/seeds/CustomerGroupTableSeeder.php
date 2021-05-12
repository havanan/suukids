<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CustomerGroupTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customer_groups')->truncate();
        $data = [
            ['name' => 'Danh mục gốc',
                'parent_id' => 0,
                'is_default' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            ['name' => 'Khách mới',
                'parent_id' => 1,
                'is_default' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
            ['name' => 'Khách quen',
                'parent_id' => 1,
                'is_default' => 1,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];
        DB::table('customer_groups')->insert($data);
    }
}