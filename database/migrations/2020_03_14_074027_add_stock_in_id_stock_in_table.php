<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStockInIdStockInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_products', function (Blueprint $table) {
            $table->bigInteger('stock_in_id')->before('product_id')->comment("Số hiệu Stock In");
            $table->tinyInteger('type')->after('stock_group_id')->default(0)->comment("0: Stock In, 1:Stock Out");

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_products', function (Blueprint $table) {
            $table->dropColumn('stock_in_id');
        });
    }
}