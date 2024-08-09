<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use App\Models\User;
use App\Services\HistoryService;
use App\Services\MailService;
use Illuminate\Contracts\Auth\Guard;
class UserController extends BaseController
{
    protected $historyService;
    protected $mailService;
    protected $Auth;

    public function __construct(HistoryService $historyService, MailService $mailService,Guard  $Auth)
    {
        $this->historyService = $historyService;
        $this->mailService = $mailService;
        $this->Auth = $Auth;
    }

    public function showLoginForm(){
        return view('users.login');
    }
    public function showPasswordResetForm(){
        return view('users.reset');
    }

    public function login(Request $request)
{
    if (Auth::attempt($request->only('LOGIN_ID', 'PASSWORD'))) {
        $user = Auth::user();

        // Set the 'userid' cookie
        Cookie::queue('userid', $user->id, 60 * 24 * 7); // Cookie expires in 7 days

        // Redirect the user to the dashboard or home page
        return redirect('/home');
    }

    // Login failed, return an error message
    return back()->withErrors(['LOGIN_ID' => 'Invalid credentials.']);

    $value = Cookie::get('userid');
    if ($value != null) {
        $user = $this->Auth->user();
        $this->historyService->h_logout($value[0]);
        Cookie::forget('userid');
    }

    if ($this->Auth->check()) {
        $user = $this->Auth->user();
        if ($user->STATUS != 1) {
            $this->historyService->h_login($user->USR_ID);

            Session::regenerate();
            Session::forget('session_params');

            return redirect()->route('homes.index');
        } else {
            Session::flash('message', 'IDまたはパスワードが不正です。');
            return redirect()->route('auth.logout');
        }
    } elseif ($request->isMethod('post')) {
        Session::flash('message', 'IDまたはパスワードが不正です。');
    }

    return view('users.login', [
        'page_title' => 'ログイン',
        'main_title' => ''
    ]);
}

    public function logout()
    {
        $user = Auth::user();
        $this->historyService->h_logout($user->USR_ID);
        Auth::logout();
        return redirect()->route('auth.login');
    }

    public function reset(Request $request)
    {
        if ($request->isMethod('post')) {
            $user = User::where('MAIL', $request->input('email'))->first();
            if (!$user) {
                Session::flash('message', '入力されたメールアドレスは登録されていません');
                return redirect()->route('users.reset');
            }

            $key = hash('sha256', uniqid() . mt_rand());
            $user->RANDOM_KEY = $key;
            $user->save();

            $url = route('users.passEdit', ['k' => $key]);
            $body = config('constants.Mail.Txt.PassEdit') . "\n" . $url;
            if (!$this->mailService->sendMail($user->MAIL, config('constants.Mail.Subject.PassEdit'), $body)) {
                Session::flash('message', 'メールの送信に失敗しました');
                return redirect()->route('users.reset');
            }

            return view('users.reset_end');
        }

        return view('users.reset');
    }

    public function passEdit(Request $request)
    {
        $key = $request->input('k', $request->input('User.key'));

        if (!$key) {
            return redirect('/');
        }

        $user = User::where('RANDOM_KEY', $key)->latest()->first();
        if (!$user) {
            return redirect('/');
        }

        if ($request->isMethod('post')) {
            $user->RANDOM_KEY = null;
            $user->PASSWORD = bcrypt($request->input('User.EDIT_PASSWORD'));
            $user->LAST_UPDATE = now();
            $user->save();

            return view('users.passEditEnd');
        }

        return view('users.passEdit', ['key' => $key]);
    }

    protected function Set_View_Option()
    {
        // Add any additional view options here
    }
}
