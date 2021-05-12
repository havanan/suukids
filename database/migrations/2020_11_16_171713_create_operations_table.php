<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id')->comment("Số hiệu sản phẩm");
            $table->integer('before_quantity')->nullable()->default(0)->comment("Số lượng trước giao dịch");
            $table->integer('quantity');
            $table->date('expired_date')->nullable()->default(null);
            $table->integer('unit_id')->nullable()->default(null);
            $table->string('unit_name')->nullable()->default(null);
            $table->double('price', 15, 2)->comment("Giá");
            $table->double('total', 15, 2)->comment("Thành tiền");
            $table->integer('stock_group_id')->nullable()->default(null)->comment("Kho ID");
            $table->bigInteger('to_stock_group_id')->nullable()->default(null)->comment("Xuất đến kho nội bộ");
            $table->tinyInteger('type')->default(0)->comment("0: Stock In, 1:Stock Out");
            $table->bigInteger('stock_id')->comment("Số hiệu Stock In");
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
        Schema::dropIfExists('operations');
    }
}
