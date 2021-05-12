<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExportExcelLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('export_excel_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 512)->nullable();
            $table->string('detail', 1000)->nullable();
            $table->string('url', 512)->nullable();
            $table->integer('account_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('ip', 15)->nullable();
            $table->integer('shop_id')->nullable();
            $table->string('shop_name')->nullable();
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
        Schema::dropIfExists('export_excel_logs');
    }
}
