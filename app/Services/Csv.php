<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Response;

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
        Log::Debug(__CLASS__.':'.__FUNCTION__);

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
}
