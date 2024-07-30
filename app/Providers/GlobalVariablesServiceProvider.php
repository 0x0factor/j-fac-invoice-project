<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class GlobalVariablesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
         // Define global variables
         $globalVariables = [
            'usernavi' => [
                'USR_NAME'=>'',
                'USR_NAME_KANA'=>'',
                'UNIT'=>'',
                'USR_MAIL'=>'',
                'AUTHORITY'=>'',
                'USR_ID'=>'',
                'CMP_NAME'=>'',
                'RECEIPT_NUMBER'=>'',
                'PROVISO'=>'',
                'CHARGE_NAME'=>'',
                'SEAL'=>'',
                'SEAL_METHOD'=>'',
                'MAIL_FROM_NAME'=>'',
                'MAIL_FROM'=>'',
                'MAIL_SMTP'=>'',
                'MAIL_SMTP_USER'=>'',
                'MAIL_SMTP_PW'=>'',
                'CVR_CST'=>'選択をクリックし、挿入を選択することで顧客名が自動入力されます。',
                'CVR_CST_CHR'=>'顧客担当者名は自分で入力するか、選択をクリックし一覧から選んでください。顧客担当者を選択をすると部署名は自動で入力されます。',
                'CHR_NAME'=>'自社担当者名を入力してください。（30文字以内）',
                'CVR_TITLE'=>'　(20文字以内)',
                'CUSTOMER_PAYMENT'=>'',
                'CST_CUTOOFF'=>'',
                'STATUS'=>'',
                'CUSTOMER_ADDRESS'=>'',
                'CHARGE_NAME_KANA'=>'',
                'POST'=>'',
                'POSTCODE'=>'',
                'ADDRESS'=>'',
                'BUILDING'=>'',
                'PHONE'=>'',
                'FAX'=>'',
                'NO'=>'半角英数20文字以内、記号は「/」「,」「-」「_」が利用可能です。',
                'DATE'=>'「現在」ボタンか「カレンダー」ボタンをクリックし、発行日を設定してください。',
                'SUBJECT'=>'(40文字以内)罫線からはみ出る場合は半角を利用してください。',
                'CST_ID'=>'選択をクリックし、挿入を選択することで顧客名が自動入力されます。削除をクリックすると顧客名が空になります。',
                'HONOR'=>'御中・様などを入力してください。(4文字以内)',
                'SEAL_FLG'=>'帳票に押印するかどうかの設定ができます。',
                'FEE'=>'(20文字以内)',
                'ITEM_LIST'=>'項目を入力してください。罫線からはみ出る場合は、行を分けるか半角を利用してください。<br>左の「×」をクリックすると行が削除されます。右の「設定」をクリックするとアイテムごとの割引を設定できます。',
                'MOVE_LINE'=>'移動したい行にカーソルをあて、上下ボタンを押すと行を移動できます。',
                'ADD_LINE'=>' 行を新たに追加できます。',
                'DEADLINE'=>'(20文字以内)',
                'DEAL'=>'(20文字以内)',
                'DELIVERY'=>'(20文字以内)',
                'DUE_DATE'=>'(20文字以内)',
                'NOTE'=>'(300文字以内、6行まで)',
                'MEMO'=>'メモは帳票には印字されませんので、帳票の管理を行う際にご活用ください。',
                'ITEM'=>'(40文字以内)',
                'ITEM_KANA'=>'ポップアップ内でカナ検索、カナ順ソートを行う際に必要になります。(50文字以内)',
                'ITEM_CODE'=>'(8文字以内)',
                'ITM_UNIT'=>'(4文字以内)',
                'ITM_PRICE'=>'(8桁以内)',
                'TAX_CLASS'=>'どれか一つ選択してください',
                'USR_PASSWORD'=>'4～20文字以内で入力してください。',
                'USR_CPASSWORD'=>'確認のためパスワードをもう一度入力してください。',
                'DISCOUNT'=>'全体に対しての割引設定が行えます。複数の税率を選択している場合は、設定できません。',
                'EDIT_STAT'=>'',
                'LOGO'=>'',
                ]
        ];

        // Share the variables with all views
        View::share($globalVariables);
    }
}
