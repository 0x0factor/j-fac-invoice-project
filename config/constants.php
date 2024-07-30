<?php

return [
    // Version
    'Version' => '2.6.5',

    // Pagination lines per page
    'Paginate' => [
        'LinesPerPage' => 20,
    ],

    // Maximum form lines
    'MaxFormLine' => 10,

    // Cover page max form lines
    'CoverpageMaxFormLine' => 9,

    // ONLY_FULL_GROUP_BY flag for MySQL5.7
    'onlyFullGroupByDisable' => true,

    // PDF address display settings
    'PdfForceOverwriteChargeAddressEvenEmpty' => false,

    // Prefecture Codes
    'PrefectureCode' => [
        0 => '選択してください',
        1 => '北海道', 2 => '青森県', 3 => '岩手県', 4 => '宮城県', 5 => '秋田県',
        6 => '山形県', 7 => '福島県', 8 => '茨城県', 9 => '栃木県', 10 => '群馬県',
        11 => '埼玉県', 12 => '千葉県', 13 => '東京都', 14 => '神奈川県', 15 => '新潟県',
        16 => '富山県', 17 => '石川県', 18 => '福井県', 19 => '山梨県', 20 => '長野県',
        21 => '岐阜県', 22 => '静岡県', 23 => '愛知県', 24 => '三重県', 25 => '滋賀県',
        26 => '京都府', 27 => '大阪府', 28 => '兵庫県', 29 => '奈良県', 30 => '和歌山県',
        31 => '鳥取県', 32 => '島根県', 33 => '岡山県', 34 => '広島県', 35 => '山口県',
        36 => '徳島県', 37 => '香川県', 38 => '愛媛県', 39 => '高知県', 40 => '福岡県',
        41 => '佐賀県', 42 => '長崎県', 43 => '熊本県', 44 => '大分県', 45 => '宮崎県',
        46 => '鹿児島県', 47 => '沖縄県',
    ],

    // Status Codes
    'StatusCode' => [
        0 => '有効',
        1 => '無効',
    ],

    // Seal Status Codes
    'SealCode' => [
        0 => '登録済み',
        1 => '未登録',
    ],

    // Issued Status Codes
    'IssuedStatCode' => [
        1 => '作成済み',
        0 => '下書き',
        2 => '破棄',
        3 => '未入金',
        4 => '入金済み',
        5 => '入金対象外',
    ],

    // Excise Codes
    'ExciseCode' => [
        2 => '外税',
        1 => '内税',
        3 => '非課税',
    ],

    // Fraction Codes
    'FractionCode' => [
        1 => '切り捨て',
        0 => '切り上げ',
        2 => '四捨五入',
    ],

    // Tax Fraction Timing Codes
    'TaxFractionTimingCode' => [
        0 => '帳票単位',
        1 => '明細の一行単位',
    ],

    // Account Type Codes
    'AccountTypeCode' => [
        '' => '選択ください',
        0 => '普通',
        1 => '当座',
    ],

    // Discount Codes
    'DiscountCode' => [
        0 => '％',
        1 => '円',
        2 => '設定しない',
    ],

    // Decimal Codes
    'DecimalCode' => [
        0 => '桁なし',
        1 => '小数点第一位',
        2 => '小数点第二位',
        3 => '小数点第三位',
    ],

    // Send Methods
    'SendMethod' => [
        0 => '郵送',
        1 => 'FAX',
    ],

    // Seal Methods
    'SealMethod' => [
        0 => 'ファイルをアップロード',
        1 => '文字列から印鑑を作成',
    ],

    // Action Codes
    'ActionCode' => [
        0 => 'ログイン',
        1 => 'ログアウト',
        2 => '見積書作成',
        3 => '見積書更新',
        4 => '見積書削除',
        5 => '請求書作成',
        6 => '請求書更新',
        7 => '請求書削除',
        8 => '納品書作成',
        9 => '納品書更新',
        10 => '納品書削除',
        11 => '合計請求書作成',
        12 => '合計請求書更新',
        13 => '合計請求書削除',
        14 => '定期請求書雛形作成',
        15 => '定期請求書雛形更新',
        16 => '定期請求書雛形削除',
        17 => '定期請求書作成',
    ],

    // Payment Month
    'PaymentMonth' => [
        '' => '選択ください',
        0 => '当月',
        1 => '翌月',
        2 => '翌々月',
        3 => '3ヶ月後',
        4 => '4ヶ月後',
        5 => '5ヶ月後',
        6 => '6ヶ月後',
    ],

    // Color Codes
    'ColorCode' => [
        0 => ['name' => '黒', 'code' => ['r' => '00', 'g' => '00', 'b' => '00']],
        1 => ['name' => '青', 'code' => ['r' => '00', 'g' => '00', 'b' => 'FF']],
        2 => ['name' => '赤', 'code' => ['r' => 'FF', 'g' => '00', 'b' => '00']],
        3 => ['name' => '緑', 'code' => ['r' => '00', 'g' => 'FF', 'b' => '00']],
    ],

    // Direction Codes
    'DirectionCode' => [
        0 => '縦',
        1 => '横',
    ],

    // Authority Codes
    'AuthorityCode' => [
        1 => '自分のデータのみ',
        2 => '他人のデータ閲覧可能',
        3 => '他人のデータ編集可能',
    ],

    // Line Attributes
    'LineAttribute' => [
        0 => '通常',
        1 => '小計',
        2 => 'グループ小計',
        3 => '割引(円)',
        4 => '割引(％)',
        5 => '備考',
        8 => '改ページ',
    ],

    // Tax Classes
    'TaxClass' => [
        "0" => '------',
        "2" => '外税(5%)',
        "1" => '内税(5%)',
        "82" => '外税(8%)',
        "81" => '内税(8%)',
        "92" => '軽減外税(8%)',
        "91" => '軽減内税(8%)',
        "102" => '外税(10%)',
        "101" => '内税(10%)',
        "3" => '非課税',
    ],

    // Tax Rates
    'TaxRates' => [
        2 => 0.05,
        1 => 0.05,
        82 => 0.08,
        81 => 0.08,
        92 => 0.08,
        91 => 0.08,
        102 => 0.10,
        101 => 0.10,
        3 => 0,
    ],

    // Tax Operation Dates
    'TaxOperationDate' => [
        5 => ["start" => "1997-04-01", "end" => "2014-03-31"],
        8 => ["start" => "2014-04-01", "end" => "2019-09-30"],
        10 => ["start" => "2019-10-01", "end" => null],
    ],

    // Mail Status Codes
    'MailStatusCode' => [
        0 => '確認待ち',
        1 => '確認済み',
        2 => '修正願い',
    ],

    // Mail Protocol Codes
    'MailProtocolCode' => [
        0 => 'SMTP',
        1 => 'SMTP_AUTH',
    ],

    // SMTP Security Codes
    'SmtpSecurityCode' => [
        0 => 'なし',
        1 => 'SSL',
        2 => 'TLS',
    ],

    // Edit Status Protocol Codes
    'Edit_StatProtocolCode' => [
        0 => '簡易',
        1 => '詳細',
    ],

    // Honorific Codes
    'HonorCode' => [
        0 => '御中',
        1 => '様',
        2 => 'その他',
    ],

    // Seal Display Settings
    'SealFlg' => [
        1 => '表示',
        0 => '非表示',
    ],

    // Mail Login Term (days)
    'MailLoginTerm' => 7,

    // Image Size Limit
    'ImageSize' => 1024 * 1024,

    // Item Error Codes
    'ItemErrorCode' => [
        'ITEM' => ['NO' => [], 'FLAG' => 0],
        'ITEM_NO' => ['NO' => [], 'FLAG' => 0],
        'QUANTITY' => ['NO' => [], 'FLAG' => 0],
        'UNIT' => ['NO' => [], 'FLAG' => 0],
        'UNIT_PRICE' => ['NO' => [], 'FLAG' => 0],
    ],

    // Mail Settings
    'Mail' => [
        'From' => '抹茶請求書',
        'Subject' => [
            'PassEdit' => '【抹茶請求書】パスワード再設定のお知らせ',
        ],
        'Txt' => [
            'PassEdit' => "▼こちらのURLからパスワードを再設定してください。",
        ],
    ],

    // Form ID for Serial Numbers
    'FormID' => [
        'Quote' => 0,
        'Delivery' => 1,
        'Bill' => 2,
        'TotalBill' => 3,
        'Receipt' => 4,
        'Regularbill' => 5,
    ],

    // Numbering Format
    'NumberingFormat' => [
        0 => '通し番号',
        1 => '日付形式',
    ],

    // Serial Settings
    'Serial' => [
        0 => '連番を設定',
        1 => '設定しない',
    ],

    // Image Upload Directory
    'ImgUploadDir' => public_path('cms'),

    // Search Result Session Deletion Timing
    'SessionDeleteAlways' => 0,
    'SessionDeleteNever' => 1,
    'SessionDeleteOnlyMenu' => 2,

    'SearchBoxSessionMode' => env('SEARCH_BOX_SESSION_MODE', 2),
];


