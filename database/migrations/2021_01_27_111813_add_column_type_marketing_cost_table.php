<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeMarketingCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('marketing_cost', function (Blueprint $table) {
            $table->integer('type')->after("shop_id")->nullable()->default(1)->comment('1: Target, 2: Cost');
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
            $table->dropColumn('type');
        });
    }
}
