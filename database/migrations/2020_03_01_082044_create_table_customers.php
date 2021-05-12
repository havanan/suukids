<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCustomers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 512);
            $table->string('avatar', 1000)->nullable()->default(null);
            $table->integer('weight')->nullable()->default(null);
            $table->date('birthday')->nullable()->default(null);
            $table->string('email', 512)->nullable()->default(null);
            $table->string('phone', 15);
            $table->string('phone2', 15)->nullable()->default(null);
            $table->string('address')->nullable()->default(null);
            $table->string('job', 256)->nullable()->default(null);
            $table->string('position', 256)->nullable()->default(null)->comment("Chức vụ");
            $table->bigInteger('customer_group_id')->nullable()->default(null)->comment("Nhóm khách hàng");
            $table->tinyInteger('sex')->nullable()->default(null)->comment("1: Nam, 2: Nữ");
            $table->tinyInteger('prefecture')->nullable()->default(null);
            $table->tinyInteger('source_id')->nullable()->default(null)->comment("Nguồn khách hàng");
            $table->bigInteger('user_confirm_id')->nullable()->default(null)->comment("Người xác nhận");
            $table->bigInteger('created_by')->comment("Người tạo");
            $table->string('note', 1000)->nullable()->default(null)->comment("Ghi chú");
            $table->string('note_alert', 1000)->nullable()->default(null)->comment("Ghi chú cảnh báo");
            $table->string('bank_account_number', 256)->nullable()->default(null);
            $table->string('bank_account_name', 512)->nullable()->default(null);
            $table->string('bank_name', 512)->nullable()->default(null);

            $table->softDeletes();
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
        Schema::dropIfExists('customers');
    }
}