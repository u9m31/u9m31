<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

use App\User;
use App\CsvPayslip;
use App\Payslip;
use App\Facades\Csv;
use App\Http\Requests\UploadCsvFile;

class CsvPayslipController extends Controller
{

    public function index(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // 検索条件指定準備
        $query = CsvPayslip::query();

        // -- 削除済みを含む
        if (array_key_exists('deleted', $request -> all())) {
          if ($request -> deleted == 'true') {
            $query -> withTrashed();
          }
        }

        // -- 対象年月（未指定の場合は全期間を対象とする）
        $ym = $this -> getYM($request -> all(), false);
        if (is_object($ym)) { return $ym; } // 期間エラー
        if ($ym) {
          $query -> where('ym', '=', $ym);
        }

        // データ取得 orderby 年月 / id 
        $CsvPayslips = $query -> orderBy('ym','desc') 
                              -> orderBy('id', 'desc') 
                              -> get();

        // 検索結果を戻す
        return ['data' => $CsvPayslips];
    }


    public function delete(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // 対象データ削除
        $csv_payslip = CsvPayslip::find($request -> id);
        if ($csv_payslip) {
            $csv_payslip -> delete();
        }
        return;
    }


    public function publish(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // 更新データ生成
        $data['published_at'] = Carbon::now();
        $data['status'] = '1';

        // 対象データ更新
        $csv_payslip = CsvPayslip::find($request -> id);
        if ($csv_payslip) {
            $csv_payslip -> fill($data) -> save();
        }
        return;
    }


    public function upload(UploadCsvFile $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // CSV をパース
        $file = $request -> file('csvfile');
        $rows = $this -> parse_csv($file);
        if (is_object($rows)) { return $rows; }

        // 対象年月を取得 (数字以外はすべて削除してから取得）
        $ym = $this -> getYM($request -> all());
        if (is_object($ym)) { return $ym; }

        // INIT
        $ret = array();
        $header_columns = 0;

        // １行ずつ処理 - ループ処理でデータエラーがあっても処理は継続
        foreach ($rows as $line => $value) {
            Log::Debug(__CLASS__.':'.__FUNCTION__.' '. ($line + 1) .'/'. count($rows) .' : '. print_r($value, true));

            // INIT
            $wk = '';
            $data = array();

            // １行目ならヘッダー情報を保存
            if ($line == 0) {
                // CSV 情報を保存
                $csv_payslip = $this -> insertCsvPayslip( $ym, $file, $value );
                if (!$csv_payslip) {
                    return new JsonResponse(['errors' => [ 'database' => '内部エラー csv payslip insert error']], 422);
                }

                // ヘッダーの項目数を取得
                $header_columns = count($value);
                continue; // ヘッダーはそのまま次の行へ
            }

            // ＣＳＶ行データのカラム数チェック（ヘッダーのカラム数と違ったらエラー）
            if (count($value) != $header_columns) {
                $wk = 'ヘッダーの項目数('.$header_columns.'）と行の項目数('.count($value).')が一致しません';
                $ret['errors'][] = ['line' => $line, 'message' => $wk];
                $data['error'] = $wk;
                Log::Debug(__CLASS__.':'.__FUNCTION__.' '. ($line + 1) .'/'. count($rows) .' : '. $wk);
            }

            // ＣＳＶの最初のカラムはユーザのログインIDで固定：ユーザを検索
            $data['loginid'] = trim($value['No0']);   // 対象者ログインID
            $user = User::where('loginid', $data['loginid']) -> first();

            // ＣＳＶに指定のユーザの存在チェック（ユーザが存在しなければエラー）
            if (!$user) {
                $wk = '該当社員(id: '.$data['loginid'] .')が見つかりませんでした';
                $ret['errors'][] = ['line' => $line, 'message' => $wk];
                $data['error'] = $wk;
                Log::Debug(__CLASS__.':'.__FUNCTION__.' '. ($line + 1) .'/'. count($rows) .' : '. $wk);
            }

            // ＣＳＶ行データ保存設定
            $data['csv_id'] = $csv_payslip['id'];     // CsvPayslip id
            $data['line'] = $line;                    // CSV行番号
            $data['ym'] = $ym;                        // 明細年月
            $data['data'] = $value;                   // CSV行データ
            $data['user_id'] = $user['id'];           // 対象者内部ＩＤ

            // ＣＳＶの２番目のカラムは明細のファイル名：指定があればファイル名を保存
            if (trim($value['No1']) != '') {
                $data['filename'] = trim($value['No1']);
            }

            // ＣＳＶ行データ保存
            Payslip::create($data);

            // ＣＳＶ行カウンタ設定　－　エラーあり？正常？
            if (isset($data['error'])) {
                $csv_payslip -> error ++;
            } else {
                $csv_payslip -> line ++;
                $ret['insert'][] = ['line' => $line, 'message' => $user['name'] ." の明細を登録しました."];
            }

        } // １行ずつ処理

        // CSV行データ読み込み結果保存（正常行数、エラー数)
        $csv_payslip -> save();

        // 結果を戻す
        return ['import' => $ret];
    }


