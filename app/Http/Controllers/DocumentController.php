<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TCPDF;
use TCPDF_FONTS;

class DocumentController extends Controller
{
    public function downloadPdf()
    {
        // ダミーデータ設定
        $data['test01'] = "01 - あいうえお - left";
        $data['test02'] = "02 - あいうえお - center";
        $data['test03'] = "03 - あいうえお - right";

        // PDF 生成メイン　－　A4 縦に設定
        $pdf = new TCPDF("P", "mm", "A4", true, "UTF-8" );
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // PDF プロパティ設定
        $pdf->SetTitle('Title aiueo あいうえお');  // PDFドキュメントのタイトルを設定  http://tcpdf.penlabo.net/method/s/SetTitle.html
        $pdf->SetAuthor('Author aiueo あいうえお');  // PDFドキュメントの著者名を設定  http://tcpdf.penlabo.net/method/s/SetAuthor.html
        $pdf->SetSubject('Subject aiueo あいうえお');  // PDFドキュメントのサブジェクト(表題)を設定  http://tcpdf.penlabo.net/method/s/SetSubject.html
        $pdf->SetKeywords('KEY1 KEY2 KEY3 あいうえお'); // PDFドキュメントのキーワードを設定 http://tcpdf.penlabo.net/method/s/SetKeywords.html
        $pdf->SetCreator('Creator aiueo あいうえお');  // PDFドキュメントの製作者名を設定  http://tcpdf.penlabo.net/method/s/SetCreator.html

        // 日本語フォント設定
        $pdf->setFont('kozminproregular','',10);

        // ページ追加
        $pdf->addPage();

        // HTMLを描画、viewの指定と変数代入 - document/pdf.blade.php
        $pdf->writeHTML(view("pdf_test", $data)->render());

        // 出力指定 ファイル名、拡張子、D(ダウンロード)
        $pdf->output('test' . '.pdf', 'D');
        return;
   }
}
