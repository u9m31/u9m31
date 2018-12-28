<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class CsvPayslip extends Model
{
    // 論理削除有効化
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    // カラム暗号化 - ヘッダー情報は暗号化して保存する
    public function setHeaderAttribute($value)
    {
        $this->attributes['header'] = Crypt::encrypt(serialize($value));
    }


    // カラム複合化
    public function getHeaderAttribute($value)
    {
        return unserialize( Crypt::decrypt($value) );
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ym',        // 明細年月：yyyymm
        'status',    // 状態：0:非公開  1:公開
        'filename',  // CSVファイル名
        'header',    // CSVヘッダー
        'line',      // 対象者数
        'error',     // CSVエラー行数
        'published_at',  // 公開日時
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];
}
