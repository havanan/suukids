<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->truncate();

        DB::table('users')->insert([
            'account_id' => 'admin',
            'password' => Hash::make('123456'),
            'name' => 'Admin',
            'phone' => '123456',
            'type' => 0, // Quyền Admin
        ]);

    }
}