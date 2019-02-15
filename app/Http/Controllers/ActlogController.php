<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Actlog;
use App\Facades\Csv;

class ActlogController extends Controller
{
    public function index()
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__);

        $actlog = Actlog::where('user_id', '>', '0')
            -> whereNotNull('route')
            -> orderby('created_at', 'desc')
            -> limit(100)
            -> get();
        return ['data' => $actlog];
    }


    public function download(Request $request)
    {
        Log::Debug(__CLASS__.':'.__FUNCTION__, $request->all());

        // 取得項目設定
        $head = ['created_at', 'route', 'status', 'remote_addr', 'user_agent', 'user_id'];

        // 抽出
        $data = Actlog::select( $head )
            -> where('user_id', '>', '0')
            -> whereNotNull('route')
            -> orderby('created_at', 'desc')
            -> get()
            -> toArray();

        // 自動付与の名前をヘッダーに追加
        $head[] = 'name';

        // CSV DOWNLOAD
        return Csv::download($data, $head, 'test.csv');
    }
}
