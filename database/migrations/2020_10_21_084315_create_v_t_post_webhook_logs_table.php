<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVTPostWebhookLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vtpost_webhook_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->nullable();
            $table->string('order_number')->nullable();
            $table->string('order_reference')->nullable();
            $table->dateTime('order_statusdate')->nullable();
            $table->integer('order_status')->nullable();
            $table->string('location_currently')->nullable();
            $table->text('note')->nullable();
            $table->integer('product_weight')->nullable();
            $table->text('json_body')->nullable();
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
        Schema::dropIfExists('vtpost_webhook_logs');
    }
}
