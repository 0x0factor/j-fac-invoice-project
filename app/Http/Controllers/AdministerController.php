<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Administer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class AdministersController extends Controller
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
        $companyID = 1;

        // Retrieve all users except those with AUTHORITY 0
        $administers = Administer::where('AUTHORITY', '!=', 0)->paginate();

        return view('administers.index', compact('main_title', 'title_text', 'administers'));
    }

    // 登録用
    public function add(Request $request)
    {
        $main_title = "ユーザ登録";
        $title_text = "管理者メニュー";
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
            $requestData['Administer']['PASSWORD'] = bcrypt($requestData['Administer']['EDIT_PASSWORD']);

            // Validation
            $validator = Validator::make($requestData['Administer'], [
                'LOGIN_ID' => 'unique:administers',
                'MAIL' => 'email'
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->toArray();
            }

            $requestData['Administer']['CMP_ID'] = $company_ID;
            $administer = Administer::create($requestData['Administer']);

            if ($administer) {
                Session::flash('success', 'ユーザを保存しました');
                return redirect("/administers/check/{$administer->USR_ID}");
            } else {
                $requestData['Administer']['EDIT_PASSWORD'] = null;
                $requestData['Administer']['EDIT_PASSWORD1'] = null;
            }
        }

        $status = config('status_code');
        $authority = config('authority_code');

        return view('administers.add', compact('main_title', 'title_text', 'error', 'status', 'authority'));
    }

    // 編集用
    public function edit(Request $request, $usr_ID)
    {
        $main_title = "ユーザ編集";
        $title_text = "管理者メニュー";
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

            if ($requestData['Administer']['CHANGEFLG'] == 1) {
                if (empty($requestData['Administer']['EDIT_PASSWORD'])) {
                    $error['PASSWORD'] = 1;
                }
                // Update password
                $requestData['Administer']['PASSWORD'] = bcrypt($requestData['Administer']['EDIT_PASSWORD']);
                $requestData['Administer']['PASSWORD_NOW'] = bcrypt($requestData['Administer']['PASSWORD_NOW']);
            } else {
                $requestData['Administer']['PASSWORD_NOW'] = $administer->PASSWORD;
            }

            $validator = Validator::make($requestData['Administer'], [
                'MAIL' => 'email'
            ]);

            if ($validator->fails()) {
                $error['MAIL'] = 2;
            }

            if ($administer->update($requestData['Administer'])) {
                Session::flash('success', 'ユーザを保存しました');
                return redirect("/administers/check/{$requestData['Administer']['USR_ID']}");
            } else {
                $requestData['Administer']['PASSWORD_NOW'] = null;
                $requestData['Administer']['EDIT_PASSWORD'] = null;
                $requestData['Administer']['EDIT_PASSWORD1'] = null;
            }
        } else {
            $administer = Administer::find($usr_ID);

            if (!$administer) {
                return redirect('/administers');
            }

            $requestData['Administer'] = $administer->toArray();
            $requestData['Administer']['CHANGEFLG'] = 0;
        }

        $status = config('status_code');
        $authority = config('authority_code');

        return view('administers.edit', compact('main_title', 'title_text', 'error', 'status', 'authority', 'requestData'));
    }

    // 編集用
    public function check($usr_ID)
    {
        $main_title = "ユーザ確認";
        $title_text = "管理者メニュー";

        $administer = Administer::find($usr_ID);

        if (!$administer) {
            return redirect('/administers');
        }

        $status = config('status_code');
        $authority = config('authority_code');

        return view('administers.check', compact('main_title', 'title_text', 'administer', 'status', 'authority'));
    }
}