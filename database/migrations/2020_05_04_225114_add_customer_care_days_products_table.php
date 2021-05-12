<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomerCareDaysProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('customer_care_days')->nullable()->default(null)->comment("Ngày gọi điện chăm sóc khách hàng");
        }); 

        Schema::table('order_products', function (Blueprint $table) {
            $table->tinyInteger('called')->nullable()->default(0)->comment("Đã gọi điện hỏi thăm?");
        }); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('customer_care_days');
        }); 

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('called');
        }); 
    }
}
