@extends('layout.default')

@section('content')
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_adduser.jpg') }}" alt="">
        <p>こちらのページはユーザ登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押すとユーザの変更を保存することができます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3><div class="edit_01_administer"><span class="edit_txt">&nbsp;</span></div></h3>
    <form action="{{ route('administer.edit') }}" method="POST" class="Administer">
        @csrf
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                @push('scripts')

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const radios = document.querySelectorAll('input[type="radio"]');
                            radios.forEach(radio => {
                                radio.addEventListener('click', function () {
                                    if (this.value == 0) {
                                        document.querySelector('div.ps_class').style.display = 'none';
                                        document.querySelector('input[name="EDIT_PASSWORD"]').value = "";
                                        document.querySelector('input[name="EDIT_PASSWORD1"]').value = "";
                                    } else if (this.value == 1) {
                                        document.querySelector('div.ps_class').style.display = 'block';
                                    }
                                });
                            });
                        });
                    </script>
                @endpush
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:170px;">ステータス</th>
                        <td>
                            <select name="STATUS" class="{{ $errors->has('STATUS') ? 'error' : '' }}">
                                @foreach($status as $key => $value)
                                    <option value="{{ $key }}" {{ old('STATUS') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('STATUS')
                                <span class="must">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">名前</th>
                        <td style="width:710px;">
                            <input type="text" name="NAME" value="{{ old('NAME') }}" class="w300 {{ $errors->has('NAME') ? 'error' : '' }}" maxlength="60">
                            <br><span class="must">{{ $errors->first('NAME') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">名前カナ</th>
                        <td style="width:710px;">
                            <input type="text" name="NAME_KANA" value="{{ old('NAME_KANA') }}" class="w300 {{ $errors->has('NAME_KANA') ? 'error' : '' }}" maxlength="60">
                            <br><span class="must">{{ $errors->first('NAME_KANA') }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">部署名</th>
                        <td style="width:710px;">
                            <input type="text" name="UNIT" value="{{ old('UNIT') }}" class="w300 {{ $errors->has('UNIT') ? 'error' : '' }}" maxlength="60">
                            <br><span class="must">{{ $errors->first('UNIT') }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">メールアドレス</th>
                        <td style="width:710px;">
                            <input type="text" name="MAIL" value="{{ old('MAIL') }}" class="w300 {{ $errors->has('MAIL') ? 'error' : '' }}" maxlength="256">
                            <br><span class="must">{{ $errors->first('MAIL') }}</span>
                            @if ($error['MAIL'] == 1)
                                <span class="must">そのメールアドレスは既に使われています</span>
                            @elseif ($error['MAIL'] == 2)
                                <span class="must">有効なメールアドレスではありません</span>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">権限</th>
                        <td style="width:710px;">
                            <select name="AUTHORITY" class="{{ $errors->has('AUTHORITY') ? 'error' : '' }}">
                                @foreach($authority as $key => $value)
                                    <option value="{{ $key }}" {{ old('AUTHORITY') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('AUTHORITY')
                                <span class="must">{{ $message }}</span>
                            @enderror
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:170px;">ユーザID</th>
                        <td style="width:710px;">
                            <input type="text" name="LOGIN_ID" value="{{ old('LOGIN_ID') }}" class="w300 {{ $errors->has('LOGIN_ID') || $error['LOGIN_ID'] == 1 ? 'error' : '' }}" maxlength="10">
                            <br><span class="must">{{ $errors->first('LOGIN_ID') }}</span>
                            @if ($error['LOGIN_ID'] == 1)
                                <span class="must">ユーザIDが既に使用されています</span>
                            @endif
                            <div id="target"></div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="radio" name="CHANGEFLG" value="0" class="ml20 mr5 txt_mid">パスワードを変更しない
                            <input type="radio" name="CHANGEFLG" value="1" class="ml20 mr5 txt_mid">パスワードを変更する
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <div class="ps_class"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></div>
                        </td>
                    </tr>
                    <tr>
                        <th style="width:170px; padding:0px;">
                            <div class="ps_class"><span class="float_l">　パスワード</span></div>
                        </th>
                        <td style="width:710px; padding:0px;">
                            <div class="ps_class">
                                <input type="password" name="EDIT_PASSWORD" class="w300 {{ $errors->has('EDIT_PASSWORD') ? 'error' : '' }}" maxlength="20">
                                <br><span class="must">{{ $errors->first('EDIT_PASSWORD') }}</span>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <div class="ps_class"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></div>
                        </td>
                    </tr>
                    <tr>
                        <th style="width:170px; padding:0px;">
                            <div class="ps_class"><span class="float_l">　パスワード(確認)</span></div>
                        </th>
                        <td style="width:710px; padding:0px;">
                            <div class="ps_class">
                                <input type="password" name="EDIT_PASSWORD1" class="w300 {{ $errors->has('EDIT_PASSWORD1') ? 'error' : '' }}" maxlength="20">
                                <br><span class="must">{{ $errors->first('EDIT_PASSWORD1') }}</span>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>

        <div class="edit_btn">
            <input type="hidden" name="USR_ID" value="{{ $params['Administer']['USR_ID'] }}">
            <input type="submit" name="submit" value="保存する" class="imgover">
            <input type="submit" name="cancel" value="キャンセル" class="imgover">
        </div>
    </form>
</div>
@endsection
