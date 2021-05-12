<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogIsOldCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_is_old_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('customer_id')->nullable()->default(null);
            $table->string('controller')->nullable()->default(null);
            $table->string('function')->nullable()->default(null);
            $table->string('reason')->nullable()->default(null);
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
        Schema::dropIfExists('log_is_old_customer');
    }
}
