<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNoteHistories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('note_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('content')->comment("Nội dung");
            $table->integer('customer_emotions')->nullable()->default(null)->comment("Cảm xúc KH 1:Bình thường,2:vui vẻ,3:Bực tức");
            $table->dateTime('date_create')->comment("Tạo lúc");
            $table->bigInteger('create_by')->nullable()->default(null)->comment("Người tạo");
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
        Schema::dropIfExists('note_histories');
    }
}
