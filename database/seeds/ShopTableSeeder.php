<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShopTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->truncate();
        $data = [
            [
                'name' => 'Shop Manager',
                'address' => 'Example',
                'phone' => '0123456789',
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ],
        ];
        DB::table('shops')->insert($data);
    }
}
