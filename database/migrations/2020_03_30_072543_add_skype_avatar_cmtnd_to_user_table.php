<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkypeAvatarCmtndToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cmtnd',30)->after("prefecture")->nullable()->default(null);
            $table->string('avatar', 512)->after("prefecture")->nullable()->default(null);
            $table->string('skype', 256)->after("prefecture")->nullable()->default(null);

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
            $table->dropColumn('cmtnd');
            $table->dropColumn('avatar');
            $table->dropColumn('skype');
        });
    }
}
