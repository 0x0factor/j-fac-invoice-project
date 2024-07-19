<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_user';

    /**
     * The primary key for the model.
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
        'LOGIN_ID',
        'NAME',
        'MAIL',
        'PASSWORD',
        'CMP_ID',
        'ADD_DATE',
        'LAST_UPDATE',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'PASSWORD',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'ADD_DATE' => 'datetime',
        'LAST_UPDATE' => 'datetime',
    ];

    /**
     * Validation rules for the model.
     *
     * @var array
     */
    protected $rules = [
        'EDIT_PASSWORD' => [
            'rule0' => [
                'rule' => ['password_valid', 'EDIT_PASSWORD', 4, 20],
                'message' => 'パスワードは4～20文字で入力してください。',
            ],
            'rule1' => [
                'rule' => ['compare2fields', 'EDIT_PASSWORD1'],
                'message' => 'パスワードとパスワード確認が一致しません',
            ],
        ],
        'EDIT_PASSWORD1' => [
            'rule0' => [
                'rule' => ['password_valid', 'EDIT_PASSWORD1', 4, 20],
                'message' => 'パスワード確認は4～20文字で入力してください。',
            ],
            'rule1' => [
                'rule' => ['compare2fields', 'EDIT_PASSWORD'],
                'message' => 'パスワードとパスワード確認が一致しません',
            ],
        ],
    ];
    /**
     * Hash the password before saving the model.
     *
     * @param  array  $data
     * @param  bool  $enforce
     * @return array
     */
    public function hashPasswords($data, $enforce = false)
    {
        if ($enforce && isset($data[$this->getTable()]['PASSWORD'])) {
            if (!empty($data[$this->getTable()]['PASSWORD'])) {
                $data[$this->getTable()]['PASSWORD'] = Hash::make($data[$this->getTable()]['PASSWORD']);
            }
        }
        return $data;
    }

    /**
     * Hash the password before saving the model.
     *
     * @return bool
     */
    public function beforeSave()
    {
        $this->hashPasswords($this->getAttributes(), true);
        return true;
    }
    /**
     * Automatically hash the password when setting it.
     *
     * @param string $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['PASSWORD'] = Hash::make($value);
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function username()
    {
        return 'LOGIN_ID';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }

    /**
     * Register a new user.
     *
     * @param  array  $param
     * @param  string  $pass
     * @return bool
     */
    public function registUser($param, $pass)
    {
        $datetime = now()->toDateTimeString();
        $data = [
            'LOGIN_ID' => 'admin',
            'NAME' => '管理者',
            'MAIL' => $param['mail'],
            'PASSWORD' => $pass,
            'CMP_ID' => 1,
            'ADD_DATE' => $datetime,
            'LAST_UPDATE' => $datetime,
        ];

        try {
            $this->create($data);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Search for a user ID.
     *
     * @param  string  $_LoginID
     * @param  int|null  $_usrID
     * @return int
     */
    public function searchUserID($_LoginID, $_usrID = null)
    {
        if (mb_strlen($_LoginID) > 0) {
            if (!preg_match("/^[a-zA-Z0-9-]*$/", $_LoginID)) {
                return 4;
            }
            if (mb_strlen($_LoginID) < 5) {
                return 2;
            }
            if (mb_strlen($_LoginID) > 10) {
                return 3;
            }

            $query = self::query();
            if ($_usrID === null) {
                $query->where('LOGIN_ID', $_LoginID);
            } else {
                $query->where('LOGIN_ID', $_LoginID)
                      ->where('USR_ID', '!=', $_usrID);
            }

            if ($query->first()) {
                return 0;
            }
            return 1;
        }
        return 5;
    }
}