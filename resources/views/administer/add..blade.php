@extends('layouts.default')

@section('content')
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_adduser.jpg') }}" alt="Add User">
            <p>こちらのページはユーザ登録の画面です。<br>必要な情報を入力の上「保存する」ボタンを押すとユーザの変更を保存することができます。</p>
        </div>
    </div>
    <br class="clear">

    <!-- header_End -->

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="edit_01_administer"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <form method="POST" action="{{ route('administer.store') }}" class="Administer">
            @csrf

            <div class="contents_box">
                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
                <div class="contents_area">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:170px;">ステータス</th>
                            <td>
                                <select name="STATUS">
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('NAME') ? 'txt_top' : '' }}">
                                <span class="float_l">名前</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:710px;">
                                <input type="text" name="NAME" class="w300 {{ $errors->has('NAME') ? 'error' : '' }}"
                                    maxlength="60" value="{{ old('NAME') }}">
                                <br><span class="usernavi">{{ $usernavi['USR_NAME'] }}</span>
                                <br><span class="must">{{ $errors->first('NAME') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('NAME_KANA') ? 'txt_top' : '' }}">名前カナ</th>
                            <td style="width:710px;">
                                <input type="text" name="NAME_KANA"
                                    class="w300 {{ $errors->has('NAME_KANA') ? 'error' : '' }}" maxlength="60"
                                    value="{{ old('NAME_KANA') }}">
                                <br><span class="usernavi">{{ $usernavi['USR_NAME_KANA'] }}</span>
                                <br><span class="must">{{ $errors->first('NAME_KANA') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('UNIT') ? 'txt_top' : '' }}">部署名</th>
                            <td style="width:710px;">
                                <input type="text" name="UNIT" class="w300 {{ $errors->has('UNIT') ? 'error' : '' }}"
                                    maxlength="60" value="{{ old('UNIT') }}">
                                <br><span class="usernavi">{{ $usernavi['UNIT'] }}</span>
                                <br><span class="must">{{ $errors->first('UNIT') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('MAIL') ? 'txt_top' : '' }}"><span
                                    class="float_l">メールアドレス</span></th>
                            <td style="width:710px;">
                                <input type="email" name="MAIL" class="w300 {{ $errors->has('MAIL') ? 'error' : '' }}"
                                    maxlength="256" value="{{ old('MAIL') }}">
                                <br><span class="usernavi">{{ $usernavi['USR_MAIL'] }}</span>
                                <br><span class="must">{{ $errors->first('MAIL') }}</span>
                                <br>
                                <span class="must">
                                    @if ($errors->has('MAIL'))
                                        @if ($errors->first('MAIL') == 1)
                                            そのメールアドレスは既に使われています
                                        @elseif ($errors->first('MAIL') == 2)
                                            有効なメールアドレスではありません
                                        @endif
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('AUTHORITY') ? 'txt_top' : '' }}">権限</th>
                            <td style="width:710px;">
                                <select name="AUTHORITY">
                                    @foreach ($authority as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                                <br><span class="usernavi">{{ $usernavi['AUTHORITY'] }}</span>
                                <br><span class="must">{{ $errors->first('AUTHORITY') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('LOGIN_ID') ? 'txt_top' : '' }}">
                                <span class="float_l">ユーザID</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:710px;">
                                <input type="text" name="LOGIN_ID"
                                    class="w300 {{ $errors->has('LOGIN_ID') ? 'error' : '' }}" maxlength="10"
                                    value="{{ old('LOGIN_ID') }}">
                                <br><span class="usernavi">{{ $usernavi['USR_ID'] }}</span>
                                <br><span class="must">{{ $errors->first('LOGIN_ID') }}</span>
                                <div id="target"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('EDIT_PASSWORD') ? 'txt_top' : '' }}">
                                <span class="float_l">パスワード</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:710px;">
                                <input type="password" name="EDIT_PASSWORD"
                                    class="w300 {{ $errors->has('EDIT_PASSWORD') ? 'error' : '' }}" maxlength="20">
                                <br><span class="usernavi">{{ $usernavi['USR_PASSWORD'] }}</span>
                                <br><span class="must">{{ $errors->first('EDIT_PASSWORD') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('EDIT_PASSWORD1') ? 'txt_top' : '' }}">
                                <span class="float_l">パスワード(確認)</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:710px;">
                                <input type="password" name="EDIT_PASSWORD1"
                                    class="w300 {{ $errors->has('EDIT_PASSWORD1') ? 'error' : '' }}" maxlength="20">
                                <br><span class="usernavi">{{ $usernavi['USR_CPASSWORD'] }}</span>
                                <br><span class="must">{{ $errors->first('EDIT_PASSWORD1') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
            </div>

            <div class="edit_btn">
                <input type="submit" name="submit" value="保存する" class="imgover">
                <input type="submit" name="cancel" value="キャンセル" class="imgover">
            </div>
        </form>
    </div>
@endsection
