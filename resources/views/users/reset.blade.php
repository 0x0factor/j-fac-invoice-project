@extends('layout.app')

@section('content')
<div class="user_reset_box">
    <form method="POST" action="{{ route('users.reset') }}">
        @csrf
        <div class="user_reset_area">
            <table cellspacing="0" cellpadding="0" border="0" width="600">
                <tr>
                    <td colspan="2">インストール時、またはユーザ登録時に設定したメールアドレスを入力してください</td>
                </tr>
                <tr>
                    <th>メールアドレス</th>
                    <td>
                        <input type="email" name="email" class="w200" value="{{ old('email') }}">
                        @if ($errors->has('email'))
                            <br><span class="must">{{ $errors->first('email') }}</span>
                        @endif
                    </td>
                </tr>
            </table>
            <div class="user_reset_btn">
                <div class="submit">
                    <button type="submit">メール送信</button>
                </div>
            </div>
        </div>
    </form>
    <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
</div>
@endsection
