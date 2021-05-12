<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnVtpCodeProvinceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->string('vtp_code')->after("ems_code")->nullable()->default(null);
            $table->string('vtp_id')->after("ems_code")->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->dropColumn('vtp_code');
            $table->dropColumn('vtp_id');
        });
    }
}
