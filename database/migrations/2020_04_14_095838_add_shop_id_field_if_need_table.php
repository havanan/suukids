<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopIdFieldIfNeedTable extends Migration
{
    protected $tableNames = ['users', 'orders', 'suppliers', 'products', 
                            'customers', 'call_histories', 'customer_groups',
                            'delivery_methods', 'order_types', 'product_bundles',
                            'stock_in', 'stock_out'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tableNames as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('shop_id')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tableNames as $tableName) {
            if (Schema::hasColumn($tableName, 'shop_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('shop_id');
                });
            }
        }
    }
}
