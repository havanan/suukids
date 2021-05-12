<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->comment("Số hiệu sản phẩm");
            $table->integer('quantity');
            $table->date('expired_date')->nullable()->default(null);
            $table->integer('unit_id')->nullable()->default(null);
            $table->string('unit_name')->nullable()->default(null);
            $table->double('price', 15, 2)->comment("Giá");
            $table->double('total', 15, 2)->comment("Thành tiền");
            $table->integer('stock_group_id')->nullable()->default(null)->comment("Kho ID");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_products');
    }

}