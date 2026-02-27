<?php
return [

    /*
    |--------------------------------------------------------------------------
    | バリデーション言語のライン
    |--------------------------------------------------------------------------
    |
    | 以下の言語ラインには、バリデータクラスで使用されるデフォルトのエラーメッセージが含まれています。
    | これらのルールのいくつかには、サイズルールなど、複数のバージョンがあります。
    | それぞれのメッセージを自由に調整してください。
    |
    */

    'accepted' => ':attribute は受け入れる必要があります。',
    'accepted_if' => ':other が :value の場合、:attribute を受け入れる必要があります。',
    'active_url' => ':attribute は有効なURLではありません。',
    'after' => ':attribute は :date より後の日付でなければなりません。',
    'after_or_equal' => ':attribute は :date 以降の日付でなければなりません。',
    'alpha' => ':attribute は文字のみを含む必要があります。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを含む必要があります。',
    'alpha_num' => ':attribute は文字と数字のみを含む必要があります。',
    'array' => ':attribute は配列である必要があります。',
    'ascii' => ':attribute は単一バイトの英数字文字と記号のみを含む必要があります。',
    'before' => ':attribute は :date より前の日付でなければなりません。',
    'before_or_equal' => ':attribute は :date 以前の日付でなければなりません。',
    'between' => [
        'array' => ':attribute は :min から :max 個の項目を持つ必要があります。',
        'file' => ':attribute は :min から :max キロバイトの間である必要があります。',
        'numeric' => ':attribute は :min から :max の間である必要があります。',
        'string' => ':attribute は :min から :max 文字の間である必要があります。',
    ],
    'boolean' => ':attribute は true または false である必要があります。',
    'confirmed' => ':attribute の確認が一致しません。',
    'current_password' => 'パスワードが正しくありません。',
    'date' => ':attribute は有効な日付ではありません。',
    'date_equals' => ':attribute は :date に等しい日付である必要があります。',
    'date_format' => ':attribute はフォーマット :format と一致しません。',
    'decimal' => ':attribute は :decimal 小数点を持つ必要があります。',
    'declined' => ':attribute は拒否する必要があります。',
    'declined_if' => ':other が :value の場合、:attribute を拒否する必要があります。',
    'different' => ':attribute と :other は異なる必要があります。',
    'digits' => ':attribute は :digits 桁である必要があります。',
    'digits_between' => ':attribute は :min から :max 桁の間である必要があります。',
    'dimensions' => ':attribute は無効な画像サイズです。',
    'distinct' => ':attribute に重複した値があります。',
    'doesnt_end_with' => ':attribute は次のいずれかで終わってはいけません: :values。',
    'doesnt_start_with' => ':attribute は次のいずれかで始まってはいけません: :values。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'ends_with' => ':attribute は次のいずれかで終わる必要があります: :values。',
    'enum' => '選択された :attribute は無効です。',
    'exists' => '選択された :attribute は無効です。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute に値を入力してください。',
    'gt' => [
        'array' => ':attribute は :value 個より多い項目を持つ必要があります。',
        'file' => ':attribute は :value キロバイトより大きい必要があります。',
        'numeric' => ':attribute は :value より大きい必要があります。',
        'string' => ':attribute は :value 文字より大きい必要があります。',
    ],
    'gte' => [
        'array' => ':attribute は :value 個以上の項目を持つ必要があります。',
        'file' => ':attribute は :value キロバイト以上である必要があります。',
        'numeric' => ':attribute は :value 以上である必要があります。',
        'string' => ':attribute は :value 文字以上である必要があります。',
    ],
    'image' => ':attribute は画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute は :other に存在しません。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なJSON文字列である必要があります。',
    'lowercase' => ':attribute は小文字である必要があります。',
    'lt' => [
        'array' => ':attribute は :value 個より少ない項目を持つ必要があります。',
        'file' => ':attribute は :value キロバイトより小さい必要があります。',
        'numeric' => ':attribute は :value より小さい必要があります。',
        'string' => ':attribute は :value 文字より小さい必要があります。',
    ],
    'lte' => [
        'array' => ':attribute は :value 個以下の項目を持つ必要があります。',
        'file' => ':attribute は :value キロバイト以下である必要があります。',
        'numeric' => ':attribute は :value 以下である必要があります。',
        'string' => ':attribute は :value 文字以下である必要があります。',
    ],
    'mac_address' => ':attribute は有効なMACアドレスである必要があります。',
    'max' => [
        'array' => ':attribute は :max 個以上の項目を持つことはできません。',
        'file' => ':attribute は :max キロバイトを超えることはできません。',
        'numeric' => ':attribute は :max を超えることはできません。',
        'string' => ':attribute は :max 文字を超えることはできません。',
    ],
    'max_digits' => ':attribute は :max 桁を超えることはできません。',
    'mimes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'mimetypes' => ':attribute は次のタイプのファイルである必要があります: :values。',
    'min' => [
        'array' => ':attribute は少なくとも :min 個の項目を持つ必要があります。',
        'file' => ':attribute は少なくとも :min キロバイトである必要があります。',
        'numeric' => ':attribute は少なくとも :min である必要があります。',
        'string' => ':attribute は少なくとも :min 文字である必要があります。',
    ],
    'min_digits' => ':attribute は少なくとも :min 桁を持つ必要があります。',
    'missing' => ':attribute が欠落しています。',
    'missing_if' => ':other が :value の場合、:attribute が欠落しています。',
    'missing_unless' => ':other が :value でない限り、:attribute が欠落しています。',
    'missing_with' => ':values が存在する場合、:attribute が欠落しています。',
    'missing_with_all' => ':values が存在する場合、:attribute が欠落しています。',
    'multiple_of' => ':attribute は :value の倍数である必要があります。',
    'not_in' => '選択された :attribute は無効です。',
    'not_regex' => ':attribute のフォーマットが無効です。',
    'numeric' => ':attribute は数値である必要があります。',
    'password' => [
        'letters' => ':attribute は少なくとも1つの文字を含む必要があります。',
        'mixed' => ':attribute は少なくとも1つの大文字と1つの小文字の文字を含む必要があります。',
        'numbers' => ':attribute は少なくとも1つの数字を含む必要があります。',
        'symbols' => ':attribute は少なくとも1つの記号を含む必要があります。',
        'uncompromised' => '指定された :attribute はデータリークに現れました。別の :attribute を選択してください。',
        'regex' => 'パスワードは半角英数字と記号のみで入力してください。',
    ],
    'present' => ':attribute が存在する必要があります。',
    'prohibited' => ':attribute は禁止されています。',
    'prohibited_if' => ':other が :value の場合、:attribute は禁止されています。',
    'prohibited_unless' => ':other が :values に含まれていない限り、:attribute は禁止されています。',
    'prohibits' => ':attribute は :other が存在する場合に禁止されています。',
    'regex' => ':attribute のフォーマットが無効です。',
    'required' => ':attribute は必須です。',
    'required_array_keys' => ':values に対する :attribute が必要です。',
    'required_if' => ':other が :value の場合、:attribute は必須です。',
    'required_if_accepted' => ':other が受け入れられた場合、:attribute は必須です。',
    'required_unless' => ':other が :values に含まれていない場合、:attribute は必須です。',
    'required_with' => ':values が存在する場合、:attribute は必須です。',
    'required_with_all' => ':values が存在する場合、:attribute は必須です。',
    'required_without' => ':values が存在しない場合、:attribute は必須です。',
    'required_without_all' => ':values のいずれも存在しない場合、:attribute は必須です。',
    'same' => ':attribute と :other は一致する必要があります。',
    'size' => [
        'array' => ':attribute は :size 個の項目を含む必要があります。',
        'file' => ':attribute は :size キロバイトである必要があります。',
        'numeric' => ':attribute は :size である必要があります。',
        'string' => ':attribute は :size 文字である必要があります。',
    ],
    'starts_with' => ':attribute は次のいずれかで始まる必要があります: :values。',
    'string' => ':attribute は文字列である必要があります。',
    'timezone' => ':attribute は有効なタイムゾーンである必要があります。',
    'unique' => ':attribute は既に使用されています。',
    'uploaded' => ':attribute のアップロードに失敗しました。',
    'uppercase' => ':attribute は大文字である必要があります。',
    'url' => ':attribute は有効なURLである必要があります。',
    'ulid' => ':attribute は有効なULIDである必要があります。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション言語のライン
    |--------------------------------------------------------------------------
    |
    | ここでは、属性ルールに「attribute.rule」という規則を使用して、
    | 特定の属性ルールに対するカスタム言語ラインを指定できます。
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性
    |--------------------------------------------------------------------------
    |
    | 次の言語ラインは、属性プレースホルダーを「Eメールアドレス」など、
    | より読みやすいものに置き換えます。「email」の代わりに「Eメールアドレス」などを使用します。
    |
    */

    'attributes' => [],

];
