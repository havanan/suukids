<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmsCodeProvincesDistrictsWardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->integer('ems_code')->default(null)->nullable()->comment('Mã bên vận đơn');
        });
        
        Schema::table('district', function (Blueprint $table) {
            $table->integer('ems_code')->default(null)->nullable()->comment('Mã bên vận đơn');
        });
        
        Schema::table('ward', function (Blueprint $table) {
            $table->integer('ems_code')->default(null)->nullable()->comment('Mã bên vận đơn');
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
            $table->dropColumn('ems_code');
        });
        
        Schema::table('district', function (Blueprint $table) {
            $table->dropColumn('ems_code');
        });
        
        Schema::table('ward', function (Blueprint $table) {
            $table->dropColumn('ems_code');
        });
    }
}
