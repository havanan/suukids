<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmsWebhookLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ems_webhook_log', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip')->nullable();
            $table->string('tracking_code')->nullable();
            $table->string('order_code')->nullable();
            $table->string('status_code')->nullable();
            $table->string('status_name')->nullable();
            $table->string('note')->nullable();
            $table->string('locate')->nullable();
            $table->string('datetime')->nullable();
            $table->string('total_weight')->nullable();
            $table->string('json_body')->nullable();
            $table->string('ems_transaction')->nullable();
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
        Schema::dropIfExists('ems_webhook_log');
    }
}
