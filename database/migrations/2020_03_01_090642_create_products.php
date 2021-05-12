<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',512);
            $table->string('product_image',512)->nullable()->default(NULL);
            $table->string('code',512)->comment("Mã sản phẩm");
            $table->double('price', 15, 2)->comment("Giá");
            $table->double('cost_price', 15, 2)->comment("Giá vốn");
            $table->integer('on_hand')->comment("Tồn kho đầu kỳ");
            $table->integer('unit_id')->nullable()->default(NULL)->comment("Đơn vị");
            $table->integer('bundle_id')->nullable()->default(NULL)->comment("Phân loại sản phẩm");
            $table->string('color',10)->nullable()->default(NULL);
            $table->string('size',256)->nullable()->default(NULL);
            $table->tinyInteger('status')->nullable()->default(1)->comment('Phân lại sản phẩm: 1: Kinh doanh, 2: Ngừng kinh doanh');
            $table->softDeletes();
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
        Schema::dropIfExists('products');
    }
}
