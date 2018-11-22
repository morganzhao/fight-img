<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name')->nullable();
            $table->string('signature')->nullable();
            $table->string('nickname')->nullable();
            $table->string('wxname')->nullable();
            $table->string('openid')->comment('微信openid')->nullable();
            $table->integer('age');
            $table->tinyInteger('sex')->default(1)->comment('性别');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('salt')->comment('盐')->nullable();
            $table->string('password')->nullable;
            $table->string('clear-text_password')->comment('明文密码')->nullable();
            $table->string('token',500)->comment('登录令牌')->nullable();
            $table->string('avatar_url',500)->comment('头像地址')->nullable();
            $table->string('vedio_url',500)->comment('视频地址')->nullable();
            $table->dateTime('expire')->comment('验证码过期时间');
            $table->dateTime('time_expire')->comment('验证码未过期时间');
            $table->integer('sms_code')->comment('短信验证码');
            $table->rememberToken();
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
