@extends('layout.app')

@section('content')
<div class="user_reset_box">
    <form method="POST" action="{{ route('users.pass_edit') }}">
        @csrf
        <div class="user_reset_area">
            <table cellspacing="0" cellpadding="0" border="0" width="600">
                <tr>
                    <th>パスワード</th>
                    <td>
                        <input type="password" name="EDIT_PASSWORD" class="w200">
                        @error('EDIT_PASSWORD')
                            <br /><span class="must">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th>パスワード確認</th>
                    <td>
                        <input type="password" name="EDIT_PASSWORD_confirmation" class="w200">
                        @error('EDIT_PASSWORD')
                            <br /><span class="must">{{ $message }}</span>
                        @enderror
                    </td>
                </tr>
            </table>
            <div class="user_reset_btn">
                <div class="submit">
                    <button type="submit">保存</button>
                </div>
            </div>
        </div>
        <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
        <input type="hidden" name="key" value="{{ $key }}">
    </form>
</div>
@endsection
