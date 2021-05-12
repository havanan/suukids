<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 512);
            $table->string('account_id', 256)->comment("Account ID dùng để đăng nhập");
            $table->string('email')->nullable()->default(null);
            $table->date('birthday')->nullable()->default(null);
            $table->tinyInteger('sex')->nullable()->default(null);
            $table->string('phone', 15);
            $table->string('address', 1000)->nullable()->default(null);
            $table->integer('prefecture')->nullable()->default(null);
            $table->integer('user_group_id')->nullable()->default(null)->comment("Nhóm người dùng");
            $table->datetime('expried_day')->nullable()->default(null)->comment("Ngày hết hạn");
            $table->tinyInteger('type')->nullable()->default(null);
            $table->tinyInteger('status')->nullable()->default(1);
            $table->string('password');
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
        Schema::dropIfExists('users');
    }
}