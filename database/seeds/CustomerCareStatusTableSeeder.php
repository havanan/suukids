<?php

use Illuminate\Database\Seeder;

class CustomerCareStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('customer_care_status')->truncate();
        $datas = [
            [
                'name' => 'Gọi thành công',
            ],
            [
                'name' => 'Không nghe máy',
            ],
            [
                'name' => 'Thuê bao',
            ],
            [
                'name' => 'Sai số',
            ],
            [
                'name' => 'Không tín hiệu',
            ],
            [
                'name' => 'KH từ chối',
            ],
            [
                'name' => 'Gọi lại',
            ],
            [
                'name' => 'Máy bận',
            ],
            [
                'name' => 'Tắt máy ngang',
            ],
            [
                'name' => 'KH gọi nhỡ',
            ],
            [
                'name' => 'KH chốt đơn',
            ],
            [
                'name' => 'Lý do đặc biệt',
            ]
        ];
        DB::table('customer_care_status')->insert($datas);
    }
}
