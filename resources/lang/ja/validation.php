<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | such as the size rules. Feel free to tweak each of these messages.
    |
    */

    'accepted'   => ':attributeを承認してください。',
    'active_url' => ':attributeは、有効なURLではありません。',
    'after'      => ':attributeには、:date以降の日付を指定してください。',
    'alpha'      => ':attributeには、アルファベッドのみ使用できます。',
    'alpha_dash' => ":attributeには、英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num'  => ":attributeには、英数字('A-Z','a-z','0-9')が使用できます。",
    'array'      => ':attributeには、配列を指定してください。',
    'before'     => ':attributeには、:date以前の日付を指定してください。',
    'between'    => [
        'numeric' => ':attributeには、:minから、:maxまでの数字を指定してください。',
        'file'    => ':attributeには、:min KBから:max KBまでのサイズのファイルを指定してください。',
        'string'  => ':attributeは、:min文字から:max文字にしてください。',
        'array'   => ':attributeの項目は、:min個から:max個にしてください。',
    ],
    'boolean'              => ":attributeには、'true'か'false'を指定してください。",
    'confirmed'            => ':attributeと:attribute確認が一致しません。',
    'date'                 => ':attributeは、正しい日付ではありません。',
    'date_format'          => ":attributeの形式は、':format'と合いません。",
    'different'            => ':attributeと:otherには、異なるものを指定してください。',
    'digits'               => ':attributeは、:digits桁にしてください。',
    'digits_between'       => ':attributeは、:min桁から:max桁にしてください。',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => ':attributeは、有効なメールアドレス形式で指定してください。',
    'exists'               => '選択された:attributeは、有効ではありません。',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => ':attributeは必須です。',
    'image'                => ':attributeには、画像を指定してください。',
    'in'                   => '選択された:attributeは、有効ではありません。',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => ':attributeには、整数を指定してください。',
    'ip'                   => ':attributeには、有効なIPアドレスを指定してください。',
    'json'                 => ':attributeには、有効なJSON文字列を指定してください。',
    'max'                  => [
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'file'    => ':attributeには、:max KB以下のファイルを指定してください。',
        'string'  => ':attributeは、:max文字以下にしてください。',
        'array'   => ':attributeの項目は、:max個以下にしてください。',
    ],
    'mimes'                => ':attributeには、:valuesタイプのファイルを指定してください。',
    'mimetypes'            => ':attributeには、:valuesタイプのファイルを指定してください。',
    'min'                  => [
        'numeric' => ':attributeには、:min以上の数字を指定してください。',
        'file'    => ':attributeには、:min KB以上のファイルを指定してください。',
        'string'  => ':attributeは、:min文字以上にしてください。',
        'array'   => ':attributeの項目は、:max個以上にしてください。',
    ],
    'not_in'               => '選択された:attributeは、有効ではありません。',
    'numeric'              => ':attributeには、数字を指定してください。',
    'present'              => 'The :attribute field must be present.',
    'regex'                => ':attributeには、有効な正規表現を指定してください。',
    'required'             => ':attributeは、必ず指定してください。',
    'required_if'          => ':otherが:valueの場合、:attributeを指定してください。',
    'required_unless'      => ':otherが:value以外の場合、:attributeを指定してください。',
    'required_with'        => ':valuesが指定されている場合、:attributeも指定してください。',
    'required_with_all'    => ':valuesが全て指定されている場合、:attributeも指定してください。',
    'required_without'     => ':valuesが指定されていない場合、:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを指定してください。',
    'same'                 => ':attributeと:otherが一致しません。',
    'size'                 => [
        'numeric' => ':attributeには、:sizeを指定してください。',
        'file'    => ':attributeには、:size KBのファイルを指定してください。',
        'string'  => ':attributeは、:size文字にしてください。',
        'array'   => ':attributeの項目は、:size個にしてください。',
    ],
    'string'   => ':attributeには、文字を指定してください。',
    'timezone' => ':attributeには、有効なタイムゾーンを指定してください。',
    'unique'   => '指定の:attributeは既に使用されています。',
    'uploaded' => ':attributeがアップロードできませんでした',
    'url'      => ':attributeは、有効なURL形式で指定してください。',


    'validate_white_list'   => '代理店のホワイトドメイン以外指定できません。',
    'validate_require'      => '必ず指定してください。',
    'validate_url'          => '有効なURL形式で指定してください。',
    'validate_url_secure'   => 'HTTPSのURLを入力してください',
    'validate_max'          => 'は:max文字以下にしてください。',
    'validate_min'          => 'は:min文字以上にしてください。',
    'validate_phone'        => '電話番号は確認できません。',
    'validate_number'       => '数字を入力してください',
    'validate_carousel_buton_number' => 'カルーセルにおけるボタン数を統一してください。',
    'validate_incomplete_field_carousel' => '画像URL・サブタイトル・ボタンのうちいずれか一つは必須です。',

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
    'imagemap_dimension_type' => '画像のサイズは:widthpixel×:heightpixel、拡張子は:typeのみアップロード可能です。',
