<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourceTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('模版名称')->default(null);
            $table->string('pid')->comment('父级id')->default(0);
            $table->string('file_path')->coomment('文件路径')->default(null);
            $table->string('download_url')->comment('下载地址')->default(null);
            $table->string('description')->comment('描述')->default(null);
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
        Schema::dropIfExists('resource_templates');
    }
}
