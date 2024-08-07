@extends('layout.app')

@section('content')
<div class="user_reset_box">
    <div class="user_reset_area">
        <div class="message">
            ご入力頂いたメールアドレスへ、パスワードの再設定をするために必要な情報をお送りしました。<br />
            メールを受信されましたら、メールの内容に従い、パスワードの再設定を完了してください。
        </div>
    </div>
    <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
</div>
@endsection
