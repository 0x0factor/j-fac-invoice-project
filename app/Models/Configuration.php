<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Configuration extends Model
{
    // Table name
    protected $table = 'T_CONFIG';

    // Primary key
    protected $primaryKey = 'CON_ID';

    // Disable timestamps if not using `created_at` and `updated_at`
    public $timestamps = false;

    // Fillable fields
    protected $fillable = [
        'CON_ID', 'FROM_NAME', 'FROM', 'STATUS', 'PROTOCOL', 'SECURITY',
        'HOST', 'PORT', 'USER', 'PASS', 'LAST_UPDATE', 'INSERT_DATE'
    ];

    // Validation rules
    public static $rules = [
        'FROM' => 'required|email|max:256',
        'FROM_NAME' => 'required|max:256',
        'PORT' => 'nullable|integer|between:0,65536',
        'USER' => 'nullable|max:30',
        'PASS' => 'nullable|regex:/^[a-zA-Z0-9]+$/',
        'HOST' => 'nullable|max:100|regex:/^[a-zA-Z0-9.-]+$/',
        'STATUS' => 'required|integer',
        'PROTOCOL' => 'required|integer',
        'SECURITY' => 'required|integer'
    ];

    // Custom validation messages
    public static $messages = [
        'FROM.required' => 'メールアドレスは必須です',
        'FROM.email' => '有効なメールアドレスではありません',
        'FROM.max' => 'メールアドレスが長すぎます',
        'FROM_NAME.required' => '送信者名は必須です',
        'FROM_NAME.max' => '送信者名が長すぎます',
        'PORT.between' => '有効なポート番号ではありません',
        'PORT.integer' => '有効なポート番号ではありません',
        'USER.max' => 'ユーザ名が長すぎます',
        'PASS.regex' => '半角英数字以外が含まれます',
        'HOST.max' => 'SMTPサーバが長すぎます',
        'HOST.regex' => '有効なドメインではありません',
        'STATUS.required' => 'ステータスは必須です',
        'PROTOCOL.required' => 'プロトコルが選択されていません',
        'SECURITY.required' => 'SMTPセキュリティが選択されていません'
    ];

    // Custom validation method
    public static function validate($data)
    {
        return Validator::make($data, self::$rules, self::$messages);
    }

    // Select method
    public static function index_select($conId = null)
    {
        if (is_null($conId)) {
            $conId = 1;
        }

        $result = self::find($conId);

        // Return null if not found
        return $result ? $result : null;
    }

    // Set data method
    public function index_setData($data, &$errors = null, $state = null)
    {
        // Time set
        if ($state === 'new') {
            $data['INSERT_DATE'] = now();
            $data['CON_ID'] = 1;
        }
        $data['LAST_UPDATE'] = now();

        // Validation
        $validator = self::validate($data);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return false;
        }

        // Additional validation based on status
        $error = false;
        if ($data['STATUS'] != 0) {
            if (empty($data['HOST'])) {
                $errors['HOST'] = "SMTPサーバは必須です。";
                $error = true;
            }

            if (empty($data['PORT'])) {
                $errors['PORT'] = "ポート番号は必須です。";
                $error = true;
            }

            if (!isset($data['PROTOCOL']) || $data['PROTOCOL'] == '') {
                $errors['PROTOCOL'] = "プロトコルが選択されていません。";
                $error = true;
            } elseif ($data['PROTOCOL'] == 1) {
                if (empty($data['USER'])) {
                    $errors['USER'] = "SMTPユーザは必須です。";
                    $error = true;
                }
                if (empty($data['PASS'])) {
                    $errors['PASS'] = "SMTPパスワードは必須です。";
                    $error = true;
                }
            }

            if (!isset($data['SECURITY']) || $data['SECURITY'] == '') {
                $errors['SECURITY'] = "SMTPセキュリティが選択されていません。";
                $error = true;
            }
        }

        if ($error) {
            return false;
        }

        // Save data
        DB::beginTransaction();

        try {
            $this->fill($data);
            $this->save();
            DB::commit();

            $data['CON_ID'] = $this->id; // Get the ID of the newly created record
            return $data;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
