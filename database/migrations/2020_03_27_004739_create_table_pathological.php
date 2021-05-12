<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePathological extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pathological', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->comment("Tên bệnh");
            $table->text('status')->nullable()->comment("Tình trạng bệnh");
            $table->dateTime('date_create')->comment("Tạo lúc");
            $table->bigInteger('create_by')->nullable()->default(null)->comment("Người tạo");
            $table->bigInteger('customer_id');
            $table->text('history_update')->nullable()->default(null)->comment("Lịch sử chỉnh sửa");
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
        Schema::dropIfExists('pathological');
    }
}
