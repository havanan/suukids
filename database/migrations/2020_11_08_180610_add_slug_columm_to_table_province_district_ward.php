<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSlugColummToTableProvinceDistrictWard extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->string('province_slug')->nullable();
        });

        Schema::table('district', function (Blueprint $table) {
            $table->string('district_slug')->nullable();
        });

        Schema::table('ward', function (Blueprint $table) {
            $table->string('ward_slug')->nullable();
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
            $table->string('province_slug')->nullable();
        });

        Schema::table('district', function (Blueprint $table) {
            $table->string('district_slug')->nullable();
        });

        Schema::table('ward', function (Blueprint $table) {
            $table->string('ward_slug')->nullable();
        });
    }
}
