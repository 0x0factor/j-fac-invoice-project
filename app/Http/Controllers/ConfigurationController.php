<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ConfigurationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 一覧用
    public function index()
    {
        $main_title = "環境設定";
        $title_text = "管理者メニュー";

        $params = Configuration::index_select(1);

        return view('configurations.index', compact(
            'main_title',
            'title_text',
            'params'
        ))
        ->with('security', Config::get('constants.SmtpSecurityCode'))
        ->with('status', [
            0 => '無効',
            1 => '有効'
        ])
        ->with('protocol', Config::get('constants.MailProtocolCode'));
    }

    // 編集用
    public function edit(Request $request)
    {
        $main_title = "環境設定";
        $title_text = "管理者メニュー";

        $error = [];

        // キャンセルボタンを押された場合、一覧にリダイレクト
        if ($request->has('cancel_x')) {
            return redirect('/configurations');
        }

        if ($request->isMethod('post')) {
            // トークンチェック
            $this->isCorrectToken($request->input('Security.token'));

            // データのインサート
            $data = $request->input('Configuration');
            if ($data['STATUS'] == 0) {
                $data['SECURITY'] = [];
                $data['PROTOCOL'] = [];
            }

            $result = Configuration::index_set_data($request->all(), $error);

            if (empty($error)) {
                // 成功
                Session::flash('message', '自社情報設定を保存しました');
                return redirect('/configurations');
            } else {
                // 失敗
                if (isset($error['PROTOCOL']) || isset($error['SECURITY'])) {
                    Session::flash('message', '値が不正に入力されました');
                }
            }
        } else {
            // 通常時処理
            // メールサーバ情報の取得
            $data = Configuration::index_select(1);
        }

        $protocol = [
            'type' => 'radio',
            'options' => Config::get('constants.MailProtocolCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        $security = [
            'type' => 'radio',
            'options' => Config::get('constants.SmtpSecurityCode'),
            'div' => false,
            'label' => false,
            'legend' => false,
            'style' => 'width:30px;',
            'class' => 'txt_mid'
        ];

        return view('configurations.edit', compact(
            'main_title',
            'title_text',
            'data'
        ))
        ->with('security', $security)
        ->with('status', [
            0 => '無効',
            1 => '有効'
        ])
        ->with('protocol', $protocol);
    }
}