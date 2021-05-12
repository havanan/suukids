<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCloudfoneCodeUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cloudfone_code')->nullable()->default(null)->comment("Số nội bộ bên cloudfone");
            $table->tinyInteger('active_cloudfone')->after('cloudfone_code')->nullable()->default(null)->comment("Cho dùng cloudfone");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIfExists('cloudfone_code');
            $table->dropIfExists('active_cloudfone');
        });
    }
}
