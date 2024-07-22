<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Configuration extends Model
{
    protected $table = 'T_CONFIG';
    protected $primaryKey = 'CON_ID';
    public $timestamps = false;

    // Define validation rules if necessary

    function index_select($_con_ID = null)
    {
        $_con_ID = is_null($_con_ID) ? 1 : $_con_ID;

        $result = DB::table($this->table)->find($_con_ID);
        

        // Return null if record does not exist
        if (! $result)
            return null;

        return (array) $result;
    }

    function index_set_data($_param, &$_error = null, $_state = null)
    {
        // Set INSERT_DATE and LAST_UPDATE
        if ($_state == 'new') {
            $_param['INSERT_DATE'] = date("Y-m-d H:i:s");
            $_param['CON_ID'] = 1;
        }
        $_param['LAST_UPDATE'] = date("Y-m-d H:i:s");

        // Start transaction
        DB::beginTransaction();

        try {
            // Perform validation and handle errors
            $_error = $this->validateData($_param);
            $error = 0;

            // Additional business logic validation
            if ($_param['STATUS'] != 0) {
                if (empty($_param['HOST'])) {
                    $_error['HOST'] = "SMTPサーバは必須です。";
                    $error = 1;
                }

                if (empty($_param['PORT'])) {
                    $_error['PORT'] = "ポート番号は必須です。";
                    $error = 1;
                }

                if (! isset($_param['PROTOCOL']) || $_param['PROTOCOL'] == '') {
                    $_error['PROTOCOL'] = "プロトコルが選択されていません。";
                    $error = 1;
                } else {
                    if ($_param['PROTOCOL'] == 1) {
                        if (empty($_param['USER'])) {
                            $_error['USER'] = "SMTPユーザは必須です。";
                            $error = 1;
                        }
                        if (empty($_param['PASS'])) {
                            $_error['PASS'] = "SMTPパスワードは必須です。";
                            $error = 1;
                        }
                    }
                }

                if (! isset($_param['SECURITY']) || $_param['SECURITY'] == '') {
                    $_error['SECURITY'] = "SMTPセキュリティが選択されていません。";
                    $error = 1;
                }
            }

            // Save to database
            if ($error == 0) {
                $this->where('CON_ID', $_param['CON_ID'])->update($this->permit_params($_param));
                DB::commit();
                $_param['CON_ID'] = $this->getInsertID();
            } else {
                DB::rollback();
                return $_param;
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $_param;
        }

        return $_param;
    }

    // Validate data based on the defined rules
    private function validateData($data)
    {
        $validator = \Validator::make($data, [
            'FROM' => ['nullable', 'max:256', 'email'],
            'FROM_NAME' => ['required', 'max:256'],
            'PORT' => ['nullable', 'numeric', 'between:0,65536'],
            'USER' => ['nullable', 'max:30'],
            'PASS' => ['nullable', 'regex:/^[a-zA-Z0-9]*$/'],
            'HOST' => ['nullable', 'max:100', 'domain'],
        ], [
            'FROM.max' => 'メールアドレスが長すぎます',
            'FROM.email' => '有効なメールアドレスではありません',
            'FROM.required' => 'メールアドレスは必須です',
            'FROM_NAME.required' => '送信者名は必須です',
            'FROM_NAME.max' => '送信者名が長すぎます',
            'PORT.between' => '有効なポート番号ではありません',
            'PORT.numeric' => '有効なポート番号ではありません',
            'USER.max' => 'ユーザ名が長すぎます',
            'PASS.regex' => '半角英数字以外が含まれます',
            'HOST.max' => 'SMTPサーバが長すぎます',
            'HOST.domain' => '有効なドメインではありません',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return [];
    }

    // Method to filter and permit only accessible parameters
    private function permit_params($params)
    {
        $accessibleParams = [
            'FROM_NAME',
            'FROM',
            'STATUS',
            'PROTOCOL',
            'SECURITY',
            'HOST',
            'PORT',
            'USER',
            'PASS',
            'CON_ID',
            'LAST_UPDATE',
        ];

        return array_intersect_key($params, array_fill_keys($accessibleParams, null));
    }
}
