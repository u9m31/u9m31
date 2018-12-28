<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Payslip extends Model
{
    // 論理削除有効化
    use SoftDeletes;
    protected $dates = ['deleted_at'];


    // Jsonに追加で含める
    protected $appends = ['name'];


    // カラム暗号化 - 明細情報は暗号化して保存する
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = Crypt::encrypt( serialize($value) );
    }


    // カラム複合化 - 明細情報を取り出すときに複合化する
    public function getDataAttribute($value)
    {
        return unserialize( Crypt::decrypt($value) );
    }


    // ユーザの氏名を取得 -- リレーションできなかった場合は空文字を返す（CSVエラーで関連付かなかった場合）
    public function getNameAttribute()
    {
        $user = $this -> user;
        if ($user) return $user -> name;
        else       return '';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'csv_id',    // CSV_ID
        'line',      // CSV行番号
        'ym',        // 明細年月：yyyymm
        'status',    // 状態：0:有効  9:削除
        'user_id',   // 対象者ID（内部ID）
        'loginid',   // 対象者ログインID
        'data',      // CSV行データ
        'filename',  // ファイル名 - 明細のファイル名を変更する場合に指定	
        'download',  // ユーザダウンロード回数
        'error',     // CSVエラー内容
    ];

    // Json に出力する項目
    protected $visible = [
        'csv_id',    // CSV_ID
        'line',      // CSV行番号
        'ym',        // 明細年月：yyyymm
        'status',    // 状態：0:有効  9:削除
        'user_id',   // 対象者ID（内部ID）
        'loginid',   // 対象者ログインID
        'data',      // CSV行データ
        'filename',  // ファイル名 - 明細のファイル名を変更する場合に指定	
        'download',  // ユーザダウンロード回数
        'error',     // CSVエラー内容
        'name',      // App\User の Name 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    public function user()
    {
        return $this -> belongsTo('App\User', 'user_id', 'id');
    }
}
