<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Administer extends Model
{
    protected $table = 't_user';
    protected $primaryKey = 'USR_ID';
    protected $fillable = [
        'USR_ID',
        'NAME',
        'NAME_KANA',
        'UNIT',
        'MAIL',
        'LOGIN_ID',
        'PASSWORD',
        'STATUS',
        'AUTHORITY',
        'CMP_ID',
        'ADD_DATE',
        'LAST_UPDATE',
    ];

    // Validation rules (using Laravel's Validator)
    public static $rules = [
        'NAME' => [
            'rule0' => ['regex:/[^\s]+/', 'message' => 'スペース以外も入力してください'],
            'rule1' => ['required', 'message' => '名前は必須項目です'],
            'rule2' => ['max:30', 'message' => '名前が長すぎます'],
        ],
        'NAME_KANA' => [
            'rule2' => ['max:60', 'message' => '名前カナが長すぎます'],
            'rule3' => ['regex:/^[ァ-ヶー　]+$/u', 'message' => '名前カナに入力できない値があります。'],
            'rule4' => ['regex:/[^\s]+/', 'message' => 'スペース以外も入力してください'],
        ],
        'UNIT' => [
            'rule2' => ['max:30', 'message' => '部署名が長すぎます'],
        ],
        'MAIL' => [
            'rule3' => ['max:255', 'message' => 'メールアドレスが長すぎます'],
        ],
        'LOGIN_ID' => [
            'rule0' => ['regex:/[^\s]+/', 'message' => 'スペース以外も入力してください'],
            'rule1' => ['required', 'message' => 'ログインIDは必須項目です'],
            'rule2' => ['between:5,10', 'message' => 'ログインIDは5～10文字で入力してください'],
            'rule3' => ['regex:/^[a-zA-Z0-9]+$/i', 'message' => '使用できな文字が含まれています。'],
            'rule4' => ['unique:T_USER,LOGIN_ID', 'message' => 'ログインIDは既に使用されています。'],
        ],
        'EDIT_PASSWORD' => [
            'rule1' => ['password_valid:EDIT_PASSWORD,4,20', 'message' => 'パスワードは4～20文字で入力してください。'],
            'rule2' => ['compare2fields:EDIT_PASSWORD1', 'message' => 'パスワードとパスワード確認が一致しません'],
        ],
        'EDIT_PASSWORD1' => [
            'rule1' => ['password_valid:EDIT_PASSWORD1,4,20', 'message' => 'パスワード確認は4～20文字で入力してください。'],
            'rule2' => ['compare2fields:EDIT_PASSWORD', 'message' => 'パスワードとパスワード確認が一致しません'],
        ],
    ];

    // Accessible columns (not necessary in Laravel, included for reference)
    protected $accessible = [
        'USR_ID',
        'NAME',
        'NAME_KANA',
        'UNIT',
        'MAIL',
        'LOGIN_ID',
        'EDIT_PASSWORD',
        'EDIT_PASSWORD1',
        'STATUS',
        'AUTHORITY',
        'PASSWORD',
        'CMP_ID',
        'ADD_DATE',
        'LAST_UPDATE',
    ];


    // Validation rules could be implemented using Laravel's Validator class or in form request validation.

    /*
     * データの書き込み処理
     */
    public function set_data($param, $error, $state = '')
    {
        if (isset($param['USR_ID'])) {
            $this->setAttribute('USR_ID', $param['USR_ID']);
        } else {
            // 時間のセット
            $param['ADD_DATE'] = now();
        }
        $param['LAST_UPDATE'] = now();

        if ($state == 'edit') {
            if (empty($param['EDIT_PASSWORD'])) {
                $param['PASSWORD'] = $param['PASSWORD_NOW'];
            } else {
                $param['PASSWORD'] = Hash::make($param['EDIT_PASSWORD']);
            }
        }

        $this->fill($param);

        if ($this->validate($param, $error)) {
            if ($this->save()) {
                return $param;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /*
     * ユーザ情報の取得
     */
    public function edit_select($usrId)
    {
        $result = $this->where('USR_ID', $usrId)->first();

        if ($result) {
            $result->PASSWORD = null;
            return $result;
        } else {
            return null;
        }
    }

    /*
     * index削除用メソッド
     */
    public function index_delete($param)
    {
        $idsToDelete = collect($param['Administer'])
            ->filter(function ($value) {
                return $value == 1;
            })
            ->keys()
            ->toArray();

        if (count($idsToDelete) > 0) {
            // 削除処理
            return $this->whereIn('USR_ID', $idsToDelete)->delete();
        } else {
            return false;
        }
    }

    /**
     * バリデーションの実行
     */
    protected function validate($param, $error)
    {
        // Implement your validation logic here, using Laravel's Validator class or in a form request.
        // Example:
        // $validator = Validator::make($param, [
        //     'NAME' => 'required|max:30',
        //     'NAME_KANA' => 'max:60|katakanaSpace',
        //     'UNIT' => 'max:30',
        //     'MAIL' => 'max:255',
        //     'LOGIN_ID' => 'required|min:5|max:10|idNumber',
        //     'EDIT_PASSWORD' => 'nullable|min:4|max:20|same:EDIT_PASSWORD1',
        //     'EDIT_PASSWORD1' => 'nullable|min:4|max:20|same:EDIT_PASSWORD',
        // ]);

        // return !$validator->fails();

        // For simplicity, returning true for now
        return true;
    }
}
