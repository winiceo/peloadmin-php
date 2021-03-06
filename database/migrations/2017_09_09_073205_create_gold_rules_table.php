<?php



use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGoldRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gold_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('金币规则名称');
            $table->string('alias')->comment('金币规则别名');
            $table->string('desc')->commnet('金币规则描述');
            $table->integer('incremental')->comment('金币规则增量');
            $table->timestamps();

            $table->unique('alias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gold_rules');
    }
}
