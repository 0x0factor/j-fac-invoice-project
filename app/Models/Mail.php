<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail as LaravelMail;
use Carbon\Carbon;

class Mail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'T_MAIL';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'TML_ID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'TO', 'FROM', 'CUSTOMER', 'CHARGE', 'CUSTOMER_CHARGE', 'PASSWORD1', 'COMMENT', 'SUBJECT', 'STATUS', 'TYPE'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'STATUS' => 'integer',
    ];

    /**
     * Validation rules.
     *
     * @var array
     */
    public static $rules = [
        'TO' => ['required', 'email'],
        'FROM' => ['required', 'email'],
        'CUSTOMER' => ['required', 'string', 'max:30'],
        'CHARGE' => ['required', 'string', 'max:30'],
        'CUSTOMER_CHARGE' => ['required', 'string', 'max:30'],
        'PASSWORD1' => ['required', 'string', 'regex:/^[a-zA-Z0-9]+$/i', 'between:6,12'],
        'COMMENT' => ['string', 'max:200'],
        'SUBJECT' => ['required', 'string'],
        'STATUS' => ['integer'],
        'TYPE' => ['string'],
    ];

    /**
     * Retrieve mail data.
     *
     * @param string $_type
     * @param array $_param
     * @return array|false
     */
    public function mail_data($_type, $_param)
    {
        if (!$_param) {
            return false;
        }

        $result = [];

        if ($_type === 'Quote') {
            $result['FRM_ID'] = $_param[$_type]['MQT_ID'];
        } elseif ($_type === 'Bill') {
            $result['FRM_ID'] = $_param[$_type]['MBL_ID'];
        } elseif ($_type === 'Delivery') {
            $result['FRM_ID'] = $_param[$_type]['MDV_ID'];
        }
        $result['SUBJECT'] = $_param[$_type]['SUBJECT'];
        $result['Customer'] = $_param['Customer']['NAME'];
        $result['CST_ID'] = $_param['Customer']['CST_ID'];

        $company = Company::where('CMP_ID', 1)->first();
        $result['Company'] = $company->NAME;

        return $result;
    }

    /**
     * Send response mail.
     *
     * @param array $_param
     * @param array $_mail
     * @return bool
     */
    public function res_mail($_param, $_mail)
    {
        // Set up Japanese language for email
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        // Subject
        $subject = $_param['Mail']['RCV_NAME'] . "様から確認メールが届きました";

        // From
        $from = "From: " . $_param['Mail']['RECEIVER'];

        // To
        $to = $_param['Mail']['SENDER'];

        // Body
        $body = "";
        $body .= $_param['Mail']['SND_NAME'] . " 様\n\n";
        $body .= $_param['Mail']['RCV_NAME'] . " 様より以下の内容が届いています。\n";
        $body .= "詳細は、確認メールページで、ご確認ください。\n\n";
        $body .= "------------------------------------------------------------\n";
        $body .= "件名　　　　　：" . $_param['Mail']['SUBJECT'] . "\n";
        $body .= "ステータス　　：" . ($_mail['STATUS'] == 1 ? "確認済み" : "修正願い") . "\n\n";
        $body .= "コメント\n";
        $body .= $_mail['COMMENT'] . "\n\n";
        $body .= "------------------------------------------------------------\n\n\n";
        $body .= "本メールは「抹茶請求書」による自動送信メールです。本メールにお心当たりの\n";
        $body .= "ない場合は、恐れ入りますが、破棄くださいますようお願い申し上げます。\n";

        // Send email using Laravel Mail facade
        if (LaravelMail::send([], [], function ($message) use ($to, $subject, $body) {
            $message->to($to)->subject($subject)->setBody($body, 'text/plain');
        })) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Send mail.
     *
     * @param array $_send_param
     * @return bool
     */
    public function Send_Mail($_send_param)
    {
        // Email validation
        $validator = Validator::make($_send_param, static::$rules);

        if ($validator->fails()) {
            return false;
        }

        // Subject
        $subject = $_send_param['CHARGE'] . "様よりデータ受領の確認";

        // From
        $from = "From: " . $_send_param['FROM'];

        // Body
        $body = str_replace(['<br />', '<br>'], "", $_send_param['BODY']);

        // Prepare data for saving to database
        $data = [
            'FRM_ID' => $_send_param['FRM_ID'],
            'USR_ID' => $_send_param['USR_ID'],
            'RECEIVER' => $_send_param['MAIL'],
            'SENDER' => $_send_param['FROM'],
            'CUSTOMER' => $_send_param['CUSTOMER'],
            'RCV_NAME' => $_send_param['CUSTOMER_CHARGE'],
            'SUBJECT' => $_send_param['SUBJECT'],
            'SND_NAME' => $_send_param['CHARGE'],
            'STATUS' => 0,
            'TOKEN' => $_send_param['CORD'],
            'TYPE' => $_send_param['TYPE'],
            'PASSWORD' => $_send_param['PASSWORD'],
            'SND_MESSAGE' => $body,
            'SND_DATE' => Carbon::now(),
        ];

        // Start transaction
        $this->getConnection()->beginTransaction();

        try {
            $this->create($data);

            // Send email
            if (LaravelMail::send([], [], function ($message) use ($to, $subject, $body) {
                $message->to($to)->subject($subject)->setBody($body, 'text/plain');
            })) {
                $this->getConnection()->commit();
                return true;
            } else {
                $this->getConnection()->rollback();
                return false;
            }
        } catch (\Exception $e) {
            $this->getConnection()->rollback();
            return false;
        }
    }
}