    private function parse_csv($file)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__);

        // 拡張子チェックがうまく動かないことがあるので独自で実施
        // -- https://api.symfony.com/3.0/Symfony/Component/HttpFoundation/File/UploadedFile.html
        if ($file ->getClientOriginalExtension() != 'csv') {
            Log::Debug(__CLASS__.':'.__FUNCTION__.' File Name: '. $file ->getClientOriginalName());
            Log::Debug(__CLASS__.':'.__FUNCTION__.' File Extension: '. $file ->getClientOriginalExtension());
            Log::Debug(__CLASS__.':'.__FUNCTION__.' ClientMimeType: '. $file ->getClientMimeType());
            Log::Debug(__CLASS__.':'.__FUNCTION__.' MimeType: '. $file ->getMimeType());
            return new JsonResponse(['errors' => [ 'csvfile' => 'CSVファイルを指定してください']], 422);
        }

        // CSV をパース
        try {
            $rows = Csv::parse($file, true);
        } catch (\Exception $e) {
            Log::Debug(__CLASS__.':'.__FUNCTION__.' parse Exception : '. $e -> getMessage());
            return new JsonResponse(['errors' => [
                'csvfile' => 'CSVファイルの読み込みでエラーが発生しました',
                'exception' => $e -> getMessage()
                ]]
            , 422);
        }

        // CSVレコード情報を戻す
        return $rows;
    }


    private function insertCsvPayslip($ym, $file, $value)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__);

        // CsvPayslip テーブル保存準備
        $data = array();
        $data['ym'] = $ym;
        $data['filename'] = $file -> getClientOriginalName();
        $data['header'] = $value;

        // CsvPayslip テーブル保存
        $csv_payslip = CsvPayslip::create($data);

        // 結果を戻す
        return $csv_payslip;
    }


    private function getYM($request, bool $required = true)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__);

        // 対象年月を取得 (数字以外はすべて削除してから取得）
        if (array_key_exists('ym', $request)) {
            $ym = preg_replace("/\D/", "", $request['ym']);
        }

        // 対象年月が未指定ならエラー
        else {
            return new JsonResponse(['errors' => ['target_ym1' => '対象年月を指定してください']], 422);
        }

        // 対象年月のチェックは必須？
        if ($required) {
            // 対象年月は 2010～2099年01～12月であること
            if (! preg_match('/^20([1-9]{1}[0-9]{1})(0[1-9]{1}|1[0-2]{1})$/', $ym)) {
                return new JsonResponse(['errors' => ['target_ym2' => '対象年月は2010年01月～2099年12月で指定してください']], 422);
            }
        }

        // 対象年月を戻す
        return $ym;
    }

}
