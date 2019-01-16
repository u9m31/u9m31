<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use App\User;
use App\CsvPayslip;
use App\Payslip;

use TCPDF;
use TCPDF_FONTS;

class PayslipController extends Controller
{

    public function index(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // 検索条件指定準備
        $query = Payslip::query();
        $query -> withTrashed();

        // 対象 CSV_ID 指定
        if (array_key_exists('id', $request -> all())) {
          $query -> where('csv_id', '=', $request -> id);
        }

        // データ取得 orderby 年月 / id 
        $Payslips = $query -> orderBy('line','asc') 
                              -> get();

        // 検索結果を戻す
        return ['data' => $Payslips];
    }

    
    public function delete(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());
        Log::Debug(__CLASS__.':'.__FUNCTION__.' user id '. $request -> user() -> id);
        Log::Debug(__CLASS__.':'.__FUNCTION__.' del  id '. $request -> id);

        // 対象データ削除
        $payslip = Payslip::find($request -> id);
//        Log::Debug(__CLASS__.':'.__FUNCTION__, $payslip);
        if ($payslip) {
            $payslip -> fill(['delete_user_id' => $request -> user() -> id]) -> save();
            $payslip -> delete();
        }
        return ['data' => $payslip];
    }


    public function pdf(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // INIT
        mb_internal_encoding("UTF-8");

        // データ確認 ＆ 取得 -- エラーがあれば処理終了
        $payslip = $this -> validate_pdf($request);
        if (is_object($payslip)) { 
            Log::Debug(__CLASS__.':'.__FUNCTION__.' - ERROR - '. print_r($payslip, true));
            return $payslip;
        }

        // PDF 情報生成
        $data = $this -> make_pdf_data($payslip);

        // PDF 生成メイン　－　A4 縦に設定
        $pdf = new TCPDF("P", "mm", "A4", true, "UTF-8" );
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // PDF プロパティ設定
        $pdf->SetTitle('給与明細　'. $data['ym'] .'　'. $data['name']);       // PDFドキュメントのタイトルを設定
        $pdf->SetAuthor($data['company']);                                    // PDFドキュメントの著者名を設定
        $pdf->SetSubject($data['filename']);                                  // PDFドキュメントのサブジェクト(表題)を設定
        $pdf->SetKeywords('給与明細　'. $data['ym'] .'　'. $data['name']);    // PDFドキュメントのキーワードを設定
        $pdf->SetCreator($data['company'].'　u9m31');                         // PDFドキュメントの製作者名を設定

        // 日本語フォント設定
        $pdf->setFont('kozminproregular','',10);

        // ページ追加
        $pdf->addPage();

        // HTMLを描画、viewの指定と変数代入 - document/pdf.blade.php
        $html = view("m31_02", $data)->render();
        $pdf->writeHTML($html);

        // 出力指定 ファイル名、D(ダウンロード)
        $pdf->output('m31.pdf', 'D');
        return;
    }

    private function validate_pdf(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // INIT
        $req = $request -> all();
        $error_msg = '';

        // データを取得 User（操作者）
        $user = $request -> user();
        if (!$user) {
            return response() -> json(['errors' => [ 'request' => '12345  user not found']], 422);
        }
        Log::Debug(__CLASS__.':'.__FUNCTION__.' - user :: ', $user -> toArray());

        // データを取得 CSV Payslip  --  管理者の場合は論理削除済みでも取得可能
        if (array_key_exists('csv_id', $req)) {
            $query = CsvPayslip::query();
            if ($user -> can('admin')) { $query -> withTrashed(); }
            $csv_payslip = $query -> find($req['csv_id']);
            if (!$csv_payslip) {
                $error_msg = '12346 [csv_id] data  not found ';
            }
            else Log::Debug(__CLASS__.':'.__FUNCTION__.' - csv_payslip :: ', $csv_payslip -> toArray());
        } else {
            $error_msg = '12347 [csv_id] not found ';
        }

        // データを取得 Payslip  --  管理者の場合は論理削除済みでも取得可能
        if (array_key_exists('id', $req)) {
            $query = Payslip::query();
            if ($user -> can('admin')) { $query -> withTrashed(); }
            $payslip = $query -> find($req['id']);
            if (!$payslip) {
                $error_msg = '12348 [payslip id] data  not found ';
            }
            else Log::Debug(__CLASS__.':'.__FUNCTION__.' - payslip :: ', $payslip -> toArray());
        } else {
            $error_msg = '12349 [payslip id] not found ';
        }

        // ここまででエラーが発生していたら処理終了
        if ($error_msg) {
            return response() -> json(['errors' => [ 'request' => $error_msg]], 422);
        }

        // データの関連チェック payslipのcsv_id と csv_payslip の ID が違ったら、要求エラー
        if ($payslip -> csv_id != $csv_payslip -> id) {
            $error_msg = '12350 [payslip.csv_id] - [csv.id] mismatch';
            return response() -> json(['errors' => [ 'request' => $error_msg]], 422);
        }
        
        // 権限確認（一般ユーザは自分の明細以外はエラー）
        if (($user -> can('user')) && ($user -> id != $payslip -> user_id)) {
            return response() -> json(['errors' => [ 'auth' => '12351 対象の明細を開けませんでした']], 422);
        }

        // エラーなしリターン
        return array(
            'head' => $csv_payslip -> header, 
            'data' => $payslip -> data,
            'name' => $payslip -> name,
            'file' => $payslip -> filename,
            'ym'   => $payslip -> ym,
        );
    }

    private function make_pdf_data($payslip)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $payslip);

        // INIT
        mb_language('ja');
        mb_internal_encoding("UTF-8");

        // 明細情報生成
        $ym = substr($payslip['ym'], 0, 4). '年' .substr($payslip['ym'], 4, 2) .'月度';
        $data['ym'] = mb_convert_kana($ym, 'N'); // - 全角数字に変換
        $data['name'] = $payslip['name'];
        $data['filename'] = $payslip['file'];
        $data['company'] = env('MIX_COMPANY_NAME', '環境変数 MIX_COMPANY_NAME を設定してください');

        // PDF埋め込み用 明細情報領域初期化
        $data['head'] = array_fill(0, count($payslip['head']), '');
        $data['data'] = array_fill(0, count($payslip['data']), '');

        // 明細項目名設定
        $cnt = 0;
        foreach($payslip['head'] as $v) {
            $data['head'][++$cnt] = $v;
        }

        // 明細項目データ設定 - もし項目が半角ハイフンだったらヘッダーを隠す
        $cnt = 0;
        foreach($payslip['data'] as $v) {
            $data['data'][++$cnt] = $v;
            if ($v == '-') {
                $data['head'][$cnt] = '';
                $data['data'][$cnt] = '';
            }
        }
        Log::Debug(__CLASS__.':'.__FUNCTION__.' - pdf data :: ', $data);

        // 生成結果を戻す
        return $data;
    }
}
