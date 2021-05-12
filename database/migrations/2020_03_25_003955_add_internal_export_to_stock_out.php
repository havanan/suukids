<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalExportToStockOut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_out', function (Blueprint $table) {
            $table->tinyInteger('internal_export')->after('total')->nullable()->default(null)->comment("1: xuất nội bộ. 2. xuât bình thường");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_out', function (Blueprint $table) {
            $table->dropColumn('internal_export');
        });
    }
}