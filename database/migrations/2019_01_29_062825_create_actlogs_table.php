<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActlogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actlogs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id') -> unsigned() -> nullable() -> comment("ユーザID");
            $table->string('route') -> nullable() -> comment("route.webで設定した名称");
            $table->string('url') -> nullable() -> comment("要求Path");
            $table->string('method') -> nullable() -> comment("要求メソッド Get Post");
            $table->integer('status') -> unsigned() -> nullable() -> comment("要求結果 200 OK とか 301 move 等");
            $table->text('data') -> nullable() -> comment("要求内容（暗号化して保存）");
            $table->string('remote_addr') -> nullable() -> comment("クライアントIPアドレス");
            $table->string('user_agent') -> nullable() -> comment("ブラウザ名");
            $table->timestamps();

            $table->index(['created_at']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actlogs');
    }
}
