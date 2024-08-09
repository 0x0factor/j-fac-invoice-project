<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AdministerController extends Controller
{
    // Initialize controller
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated
    }

    // 一覧用
    public function index()
    {
        $main_title = "ユーザ一覧";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        $companyID = 1;

        // Retrieve all users except those with AUTHORITY 0
        $administers = Administer::where('AUTHORITY', '!=', 0)->paginate(20);
        $list = $administers->items();
        $status = config('constants.StatusCode');
        return view('administer.index', compact('main_title', 'title_text', 'title', 'administers', 'list', 'status'));
    }

    // 登録用
    public function add(Request $request)
    {
        $main_title = "ユーザ登録";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        $company_ID = 1;
        $error = [
            'LOGIN_ID' => 0,
            'PASSWORD' => 0,
            'MAIL' => 0
        ];

        if ($request->has('cancel_x')) {
            return redirect('/administers');
        }

        if ($request->has('submit_x')) {
            // Validate CSRF token
            $request->validate([
                '_token' => 'required'
            ]);

            $requestData = $request->all();


            $requestData['EDIT_PASSWORD'] = bcrypt($requestData['EDIT_PASSWORD']);

            // Validation
            $validator = Validator::make($requestData, [
                'LOGIN_ID' => 'unique:administers',
                'MAIL' => 'email'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->toArray();
            }

            $requestData['CMP_ID'] = $company_ID;
            $administer = Administer::create($requestData);

            if ($administer) {
                Session::flash('success', 'ユーザを保存しました');
                return redirect("/administers/check/{$administer->USR_ID}");
            } else {
                $requestData['EDIT_PASSWORD'] = null;
                $requestData['EDIT_PASSWORD1'] = null;
            }
        }

        $status = config('constants.StatusCode');

        $authority = config('constants.AuthorityCode');

        return view('administer.add', compact('main_title', 'title_text', 'title', 'error', 'status', 'authority'));
    }

    // 編集用
    public function edit(Request $request, $usr_ID)
    {
        $main_title = "ユーザ編集";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        $company_ID = 1;
        $error = [
            'LOGIN_ID' => 0,
            'PASSWORD' => 0,
            'MAIL' => 0
        ];

        if ($request->has('cancel_x')) {
            return redirect('/administers');
        }

        if ($request->isMethod('post')) {
            // Validate CSRF token
            $request->validate([
                '_token' => 'required'
            ]);

            $requestData = $request->all();
            $administer = Administer::find($usr_ID);

            if ($requestData['CHANGEFLG'] == 1) {
                if (empty($requestData['EDIT_PASSWORD'])) {
                    $error['PASSWORD'] = 1;
                }
                // Update password
                $requestData['PASSWORD'] = bcrypt($requestData['EDIT_PASSWORD']);
                $requestData['PASSWORD_NOW'] = bcrypt($requestData['PASSWORD_NOW']);
            } else {
                $requestData['PASSWORD_NOW'] = $administer->PASSWORD;
            }

            $validator = Validator::make($requestData, [
                'MAIL' => 'email'
            ]);

            if ($validator->fails()) {
                $error['MAIL'] = 2;
            }

            if ($administer->update($requestData)) {
                Session::flash('success', 'ユーザを保存しました');
                return redirect("/administers/check/{$requestData['USR_ID']}");
            } else {
                $requestData['PASSWORD_NOW'] = null;
                $requestData['EDIT_PASSWORD'] = null;
                $requestData['EDIT_PASSWORD1'] = null;
            }
        } else {
            $administer = Administer::find($usr_ID);

            if (!$administer) {
                return redirect('/administers');
            }

            $requestData = $administer->toArray();
            $requestData['CHANGEFLG'] = 0;
        }

        $status = config('constants.StatusCode');
        $authority = config('constants.AuthorityCode');

        return view('administer.edit', compact('main_title', 'title_text', 'title', 'error', 'status', 'authority', 'requestData'));
    }

    // 編集用
    public function check($usr_ID)
    {
        $main_title = "ユーザ確認";
        $title_text = "管理者メニュー";
        $title = "抹茶請求書";

        $administer = Administer::find($usr_ID);

        if (!$administer) {
            return redirect('/administers');
        }

        $status = config('constants.StatusCode');
        $authority = config('constants.AuthorityCode');

        return view('administer.check', compact('main_title', 'title_text', 'title', 'administer', 'status', 'authority'));
    }
}
