<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFieldsOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->datetime('share_date')->comment("Ngày chia")->nullable()->default(null)->change();
            $table->datetime('close_date')->comment("Ngày chốt")->nullable()->default(null)->change();
            $table->datetime('delivery_date')->comment("Ngày phân phát")->nullable()->default(null)->change();
            $table->datetime('complete_date')->comment("Ngày thành công")->nullable()->default(null)->change();
            $table->datetime('collect_money_date')->comment("Ngày thu tiền")->nullable()->default(null)->change();

            $table->unsignedInteger('district_id')->nullable();
            $table->unsignedInteger('ward_id')->nullable();
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
            $table->datetime('share_date')->comment("Ngày chia")->nullable(false)->change();
            $table->datetime('close_date')->comment("Ngày chốt")->nullable(false)->change();
            $table->datetime('delivery_date')->comment("Ngày phân phát")->nullable(false)->change();
            $table->datetime('complete_date')->comment("Ngày thành công")->nullable(false)->change();
            $table->datetime('collect_money_date')->comment("Ngày thu tiền")->nullable(false)->change();

            $table->dropColumn('district_id');
            $table->dropColumn('ward_id');
        });
    }
}