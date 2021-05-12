<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockIn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_in', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('create_day')->comment("Ngày tạo");
            $table->string('bill_number')->comment("Số phiếu");
            $table->string('deliver_name')->nullable()->default(null)->comment("Người giao");
            $table->string('receiver_name')->nullable()->default(null)->comment("Người nhận");
            $table->string('note')->nullable()->default(null)->comment("Ghi chú");
            $table->integer('supplier_id')->nullable()->default(null)->comment("Nhà cung cấp");
            $table->double('total', 15, 2)->comment("Tổng thanh toán");

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
        Schema::dropIfExists('stock_in');
    }
}