/**
 * json_encodeの代替(PHP5.2未満の場合)
 */


 if (!function_exists('json_encode')) {
    function json_encode($array)
    {
        if (!is_array($array)) {
            return _js_encValue($array);
        }

        $assoc = false;
        if (array_diff(array_keys($array), range(0, count($array) - 1))) {
            $assoc = true;
        }

        $data = [];
        foreach ($array as $key => $value) {
            if ($assoc) {
                if (!is_numeric($key)) {
                    $key = preg_replace('/(["\\\])/u', '\\\\$1', $key);
                }
                $key = '"' . $key . '"';
            }
            $value = _js_encValue($value);
            $data[] = ($assoc ? "$key:$value" : $value);
        }
        return $assoc ? '{' . implode(',', $data) . '}' : '[' . implode(',', $data) . ']';
    }

    function _js_encValue($value)
    {
        if (is_array($value)) {
            return json_encode($value);
        } elseif (is_bool($value)) {
            return $value ? 'true' : 'false';
        } elseif ($value === null) {
            return 'null';
        } elseif (is_string($value)) {
            return '"' . _js_toU16Entities($value) . '"';
        } elseif (is_numeric($value)) {
            return $value;
        }
        return '"' . $value . '"';
    }

    function _js_toU16Entities($string)
    {
        $len = mb_strlen($string, 'UTF-8');
        $str = '';
        $strAry = preg_split('//u', $string);
        for ($idx = 0, $len = count($strAry); $idx < $len; $idx++) {
            $code = $strAry[$idx];
            if ($code === '') continue;
            if (strlen($code) > 1) {
                $hex = bin2hex(mb_convert_encoding($code, 'UTF-16', 'UTF-8'));
                if (strlen($hex) == 8) { // surrogate pair
                    $str .= vsprintf('\u%04s\u%04s', str_split($hex, 4));
                } else {
                    $str .= sprintf('\u%04s', $hex);
                }
            } else {
                switch ($code) {
                    case '"':
                    case '/':
                    case '\\':
                        $code = '\\' . $code;
                }
                $str .= $code;
            }
        }
        $str = str_replace(["\r\n", "\r", "\n"], ['\r\n', '\r', '\n'], $str);
        return $str;
    }
}
