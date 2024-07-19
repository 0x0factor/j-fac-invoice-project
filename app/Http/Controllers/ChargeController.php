<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Charge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ChargesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['contents']);
    }

    // 一覧用
    public function index()
    {
        $status = config('app.status_code');
        $seal = config('app.seal_code');

        return view('charges.index', compact('status', 'seal'))
            ->with('main_title', '自社担当者一覧')
            ->with('title_text', '自社情報設定')
            ->with('page_title', 'Company');
    }

    // 登録用
    public function add(Request $request)
    {
        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;
        $image_error = 0;
        $chr_id = 0;

        if ($request->has('cancel_x')) {
            return redirect()->route('charges.index');
        }

        if ($request->isMethod('post')) {
            $this->validate($request, [
                'Charge.SEAL_STR' => 'nullable|string',
                // Add other validations here
            ]);

            // Update validation
            $phone_error = $this->phone_validation($request->input('Charge'));
            $fax_error = $this->fax_validation($request->input('Charge'));

            // Seal creation
            if ($request->input('Charge.SEAL_METHOD') && !empty($request->input('Charge.SEAL_STR'))) {
                $this->make_seal($request->input('Charge.SEAL_STR'));
            }

            // Insert data
            $result = Charge::set_data($request->input('Charge'), $company_ID, $phone_error, $fax_error, $chr_id);

            if ($result) {
                if ($result === 1 || $result === 2 || $result === 3) {
                    $image_error = $result;
                } else {
                    Session::flash('message', '自社担当者を保存しました');
                    return redirect()->route('charges.check', ['id' => $chr_id]);
                }
            }
        }

        $status = config('app.status_code');
        $countys = config('app.prefecture_code');
        $seal_method = config('app.seal_method');
        $seal_flg = config('app.seal_flg');

        return view('charges.add', compact('image_error', 'phone_error', 'fax_error', 'status', 'countys', 'seal_method', 'seal_flg'))
            ->with('main_title', '自社担当者登録')
            ->with('title_text', '自社情報設定')
            ->with('page_title', 'Company');
    }

    // 削除用
    public function delete(Request $request)
    {
        $this->validate($request, [
            'Security.token' => 'required'
        ]);

        if ($this->isCorrectToken($request->input('Security.token')) && Charge::index_delete($request->input())) {
            Session::flash('message', '自社担当者を削除しました');
            return redirect()->route('charges.index');
        } else {
            Session::flash('error', '自社担当者が削除できませんでした。');
            return redirect()->route('charges.index');
        }
    }

    // 編集用
    public function edit(Request $request, $charge_ID)
    {
        $company_ID = 1;
        $phone_error = 0;
        $fax_error = 0;
        $image_error = 0;

        if ($request->has('cancel_x')) {
            return redirect()->route('charges.index');
        }

        if ($request->isMethod('post') && $this->isCorrectToken($request->input('Security.token'))) {
            if ($request->input('Charge.DEL_SEAL') != 0) {
                Charge::seal_delete($charge_ID);
            }

            $phone_error = $this->phone_validation($request->input('Charge'));
            $fax_error = $this->fax_validation($request->input('Charge'));

            if ($request->input('Charge.SEAL_METHOD') && !empty($request->input('Charge.SEAL_STR'))) {
                $this->make_seal($request->input('Charge.SEAL_STR'));
            }

            $result = Charge::set_data($request->input('Charge'), $company_ID, $phone_error, $fax_error);

            if ($result) {
                if ($result === 1 || $result === 2 || $result === 3) {
                    $image_error = $result;
                    $image = Charge::get_image($charge_ID);
                    return view('charges.edit', compact('image', 'image_error', 'phone_error', 'fax_error', 'charge_ID'))
                        ->with('main_title', '自社担当者編集')
                        ->with('title_text', '自社情報設定')
                        ->with('page_title', 'Company');
                } else {
                    Session::flash('message', '自社担当者を保存しました');
                    return redirect()->route('charges.check', ['id' => $charge_ID]);
                }
            }
        } else {
            $charge = Charge::edit_select($charge_ID);
            if (!$charge) {
                return redirect()->route('charges.index');
            }

            if (!$this->Get_Edit_Authority($charge['Charge']['USR_ID'])) {
                Session::flash('error', 'ページを開く権限がありません');
                return redirect()->route('charges.index');
            }

            $image = $charge['Charge']['SEAL'] ?? null;

            return view('charges.edit', compact('image', 'phone_error', 'fax_error', 'charge_ID'))
                ->with('main_title', '自社担当者編集')
                ->with('title_text', '自社情報設定')
                ->with('page_title', 'Company');
        }
    }

    // 確認用
    public function check(Request $request, $charge_ID)
    {
        $company_ID = 1;

        if ($request->has('cancel_x')) {
            return redirect()->route('charges.index');
        }

        $charge = Charge::edit_select($charge_ID);
        if (!$charge) {
            return redirect()->route('charges.index');
        }

        if (!$this->Get_Check_Authority($charge['Charge']['USR_ID'])) {
            Session::flash('error', 'ページを開く権限がありません');
            return redirect()->route('charges.index');
        }

        $image = $charge['Charge']['SEAL'] ?? null;

        return view('charges.check', compact('charge', 'image', 'charge_ID'))
            ->with('main_title', '自社担当者確認')
            ->with('title_text', '自社情報設定')
            ->with('page_title', 'Company');
    }

    // 画像表示用
    public function contents($charge_ID)
    {
        $charge = Charge::edit_select($charge_ID);

        $image = $charge['Charge']['SEAL'] ?? null;

        if (empty($image)) {
            abort(404);
        }

        return response($image, 200)
            ->header('Content-Type', 'image/png');
    }

    // 印鑑作成用
    protected function make_seal($str)
    {
        $font_path = storage_path('fonts/ipam00303/ipam.ttf');

        $im = imagecreatetruecolor(500, 500);
        $red = imagecolorallocate($im, 0xFF, 0x00, 0x00);
        $white = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);

        // 背景を白に設定
        imagefill($im, 0, 0, $white);

        // 線幅を 5 に設定
        imagesetthickness($im, 5);

        // 楕円を描画
        imagefilledellipse($im, 250, 250, 300, 300, $red);
        imagefilledellipse($im, 250, 250, 280, 280, $white);

        $tmp_name = array();

        // 文字数ごとに異なる描画方法
        switch (mb_strlen($str)) {
            case 1:
                ImageTTFText($im, 130, 0, 165, 310, $red, $font_path, $str);
                break;

            case 2:
                $tmp_name[0] = mb_substr($str, 0, 1);
                $tmp_name[1] = mb_substr($str, 1, 1);
                ImageTTFText($im, 110, 0, 175, 235, $red, $font_path, $tmp_name[0]);
                ImageTTFText($im, 110, 0, 175, 365, $red, $font_path, $tmp_name[1]);
                break;

            case 3:
                $tmp_name[0] = mb_substr($str, 0, 1);
                $tmp_name[1] = mb_substr($str, 1, 1);
                $tmp_name[2] = mb_substr($str, 2, 1);
                ImageTTFText($im, 75, 0, 200, 195, $red, $font_path, $tmp_name[0]);
                ImageTTFText($im, 75, 0, 200, 285, $red, $font_path, $tmp_name[1]);
                ImageTTFText($im, 75, 0, 200, 375, $red, $font_path, $tmp_name[2]);
                break;

            case 4:
                $tmp_name[0] = mb_substr($str, 0, 1);
                $tmp_name[1] = mb_substr($str, 1, 1);
                $tmp_name[2] = mb_substr($str, 2, 1);
                $tmp_name[3] = mb_substr($str, 3, 1);
                ImageTTFText($im, 85, 0, 245, 235, $red, $font_path, $tmp_name[0]);
                ImageTTFText($im, 85, 0, 245, 345, $red, $font_path, $tmp_name[1]);
                ImageTTFText($im, 85, 0, 135, 235, $red, $font_path, $tmp_name[2]);
                ImageTTFText($im, 85, 0, 135, 345, $red, $font_path, $tmp_name[3]);
                break;
        }

        // キャンバスを透過する
        imagecolortransparent($im, $white);

        // 出力
        $tmpFilePath = storage_path('app/tmpseal.png');
        imagepng($im, $tmpFilePath);
        $sealImage = file_get_contents($tmpFilePath);
        imagedestroy($im);

        return $sealImage;
    }
}