<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger("order_id");
            $table->tinyInteger("type")->nullable()->default(null)
                ->comment("Loại lịch sử: 1: Mở đơn hàng, 2: Gán cho tài khoản, 3: Chuyển trạng thái, 4: Sửa sản phẩm, 5: Sửa các thông số khác");
            $table->string("message", 1000)->nullable()->default(null)->comment("Nội dung");
            $table->bigInteger("created_by")->nullable()->default(null)->comment("Người thay đổi");

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
        Schema::dropIfExists('order_histories');
    }
}