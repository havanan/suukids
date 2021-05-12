<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCallHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_care_id')->nullable()->default(null)->comment("Trạng thái KH");
            $table->string('content')->comment("Nội dung");
            $table->integer('customer_emotions')->nullable()->default(null)->comment("Cảm xúc KH 1:Bình thường,2:vui vẻ,3:Bực tức");
            $table->dateTime('date_create')->comment("Thời điểm gọi");
            $table->bigInteger('customer_id');
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
        Schema::dropIfExists('call_histories');
    }
}
