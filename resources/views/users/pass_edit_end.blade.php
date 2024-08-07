@extends('layout.app')

@section('content')
<div class="user_reset_box">
    <div class="user_reset_area">
        <div class="message">
            パスワード再設定完了致しました。
            <a href="{{ route('login') }}">ログインページへ</a>
        </div>
    </div>
    <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
</div>
@endsection
