<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Actlog extends Model
{
    // 更新日時記録無効化
    const UPDATED_AT = null;

    // Jsonに追加で含める
    protected $appends = ['name'];

    // ユーザの氏名を取得 -- リレーションできなかった場合は空文字を返す
    public function getNameAttribute()
    {
        $user = $this -> user;
        if ($user) return $user -> name;
        else       return '';
    }

    // カラム暗号化 - 要求内容は暗号化して保存する
    public function setDataAttribute($value)
    {
        if ($value) {
            $this->attributes['data'] = Crypt::encrypt( serialize($value) );
        }
    }

    // カラム複合化 - 要求内容を取り出すときに複合化する
    public function getDataAttribute($value)
    {
        if ($value) {
            return unserialize( Crypt::decrypt($value) );
        } else {
            return $value;
        }
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'route',
        'url',
        'method',
        'status',
        'data',
        'remote_addr',
        'user_agent',
    ];

    // Json に出力する項目
    protected $visible = [
        'user_id',
        'route',
        'url',
        'method',
        'status',
        'data',
        'remote_addr',
        'user_agent',
        'created_at',
        'name',
    ];

    public function user()
    {
        return $this -> belongsTo('App\User', 'user_id', 'id');
    }
}
