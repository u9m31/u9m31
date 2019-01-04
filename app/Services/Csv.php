<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Response;

use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;
use Goodby\CSV\Import\Standard\LexerConfig;

class Csv
{
    /**
     * CSVダウンロード
     * @param array $csv_data
     * @param array $csv_header
     * @param string $csv_filename
     * @return \Illuminate\Http\Response
     */
    public function download($csv_data, $csv_header, $csv_filename)
    {
        // ヘッダー指定あれば１行目にヘッダーをセット
        if (count($csv_header) > 0) {
            array_unshift($csv_data, $csv_header);
        }

        // ストリームでレスポンス ::  
        //      vendor/laravel/framework/src/Illuminate/Routing/ResponseFactory.php  
        //          streamDownload($callback, $name = null, array $headers = [], $disposition = 'attachment')
        return response() -> streamDownload( 
            function () use($csv_data) {
                $file = new \SplFileObject('php://output', 'w');
                foreach ($csv_data as $row) {
                    $file->fputcsv($row);
                }
            }, 
            $csv_filename,
            array('Content-Type' => 'application/octet-stream')
        );
    }

    /**
     * CSVアップロード（CSV 取り込み）
     * @param file $file
     * @param bool $headless
     * @return rows
     **/
    public function parse($file, $headless = false)
    {
        // Goodby CSVのconfig設定
        $config = new LexerConfig();
        $interpreter = new Interpreter();
        $lexer = new Lexer($config);

        // 文字コード判定 - ファイルの最初から1MB分の文字列から判定
        $str = file_get_contents($file, NULL, NULL, 0, 1024*1024);
        $enc = mb_detect_encoding($str,['UTF-8', 'SJIS-win', 'SJIS', 'JIS', 'Unicode', 'ASCII'], true);
        if ($enc === false) { $enc = 'SJIS-win'; } // 文字コード自動判定できなかったら。。SJISにしとく
        Log::Debug(__CLASS__.':'.__FUNCTION__.' CSV FILE CHARSET : '. $enc);

        // CharsetをUTF-8に変換
        $config->setToCharset("UTF-8");
        $config->setFromCharset($enc);

        // CSVデータをパース
        $rows = array();
        try {
            $interpreter->addObserver(function(array $row) use (&$rows) {
                $rows[] = $row;
            });
            $lexer->parse($file, $interpreter);
        } catch (\Exception $e) {
            throw $e;
        }

        // １行ずつ処理
        $data = array();
        foreach ($rows as $key => $value) {

            // 最初の行はヘッダー - BOMが含まれている場合にうまく削除されていないようなので手動で削除する
            if($key == 0) {
                //$header = $value; 
                $header = preg_replace("/^". pack('H*', 'EFBBBF') ."/", '', $value);
                if (!$headless) continue;    // ヘッダー要らなければ continue
            }

            // 配列化 - ２行目以降はヘッダーに沿って配列に
            //        - 元のCSV列番号[No999]でも取得できるようにしておく
            foreach ($value as $k => $v) {
                if ($headless) { $data[$key]['No'.$k] = $v; }
                else           { $data[$key][$header[$k]] = $v; }
            }
        }

        // ＣＳＶを配列で戻す
        return $data;
    }
}
