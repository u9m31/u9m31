<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table -> increments('id');
            $table -> integer('csv_id') -> unsigned() -> comment('CSV_ID');
            $table -> integer('line') -> unsigned() -> comment('CSV行番号');
            $table -> char('ym', 6) -> comment('明細年月：yyyymm');
            $table -> char('status', 1) -> default('0') -> comment('状態：0:有効  9:削除');
            $table -> integer('user_id') -> nullable() -> unsigned() -> comment('対象者ID（内部ID）');
            $table -> string('loginid') -> comment('対象者ログインID');
            $table -> text('data') -> comment('CSV行データ');
            $table -> string('filename') -> nullable() -> comment('ファイル名');
            $table -> integer('download') -> unsigned() -> default(0) -> comment('ユーザダウンロード回数');
            $table -> string('error') -> nullable() -> comment('CSVエラー内容');
            $table -> integer('delete_user_id') -> nullable() -> unsigned() -> comment('削除操作者ID');
            $table -> timestamps();
            $table -> softDeletes();

            $table -> index('csv_id');
            $table -> index('user_id');
            $table -> index('loginid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payslips');
    }
}
