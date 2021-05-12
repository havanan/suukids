<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmsOrderStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('ems_order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('code');
            $table->string('name');
            
            $table->tinyInteger('is_complete')->nullable()->default(null);
            $table->tinyInteger('is_refund')->nullable()->default(null);
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
        Schema::dropIfExists('ems_order_status');
    }
}
