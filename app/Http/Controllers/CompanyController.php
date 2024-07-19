<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Serial;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class CompaniesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('contents');
    }

    public function index()
    {
        $main_title = "自社情報設定確認";
        $title_text = "自社情報設定";

        $company_ID = 1;

        // 自社情報の取得
        $data = Company::find($company_ID);

        // 画像をセット
        $image = null;
        if (isset($data->SEAL) && $data->SEAL) {
            $image = $data->SEAL;
        }

        // 色設定
        $color = [];
        foreach (Config::get('constants.ColorCode') as $key => $value) {
            $color[$key] = $value['name'];
        }

        return view('companies.index', compact(
            'main_title',
            'title_text',
            'data',
            'image',
            'color'
        ))
        ->with('account_type', Config::get('constants.AccountTypeCode'))
        ->with('countys', Config::get('constants.PrefectureCode'))
        ->with('payment', Config::get('constants.PaymentMonth'))
        ->with('direction', Config::get('constants.DirectionCode'))
        ->with('cutooff_select', [0 => '末日', 1 => '指定'])
        ->with('payment_select', [0 => '末日', 1 => '指定'])
        ->with('decimals', Config::get('constants.DecimalCode'))
        ->with('excises', Config::get('constants.ExciseCode'))
        ->with('fractions', Config::get('constants.FractionCode'))
        ->with('tax_fraction_timing', Config::get('constants.TaxFractionTimingCode'))
        ->with('numbering_format', Config::get('constants.NumberingFormat'))
        ->with('honor', Config::get('constants.HonorCode'))
        ->with('serial_option', Config::get('constants.Serial'))
        ->with('seal_flg', Config::get('constants.SealFlg'));
    }

    public function edit(Request $request)
    {
        $main_title = "自社情報設定";
        $title_text = "自社情報設定";

        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;
        $image_error = 0;
        $serial_error = 0;

        // キャンセルボタンを押された場合、一覧にリダイレクト
        if ($request->has('cancel')) {
            return redirect()->route('companies.index');
        }

        if ($request->isMethod('post')) {
            // トークンチェック
            $this->isCorrectToken($request->input('Security.token'));

            // 更新時処理
            if ($request->input('Company.DEL_SEAL') != 0) {
                Company::seal_delete($company_ID);
            }

            // 電話番号のバリデーション
            $phone_error = $this->phone_validation($request->input('Company'), 'Company');

            // FAX番号のバリデーション
            $fax_error = $this->fax_validation($request->input('Company'));

            // 連番情報のバリデーション
            $serial_error = $this->serial_validation($request->input('SERIAL'));

            if ($request->input('Company.HONOR_CODE') != 2) {
                $request->merge(['Company' => ['HONOR_TITLE' => '']]);
            }

            // 連番情報のインサート
            Serial::set_data($request->input('SERIAL'));

            // データのインサート
            $result = Company::index_set_data($request->all(), $phone_error, $fax_error, $serial_error);

            if ($result) {
                if (in_array($result, [1, 2, 3])) {
                    // 画像登録失敗
                    $image_error = $result;
                    $image = Company::get_image($company_ID);
                } else {
                    // 成功
                    Session::flash('message', '自社情報設定を保存しました');
                    return redirect()->route('companies.index');
                }
            }
        } else {
            // 通常時処理
            // 自社情報の取得
            $data = Company::find($company_ID);
            $data['SERIAL'] = Serial::get_data();
        }

        // 画像をセット
        $image = null;
        if (isset($data['Company']['SEAL']) && $data['Company']['SEAL']) {
            $image = $data['Company']['SEAL'];
        }

        // 小数点処理
        $decimal = [
            'type' => 'radio',
            'options' => Config::get('constants.DecimalCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 5px; margin-left: 10px;',
            'class' => 'txt_mid'
        ];

        // 消費税
        $excise = [
            'type' => 'radio',
            'options' => Config::get('constants.ExciseCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 5px; margin-left: 10px;',
            'class' => 'txt_mid'
        ];

        // 端数処理
        $fraction = [
            'type' => 'radio',
            'options' => Config::get('constants.FractionCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 5px; margin-left: 10px;',
            'class' => 'txt_mid'
        ];

        // 消費税端数計算
        $tax_fraction_timing = [
            'type' => 'radio',
            'options' => Config::get('constants.TaxFractionTimingCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 10px; margin-left: 8px;',
            'class' => 'txt_mid'
        ];

        // 色設定
        $color = [];
        foreach (Config::get('constants.ColorCode') as $key => $value) {
            $color[$key] = $value['name'];
        }

        // 締日処理
        $cutooff_select = [
            'type' => 'radio',
            'options' => [0 => '末日', 1 => '指定'],
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 5px; margin-left: 10px;',
            'class' => 'txt_mid'
        ];

        // 支払い日処理
        $payment_select = [
            'type' => 'radio',
            'options' => [0 => '末日', 1 => '指定'],
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'margin-right: 5px; margin-left: 10px;',
            'class' => 'txt_mid'
        ];

        return view('companies.edit', compact(
            'main_title',
            'title_text',
            'data',
            'image',
            'phone_error',
            'fax_error',
            'image_error',
            'serial_error',
            'decimal',
            'excise',
            'fraction',
            'tax_fraction_timing',
            'color'
        ))
        ->with('numbering_format', Config::get('constants.NumberingFormat'))
        ->with('serial', Config::get('constants.Serial'))
        ->with('account_type', Config::get('constants.AccountTypeCode'))
        ->with('countys', Config::get('constants.PrefectureCode'))
        ->with('payment', Config::get('constants.PaymentMonth'))
        ->with('direction', Config::get('constants.DirectionCode'))
        ->with('cutooff_select', $cutooff_select)
        ->with('payment_select', $payment_select)
        ->with('honor', Config::get('constants.HonorCode'))
        ->with('seal_flg', Config::get('constants.SealFlg'));
    }

    // 画像表示用
    public function contents()
    {
        $company_ID = 1;
        $data = Company::find($company_ID);

        $image = $data->SEAL;

        if (empty($image)) {
            abort(404);
        }

        return response($image)->header('Content-Type', 'image/png');
    }
}