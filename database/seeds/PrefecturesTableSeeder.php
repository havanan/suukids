<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrefecturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = 'database/seeds/prefectures.sql';
        DB::unprepared(file_get_contents($path));
        $this->command->info('Prefecture seeded!');
    }
}