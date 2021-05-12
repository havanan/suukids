<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusColumnToLocationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('province', function (Blueprint $table) {
            $table->integer('status')->default(1)->comment('1:active, 2:inactive');
        });
        Schema::table('district', function (Blueprint $table) {
            $table->integer('status')->default(1)->comment('1:active, 2:inactive');
        });
        Schema::table('ward', function (Blueprint $table) {
            $table->integer('status')->default(1)->comment('1:active, 2:inactive');
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
            $table->dropColumn('status');
        });
        Schema::table('district', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        Schema::table('ward', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
