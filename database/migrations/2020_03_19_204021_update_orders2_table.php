<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrders2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',512);
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('close_user_id')->nullable();
            $table->string('close_user_type')->nullable();
            $table->unsignedInteger('delivery_user_id')->nullable();
            $table->string('delivery_user_type')->nullable();
            $table->unsignedInteger('create_user_id')->nullable();
            $table->string('create_user_type')->nullable();
            $table->unsignedInteger('province_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_types');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('close_user_id');
            $table->dropColumn('close_user_type');
            $table->dropColumn('delivery_user_id');
            $table->dropColumn('delivery_user_type');
            $table->dropColumn('create_user_id');
            $table->dropColumn('create_user_type');
            $table->dropColumn('province_id');
        });
    }
}
