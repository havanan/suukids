<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnUserLevelMarketingCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_cost', function (Blueprint $table) {
            $table->integer('user_id')->after("shop_id")->nullable()->default(39);
            $table->integer('level')->after("shop_id")->nullable()->default(1)->comment('1: Admin, 2: User');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marketing_cost', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('level');
        });
    }
}
