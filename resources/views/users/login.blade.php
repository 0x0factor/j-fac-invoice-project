@extends('layout.app')

@section('content')
<div id="login">
    <form method="POST" action="{{ route('login') }}" name="UserLoginForm">
        @csrf
        <div class="login">
            <img src="{{ asset('img/login/tl_login.jpg') }}" alt="ログイン画面">
        </div>
        <div id="login_box">
            <div id="login_logo">
                <img src="{{ asset('img/login/i_logo_login.jpg') }}">
            </div>
            <div id="login_id">
                <img src="{{ asset('img/login/tm_id.gif') }}" alt="ID" class="mr10">
                <input type="text" name="LOGIN_ID" class="w320" value="{{ old('LOGIN_ID') }}">
            </div>

            <div id="login_pw">
                <img src="{{ asset('img/login/tm_pw.gif') }}" alt="パスワード" class="mr10">
                <input type="password" name="PASSWORD" class="w320">
            </div>
            <div id="login_btn">
                <button type="submit" class="imgover">
                    <img src="{{ asset('img/login/bt_login.jpg') }}" alt="ログイン">
                </button>
                <a href="{{ route('password.request') }}">パスワードお忘れの方</a>
            </div>
            @if (Session::has('error'))
                <span class="must">{{ Session::get('error') }}</span>
            @endif
        </div>
    </form>
</div>
@endsection
