<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',512);
            $table->tinyInteger('is_default')->nullable()->default(0)->comment("1:mặc định 0:ko phải mặc định");
            $table->tinyInteger('is_main')->nullable()->default(0)->comment("1:kho chính 0:ko phải kho chính");
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
        Schema::dropIfExists('stock_groups');
    }
}
