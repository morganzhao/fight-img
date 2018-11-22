<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('img')->nullable();
            $table->string('local_img')->nullable();
            $table->string('label')->nullable();
            $table->integer('userid')->nullable();
            $table->tinyInteger('type')->comment('1:系统默认图片，2:用户制作gif图片，3:用户自作视频')->default(1)->nullable();
            $table->integer('save_num')->comment('保存数')->default(0)->nullable();
            $table->integer('search_num')->comment('搜索次数')->default(0);
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
        Schema::dropIfExists('resources');
    }
}
