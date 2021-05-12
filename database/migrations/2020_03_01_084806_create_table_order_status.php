<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',512);
            $table->tinyInteger('no_revenue_flag')->nullable()->default(0)->comment("Không tính doanh thu");
            $table->tinyInteger('no_reach_flag')->nullable()->default(0)->comment("Không tiếp cận");
            $table->tinyInteger('is_system')->nullable()->default(0)->comment("Là trạng thái hệ thống");
            $table->tinyInteger('is_default')->nullable()->default(0)->comment("Là trạng thái mặc định");
            $table->integer('level')->nullable()->default(0)->comment("Cấp bậc của trạng thái");
            $table->integer('position')->nullable()->default(0)->comment("Vị trí");
            $table->string('color',10)->nullable()->default(NULL);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_status');
    }



}
