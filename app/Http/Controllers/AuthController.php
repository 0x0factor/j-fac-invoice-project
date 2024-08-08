<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {

        return view('auth.login', [
            'title' => 'Login',
        ]);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'LOGIN_ID' => 'required',
            'PASSWORD' => 'required'
        ]);
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/homes');
        } else {
            Session::flash('error', 'Invalid credentials');
            return redirect()->back()->withInput($request->only('LOGIN_ID'));
        }
    }

    public function register()
    {
        return view('auth.register', [
            'title' => 'Register',
        ]);
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'NAME' => 'required',
            'LOGIN_ID' => 'required|unique:t_user',
            'MAIL' => 'required',
            'PASSWORD' => 'required',
            'passwordConfirm' => 'required|same:PASSWORD'
        ]);

        // qq(Hash::make(InvoiceTool2));
        $val = Hash::make($request['PASSWORD']);
        $validated['PASSWORD'] = $val;
        $validated['CMP_ID'] = 1;
        $validated['ATHORITY'] = 0;
        $validated['STATUS'] = 0;
        $user = User::create($validated);
        Alert::success('Success', 'Register user has been successfully !');
        return redirect('/login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        Alert::success('Success', 'Log out success !');
        return redirect('/login');
    }

    public function reset()
    {
        return view('users.reset', [
            'title' => '抹茶請求書',
        ]);
    }

    // Show password reset form
    public function showResetForm()
    {
        return view('users.email');
    }

    // Handle password reset request
    public function resetPost(Request $request)
    {

            $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('MAIL', $request->email)->first();

            if (!$user) {
                return back()->withErrors(['email' => '入力されたメールアドレスは登録されていません']);
            }

            // Generate a random key
            $key = Str::random(60);
            $user->random_key = Hash::make($key);
            $user->save();

            // Generate the reset URL
            $url = url('/users/pass_edit?k=' . $key);
            $body = config('mail.txt.pass_edit') . "\n" . $url;

            try {
                Mail::to($user->email)->send(new PasswordResetMail($body));
            } catch (\Exception $e) {
                return back()->withErrors(['email' => 'メールの送信に失敗しました']);
            }

            return view('users.reset_end');
    }


    // Show password edit form
    public function showPassEditForm(Request $request)
    {
        $key = $request->query('k');

        if (!$key) {
            return redirect('/');
        }

        $user = User::where('RANDOM_KEY', $key)->first();

        if (!$user) {
            return redirect('/');
        }

        return view('users.reset', ['key' => $key]);
    }

    // Handle password edit
    public function passEdit(Request $request)
    {
        $request->validate([
            'EDIT_PASSWORD' => 'required|confirmed|min:6',
            'key' => 'required'
        ]);

        $user = User::where('RANDOM_KEY', $request->key)->first();

        if (!$user) {
            return redirect('/');
        }

        $user->PASSWORD = Hash::make($request->EDIT_PASSWORD);
        $user->RANDOM_KEY = null;
        $user->LAST_UPDATE = now();
        $user->save();

        return view('users.pass_edit_end');
    }

    public function resetEnd()
    {
        return view('users.reset_end', [
            'title' => '抹茶請求書',
        ]);
    }

    public function passEditEnd()
    {
        return view('users.pass_edit_end', [
            'title' => '抹茶請求書',
        ]);
    }
}
