<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Personal extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_USER';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'USR_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'EDIT_PASSWORD',
        'EDIT_PASSWORD1',
        'PASSWORD',
        'RANDOM_KEY',
        'LAST_UPDATE',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'EDIT_PASSWORD' => [
            'rule0' => [
                'password_valid:EDIT_PASSWORD,4,20',
                'message' => 'パスワードは4～20文字で入力してください。',
            ],
            'rule1' => [
                'compare2fields:EDIT_PASSWORD1',
                'message' => 'パスワードとパスワード確認が一致しません',
            ],
        ],
        'EDIT_PASSWORD1' => [
            'rule0' => [
                'password_valid:EDIT_PASSWORD1,4,20',
                'message' => 'パスワード確認は4～20文字で入力してください。',
            ],
            'rule1' => [
                'compare2fields:EDIT_PASSWORD',
                'message' => 'パスワードとパスワード確認が一致しません',
            ],
        ],
    ];

    /**
     * Apply validation rules.
     *
     * @param array $data
     * @return \Illuminate\Validation\Validator
     */
    public static function validate(array $data)
    {
        return Validator::make($data, static::$rules);
    }
}

