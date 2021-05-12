<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin')->truncate();
        DB::table('admin')->insert([
            'account_id' => 'superadmin',
            'phone' => '123456',
            'email' => 'superadmin@gmail.com',
            'name' => 'Admin',
            'type' => 0,
            'password' => Hash::make('123456'),
        ]);
    }
}
