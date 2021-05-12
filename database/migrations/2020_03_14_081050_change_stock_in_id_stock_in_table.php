<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeStockInIdStockInTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_products', function (Blueprint $table) {
            $table->renameColumn('stock_in_id', 'stock_id');
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
            $table->renameColumn('stock_id', 'stock_in_id');
        });
    }
}