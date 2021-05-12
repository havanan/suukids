<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class UserLoginTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->whereNull('login_time_from')->update([
            'login_time_from' => (new Carbon(LOGIN_TIME_FROM . ':00 2020-01-01'))->toDateTimeString(),
            'login_time_to' => (new Carbon(LOGIN_TIME_TO . ':00 2020-01-01'))->toDateTimeString()
        ]);
    }
}
