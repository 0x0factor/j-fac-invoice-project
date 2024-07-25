<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {

        return view('auth.login', [
            'title' => 'Login',
        ]);
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'LOGIN_ID' => 'required',
            'PASSWORD' => 'required'
        ]);
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            Alert::success('Success', 'Login success !');
            return redirect()->intended('/homes');
        } else {
            Alert::error('Error', 'Login failed !');
            return redirect('/login');
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
}
