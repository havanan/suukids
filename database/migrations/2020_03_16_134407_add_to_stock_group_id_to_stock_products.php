<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddToStockGroupIdToStockProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_products', function (Blueprint $table) {
            $table->bigInteger('to_stock_group_id')->after('stock_group_id')->nullable()->default(null)->comment("Xuất đến kho nội bộ");
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
            //
            $table->dropColumn('to_stock_group_id');
        });
    }
}
