<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCancelFieldOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('cancel_user_id')->nullable()->default(null)->comment("Người huy đơn");
            $table->string('cancel_user_type')->nullable()->default(null);
            $table->datetime('cancel_date')->comment("Ngày hủy đơn")->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cancel_user_id');
            $table->dropColumn('cancel_date');
        });
    }
}
