<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(PermissionSeeder::class);
        $this->call(OrderStatusTableSeeder::class);
        $this->call(PrefecturesTableSeeder::class);
        $this->call(AdminTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(OrderSourcesTableSeeder::class);
        $this->call(CustomerGroupTableSeeder::class);
        $this->call(StockGroupTableDataSeeder::class);
        $this->call(OrderTypeTableSeeder::class);
    }
}