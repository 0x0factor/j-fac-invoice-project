<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Personal;

class PersonalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // 編集用
    public function passEdit(Request $request)
    {
        $main_title = "パスワード変更";
        $user = Auth::user();

        $title_text = ($user->AUTHORITY == '1') ? "マイメニュー" : "管理者メニュー";
        $title = "抹茶請求書";

        if ($request->has('cancel_x')) {
            return redirect('/homes');
        }

        if ($request->isMethod('post')) {
            // トークンチェック
            $request->validate([
                'Security.token' => 'required|csrf_token'
            ]);

            $personalData = $request->input('Personal');
            $personalData['USR_ID'] = $user->USR_ID;
            $personalData['RANDOM_KEY'] = null;
            $personalData['PASSWORD'] = Hash::make($personalData['EDIT_PASSWORD']);
            $personalData['LAST_UPDATE'] = now()->format('Y-m-d H:i:s');

            $personal = Personal::find($user->USR_ID);
            if ($personal) {
                $personal->fill($this->permitParams($personalData));
                $personal->save();

                Session::flash('status', 'パスワードを変更しました。');
            }

            $request->merge([
                'Personal.PASSWORD_NOW' => null,
                'Personal.EDIT_PASSWORD' => null,
                'Personal.EDIT_PASSWORD1' => null
            ]);
        }
        $usernavi = Personal::paginate(15);

        return view('personal.pass_edit', compact('main_title', 'title_text', 'title', 'usernavi'));
    }
}