//    'custom' => [
//        'attribute-name' => [
//            'rule-name' => 'custom-message',
//        ],
//    ],

    'custom' => [
        'channel_type' => [
            'required'  => 'The attribute is required.',
            'numeric'   => 'Trường yêu cầu dạng số',
        ],
        'fb_page_id' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_app_id' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_app_secret' => [
            'required'  => 'The attribute is required.',
        ],
        'fb_page_access_token' => [
            'required'  => 'The attribute is required.',
        ],
        'custom_email' => [
            'required' => 'メールアドレスは、必ず指定してください。',
        ],
        'custom_password' => [
            'required' => 'パスワードは、必ず指定してください。',
        ],
        'terms_of_use' => [
            'required' => '利用規約に同意する必要があります。',
        ]
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
        'page_id' => 'ページ',
        'bot_name' => 'ボット名',
        'email' => 'メールアドレス',
        'name' => '名前',
        'variable_name' => '変数名',
        'group_name' => 'グループ名',
        'company_name' => '企業名',
        'password' => 'パスワード',
        'password_confirmation' => '確認パスワード',
        'scenario_name' => 'シナリオ名',
        'scenario' => 'シナリオ',
        'title' => 'タイトル',
        'type' => 'タイプ',
        'url' => 'URL',
        'greeting_message' => 'Greeting Message',
        'upload_file' => 'ファイルアップロード',
        'time' => '開始日時',
        'notification_name' => 'プッシュメッセージ名',
        'api_name' => 'API名',
	    'param.key.*' => 'キー',
	    'max_bot_number' => 'ボット数の上限',
	    'max_user_number' => 'ユーザー数の上限',
        'white_list_domain' => 'ホワイトリストドメイン',
        'file' => 'ファイル',
	    'channel_id' => 'チャネルID',
        'app_id' => 'アプリID',
	    'channel_secret' => 'Channel secret',
	    'channel_access_token' => 'Channel access token',
        'page_access_token' => 'ページアクセストークン',
        'timezone' => 'タイムゾーン',
        'language' => '言語',
        'to'       => 'To',
        'content'  => 'メール内容',
        'subject'  => '件名',
        'transfer_from_bot_name' => 'コピー元ボット',
        'transfer_to_bot' => 'コピー先ボット',
        'api_type' => 'API種類',
        'action'   => 'アクション',
        'email_name' => ' テンプレート名',
        'template_name' => 'テンプテート名',
        'domain' => 'ドメイン',
        'address' => '住所',
        'tel' => '電話番号',
        'person_in_charge' => '担当者',
        'business_segments' => '事業区分',
        'zip_code' => '郵便番号',
        'card_number'   => 'カード番号',
        'month_select' => '月',
        'year_select' => '年',
        'cvc' => 'セキュリティコード',
        'terms_of_use' => '利用規約',
        'agree' => '同意する',
        'nlp_app_id' => 'App Id',
        'culture' => 'Culture',
        'from_name' => 'From name',
        'from_email' => 'From',
        'option' => 'オプション',
        'scenario_connect' => 'シナリオ連携',
        'webhook_token' => 'Webhookトークン',
        'api_token' => 'APIトークン',
        'sheet_id' => ' Spreadsheet Id',
        'detail' => '詳細',
        'start_date' => '掲載日付',
        'yahoo_url' => 'サンクスページURL',
        'gateway_name' => '決済ゲートウェイ名',
        'pgcard_shop_id' => 'ショップID',
        'pgcard_shop_pass' => 'ショップパスワード',
        'pgcard_site_id' => 'サイトID',
        'pgcard_site_pass' => 'サイトパスワード',
        'api_url' => 'API Url',
    ],

];
