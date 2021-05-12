<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShopIdFieldsPermissionStatusTable extends Migration
{
    protected $tableNames = ['order_status', 'permissions'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->tableNames as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedInteger('shop_id')->nullable()->default(null);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->tableNames as $tableName) {
            if (Schema::hasColumn($tableName, 'shop_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('shop_id');
                });
            }
        }
    }
}
