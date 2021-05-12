<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->string('account_id', 256)->nullable();
            $table->string('name', 512);
            $table->string('email', 512);
            $table->string('password');
            $table->string('avatar', 512)->nullable()->default(null);
            $table->tinyInteger('sex')->nullable()->default(null);
            $table->date('birthday')->nullable()->default(null);
            $table->string('phone', 15);
            $table->string('address', 1000)->nullable()->default(null);
            $table->integer('prefecture')->nullable()->default(null);
            $table->string('skype', 256)->nullable()->default(null);
            $table->tinyInteger('type')->default(1)->comment("0: Supper Admin, 1: Managers");
            $table->rememberToken();
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
        Schema::dropIfExists('admin');
    }
}