<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColHistoryUpdateToCallHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('call_histories', function (Blueprint $table) {
            $table->text('history_update')->after('customer_id')->nullable()->default(null)->comment("Lịch sử chỉnh sửa");
        });
        Schema::table('note_histories', function (Blueprint $table) {
            $table->text('history_update')->after('customer_id')->nullable()->default(null)->comment("Lịch sử chỉnh sửa");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('call_histories', function (Blueprint $table) {
            $table->dropColumn('history_update');
        });
        Schema::table('note_histories', function (Blueprint $table) {
            $table->dropColumn('history_update');
        });
    }
}
