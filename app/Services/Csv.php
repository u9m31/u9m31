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
     * @param array $header
     * @return rows
     **/
    public function parse($file)
    {
        // Goodby CSVのconfig設定
        $config = new LexerConfig();
        $interpreter = new Interpreter();
        $lexer = new Lexer($config);

        // CharsetをUTF-8に変換
        $config->setToCharset("UTF-8");
        $config->setFromCharset("sjis-win");

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

            // 最初の行はヘッダー
            if($key == 0) {
                $header = $value;
                continue;
            }

            // 配列化 - ２行目以降はヘッダーに沿って配列に
            foreach ($value as $k => $v) {
                $data[$key][$header[$k]] = $v;
            }
        }

        // ＣＳＶを配列で戻す
        return $data;
    }
}
