<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCsvPayslipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('csv_payslips', function (Blueprint $table) {
            $table->increments('id');
            $table -> char('ym', 6) -> comment('明細年月：yyyymm');
            $table -> char('status', 1) -> default('0') -> comment('状態：0:非公開  1:公開');
            $table -> string('filename') -> comment('CSVファイル名');
            $table -> text('header') -> comment('CSVヘッダー');
            $table -> integer('line') -> unsigned() -> default(0) -> comment('対象者数（CSV行数）');
            $table -> integer('error') -> unsigned() -> default(0) -> comment('エラー数（CSV行数）');
            $table -> datetime('published_at') -> nullable() -> comment('公開日時');
            $table -> timestamps();
            $table -> softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('csv_payslips');
    }
}
