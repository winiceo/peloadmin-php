<?php

declare(strict_types=1);



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsApplyLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_apply_logs', function (Blueprint $table) {
            $table->integer('news_id')->unsigned()->comment('资讯id');
            $table->integer('user_id')->unsigned()->comment('用户id');
            $table->integer('status')->unsigned()->comment('审核状态，0-待审核 1-已处理 2-已驳回');
            $table->integer('mark')->nullable()->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_apply_logs');
    }
}
