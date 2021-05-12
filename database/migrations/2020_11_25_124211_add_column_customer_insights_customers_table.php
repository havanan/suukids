<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnCustomerInsightsCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('refferal',255)->after("position")->nullable()->default(null)->comment('Người giới thiệu');
            $table->string('sale_note',500)->after("position")->nullable()->default(null)->comment('Lời nhắc/lời khuyên');
            $table->string('buy_capacity',255)->after("position")->nullable()->default(null)->comment('Tài chính');
            $table->boolean('app_installed')->after("position")->nullable()->default(false)->comment('Tải app hay chưa');
            $table->text('tags')->after("position")->nullable()->default(null)->comment('Các bệnh');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('refferal');
            $table->dropColumn('sale_note');
            $table->dropColumn('buy_capacity');
            $table->dropColumn('app_installed');
            $table->dropColumn('tags');
        });
    }
}
