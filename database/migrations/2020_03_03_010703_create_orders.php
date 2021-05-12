<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("shipping_code", 256)->nullable()->default(null);
            $table->bigInteger("customer_id")->nullable()->default(null);
            $table->string("note1", 1000)->nullable()->default(null);
            $table->string("note2", 1000)->nullable()->default(null);
            $table->string("shipping_note", 1000)->nullable()->default(null);
            $table->tinyInteger("is_top_priority")->nullable()->default(0);
            $table->tinyInteger("is_send_sms")->nullable()->default(0);
            $table->tinyInteger("is_inner_city")->nullable()->default(0);
            $table->integer("status_id")->nullable()->default(null);
            $table->integer("shipping_service_id")->nullable()->default(null);
            $table->integer("bundle_id")->nullable()->default(null);
            $table->integer("source_id")->nullable()->default(null);
            $table->tinyInteger("type")->nullable()->default(null);
            $table->bigInteger("user_created")->nullable()->default(null)->comment("Người tạo đơn");
            $table->bigInteger("upsale_from_user_id")->nullable()->default(null)->comment("Người Up Sale");
            $table->bigInteger("assigned_user_id")->nullable()->default(null)->comment("Chia đơn cho");
            $table->string("cancel_note", 1000)->nullable()->default(null);
            $table->double('price', 15, 2)->comment("Thành tiền");
            $table->double('discount_price', 15, 2)->comment("Giảm giá");
            $table->double('shipping_price', 15, 2)->comment("Phí vận chuyển");
            $table->double('other_price', 15, 2)->comment("Phụ thu");
            $table->double('total_price', 15, 2)->comment("Tổng tiền");
            $table->datetime('create_date')->comment("Ngày tạo");
            $table->datetime('share_date')->comment("Ngày chia")->nullable();
            $table->datetime('close_date')->comment("Ngày chốt")->nullable();
            $table->datetime('assign_accountant_date')->comment("Ngày chuyển cho kế toán")->nullable();
            $table->datetime('delivery_date')->comment("Ngày phân phát")->nullable();
            $table->datetime('complete_date')->comment("Ngày thành công")->nullable();
            $table->datetime('collect_money_date')->comment("Ngày thu tiền")->nullable();

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
        Schema::dropIfExists('orders');
    }
}