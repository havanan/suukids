<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpiredDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dateTime('expired_date')->nullable()->default(null)->after('phone');
            $table->tinyInteger('is_pause')->nullable()->default(0)->after('expired_date');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('expired_date');
            $table->dropColumn('is_pause');
            $table->dropSoftDeletes();

        });
    }
}