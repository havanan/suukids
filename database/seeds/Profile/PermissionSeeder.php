<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        DB::table('permissions')->truncate();

        // Status for MKT
        DB::table('permissions')->insert([
            'name' => 'MKT',
            'sale_flag' => 0,
            'customer_manager_flag' => 0,
            'view_revenue_sale_flag' => 0,
            'share_orders_flag' => 0,
            'bung_don_flag' => 0,
            'bung_don2_flag' => 0,
            'customer_care_flag' => 0,
            'marketing_flag' => 1,
            'marketing_manager_flag' => 0,
            'view_revenue_marketing_flag' => 0,
            'export_excel_flag' => 0,
            'accountant_flag' => 0,
            'stock_out_flag' => 0,
            'stock_manager_flag' => 0,
            'status_permissions' => '{"1":2,"2":2,"3":2,"4":1,"5":1,"6":1,"7":1,"8":2,"9":1,"10":1,"11":1}',
        ]);
        // Status for SALE
        DB::table('permissions')->insert([
            'name' => 'SALE',
            'sale_flag' => 1,
            'customer_manager_flag' => 0,
            'view_revenue_sale_flag' => 0,
            'share_orders_flag' => 0,
            'bung_don_flag' => 0,
            'bung_don2_flag' => 0,
            'customer_care_flag' => 0,
            'marketing_flag' => 0,
            'marketing_manager_flag' => 0,
            'view_revenue_marketing_flag' => 0,
            'export_excel_flag' => 0,
            'accountant_flag' => 0,
            'stock_out_flag' => 0,
            'stock_manager_flag' => 0,
            'status_permissions' => '{"1":2,"2":2,"3":2,"4":1,"5":1,"6":1,"7":1,"8":2,"9":1,"10":1,"11":1}',
        ]);
    }
}