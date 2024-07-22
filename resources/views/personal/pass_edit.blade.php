@extends('layout.default')

@section('content')
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_adduser.jpg') }}" alt="">
        <p>こちらのページはパスワード変更の画面です。<br>パスワードを入力の上「変更する」ボタンを押すとパスワードを変更することができます。</p>
    </div>
</div>
<br class="clear">

<!-- header_End -->

<!-- contents_Start -->
<div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
<div class="user_reset_box">
    <form action="{{ route('personal.passEdit') }}" method="POST">
        @csrf
        <div class="user_reset_area">
            <table cellspacing="0" cellpadding="0" border="0" width="600">
                <tr>
                    <th>パスワード</th>
                    <td>
                        <input type="password" name="EDIT_PASSWORD" class="w200">
                        <br><span class="usernavi">{{ $usernavi['USR_PASSWORD'] }}</span>
                        <br><span class="must">{{ $errors->first('EDIT_PASSWORD') }}</span>
                    </td>
                </tr>
                <tr>
                    <th>パスワード確認</th>
                    <td>
                        <input type="password" name="EDIT_PASSWORD1" class="w200">
                        <br><span class="usernavi">{{ $usernavi['USR_CPASSWORD'] }}</span>
                        <br><span class="must">{{ $errors->first('EDIT_PASSWORD1') }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <div class="edit_btn">
            @csrf
            <button type="submit" name="submit" class="imgover"><img src="{{ asset('img/bt_change.jpg') }}" alt="変更する"></button>
            <button type="submit" name="cancel" class="imgover"><img src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル"></button>
        </div>
    </form>
</div>
<!-- contents_End -->
@endsection
