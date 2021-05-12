<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoVtpostToProvinceDistrictWardTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->string('vtpost_province_code')->nullable();
            $table->integer('vtpost_province_id')->nullable();
        });

        Schema::table('district', function (Blueprint $table) {
            $table->integer('vtpost_district_id')->nullable();
            $table->integer('vtpost_district_value')->nullable();
        });

        Schema::table('ward', function (Blueprint $table) {
            $table->integer('vtpost_ward_id')->nullable();
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
            $table->dropColumn('vtpost_province_code');
            $table->dropColumn('vtpost_province_id');
        });

        Schema::table('district', function (Blueprint $table) {
            $table->dropColumn('vtpost_district_id');
            $table->dropColumn('vtpost_district_value');
        });

        Schema::table('ward', function (Blueprint $table) {
            $table->dropColumn('vtpost_ward_id');
        });
    }
}
