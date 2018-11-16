<?php  // resources/lang/ja/validation.php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attributeを承認してください。',
    'active_url'           => ':attributeは正しいURLではありません。',
    'after'                => ':attributeは:date以降の日付にしてください。',
    'after_or_equal'       => ':attributeは:date以降の日付にしてください。',          // 追加
    'alpha'                => ':attributeは英字のみにしてください。',
    'alpha_dash'           => ':attributeは英数字とハイフンのみにしてください。',
    'alpha_num'            => ':attributeは英数字のみにしてください。',
    'array'                => ':attributeは配列にしてください。',
    'before'               => ':attributeは:date以前の日付にしてください。',
    'before_or_equal'      => ':attributeは:date以前の日付にしてください。',          // 追加
    'between'              => [
        'numeric' => ':attributeは:min?:maxまでにしてください。',
        'file'    => ':attributeは:min?:max KBまでのファイルにしてください。',
        'string'  => ':attributeは:min?:max文字にしてください。',
        'array'   => ':attributeは:min?:max個までにしてください。',
    ],
    'boolean'              => ':attributeはtrueかfalseにしてください。',
    'confirmed'            => ':attributeは確認用項目と一致していません。',
    'date'                 => ':attributeは正しい日付ではありません。',
    'date_format'          => ':attributeは":format"書式と一致していません。',
    'different'            => ':attributeは:otherと違うものにしてください。',
    'digits'               => ':attributeは:digits桁にしてください',
    'digits_between'       => ':attributeは:min?:max桁にしてください。',
    'dimensions'           => ':attributeは画像サイズが不正です。',                   // 追加
    'distinct'             => ':attributeは重複しています。',                         // 追加
    'email'                => ':attributeを正しいメールアドレスにしてください。',
    'exists'               => '選択された:attributeは正しくありません。',
    'file'                 => ':attributeはファイルにしてください。',                 // 追加
    'filled'               => ':attributeは必須です。',
    'gt'                   => [                    
        'numeric' => ':attributeは:value以上にしてください。',                        // 追加
        'file'    => ':attributeは:value KB以上のファイルにしてください。.',          // 追加
        'string'  => ':attributeは:value文字以上にしてください。',                    // 追加
        'array'   => ':attributeは:value個以上にしてください。',                      // 追加
    ],
    'gte'                  => [                                                      
        'numeric' => ':attributeは:value以上にしてください。',                        // 追加
        'file'    => ':attributeは:value KB以上のファイルにしてください。.',          // 追加
        'string'  => ':attributeは:value文字以上にしてください。',                    // 追加
        'array'   => ':attributeは:value個以上にしてください。',                      // 追加
    ],
    'image'                => ':attributeは画像にしてください。',
    'in'                   => '選択された:attributeは正しくありません。',
    'in_array'             => 'The :attribute field does not exist in :other.',       // 追加
    'integer'              => ':attributeは整数にしてください。',
    'ip'                   => ':attributeを正しいIPアドレスにしてください。',
    'ipv4'                 => ':attributeを正しいIPv4アドレスにしてください。',       // 追加
    'ipv6'                 => ':attributeを正しいIPv6アドレスにしてください。',       // 追加
    'json'                 => ':attributeを正しいJSON形式にしてください。',           // 追加
    'lt'                   => [                                                
        'numeric' => ':attributeは:value より小さくなければなりません。',             // 追加
        'file'    => ':attributeは:value KBより小さくなければなりません。',           // 追加
        'string'  => ':attributeは:value 文字以下でなければなりません。',             // 追加
        'array'   => ':attributeは:value 項目以下でなければなりません。',             // 追加
    ],
    'lte'                  => [    
        'numeric' => ':attributeは:value以下にしてください。',                        // 追加
        'file'    => ':attributeは:value KB以下のファイルにしてください。.',          // 追加
        'string'  => ':attributeは:value文字以下にしてください。',                    // 追加
        'array'   => ':attributeは:value個以下にしてください。',                      // 追加
    ],
    'max'                  => [
        'numeric' => ':attributeは:max以下にしてください。',
        'file'    => ':attributeは:max KB以下のファイルにしてください。.',
        'string'  => ':attributeは:max文字以下にしてください。',
        'array'   => ':attributeは:max個以下にしてください。',
    ],
    'mimes'                => ':attributeは:valuesタイプのファイルにしてください。',
    'mimetypes'            => ':attributeは:valuesタイプのファイルにしてください。',  // 追加
    'min'                  => [
        'numeric' => ':attributeは:min以上にしてください。',
        'file'    => ':attributeは:min KB以上のファイルにしてください。.',
        'string'  => ':attributeは:min文字以上にしてください。',
        'array'   => ':attributeは:min個以上にしてください。',
    ],
    'not_in'               => '選択された:attributeは正しくありません。',
    'not_regex'            => ':attributeの書式が正しくありません。',                 // 追加
    'numeric'              => ':attributeは数字にしてください。',
    'present'              => ':attributeは存在する必要があります。',                 // 追加
    'regex'                => ':attributeの書式が正しくありません。',
    'required'             => ':attributeは必須です。',
    'required_if'          => ':otherが:valueの時、:attributeは必須です。',
    'required_unless'      => ':otherが:valueにないの時、:attributeは必須です。',     // 追加
    'required_with'        => ':valuesが存在する時、:attributeは必須です。',
    'required_with_all'    => ':valuesが存在する時、:attributeは必須です。',
    'required_without'     => ':valuesが存在しない時、:attributeは必須です。',
    'required_without_all' => ':valuesが存在しない時、:attributeは必須です。',
    'same'                 => ':attributeと:otherは一致していません。',
    'size'                 => [
        'numeric' => ':attributeは:sizeにしてください。',
        'file'    => ':attributeは:size KBにしてください。.',
        'string'  => ':attribute:size文字にしてください。',
        'array'   => ':attributeは:size個にしてください。',
    ],
    'string'               => ':attributeは文字列にしてください。',
    'timezone'             => ':attributeは正しいタイムゾーンを指定してください。',
    'unique'               => ':attributeは既に存在します。',
    'uploaded'             => ':attributeのアップロードに失敗しました。',             // 追加
    'url'                  => ':attributeを正しい書式にしてください。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
        'pass' => [
            'regex' => '必ず英文字と数字、記号を１文字含むこと',
        ],
        'role' => [
            'in' => ':attributeは 5 か 10 を指定してください',
        ],
        'csvfile' => [
            'required' => 'ファイルを選択してください。',
            'file' => 'ファイルアップロードに失敗しました。',
            'mimetypes' => 'ファイル形式が不正です(CSVファイルを選択してください）',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'name' => '名前',
        'loginid' => 'ログインＩＤ',
        'pass' => 'パスワード',
        'role' => '権限',
    ],

];
