@extends('layout.default')

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
    @if (Session::has('flash_message'))
        <div class="flash-message">
            {{ Session::get('flash_message') }}
        </div>
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
            <p>こちらのページは環境設定編集の画面です。@if ($user['AUTHORITY'] == 0)
                    <br />必要な情報を入力の上「編集する」ボタンを押下すると環境設定を変更できます。
                @endif
            </p>
        </div>
    </div>
    <br class="clear" />
    <!-- header_End -->
    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}"></div>
        <h3>
            <div class="edit_02_edit_mail"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}">
            <div class="contents_area">
                <form action="{{ url('configurations') }}" method="POST" enctype="multipart/form-data"
                    class="Configuration">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:160px;"><span class="float_l">送信者名</span><img
                                    src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                            <td style="width:720px;">
                                <input type="text" name="FROM_NAME" value="{{ old('FROM_NAME') }}"
                                    class="w300{{ $errors->has('FROM_NAME') ? ' error' : '' }}" maxlength="60">
                                <br /><span class="usernavi">{{ $usernavi['MAIL_FROM_NAME'] }}</span>
                                <br /><span class="must">{{ $errors->first('FROM_NAME') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr>
                            <th style="width:160px;"><span class="float_l">送信者アドレス</span><img
                                    src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                            <td style="width:720px;">
                                <input type="text" name="FROM" value="{{ old('FROM') }}"
                                    class="w300{{ $errors->has('FROM') ? ' error' : '' }}" maxlength="30">
                                <br /><span class="usernavi">{{ $usernavi['MAIL_FROM'] }}</span>
                                <br /><span class="must">{{ $errors->first('FROM') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr>
                            <th style="width:160px;"><span class="float_l">SMTPの使用</span><img
                                    src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                            <td style="width:720px;">
                                <select name="STATUS">
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('STATUS') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <br /><span class="usernavi">{{ $usernavi['MAIL_SMTP'] }}</span>
                            </td>
                        </tr>
                    </table>
                    <div class='Smtpuse'>
                        <table>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th style="width:170px;"><span class="float_l">プロトコル</span><img
                                        src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                                <td style="width:710px;">
                                    <input type="text" name="PROTOCOL" value="{{ old('PROTOCOL') }}">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th style="width:170px;"><span class="float_l">SMTPセキュリティ</span><img
                                        src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                                <td style="width:710px;">
                                    <input type="text" name="SECURITY" value="{{ old('SECURITY') }}">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th style="width:170px;"><span class="float_l">SMTPサーバ</span><img
                                        src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                                <td style="width:710px;">
                                    <input type="text" name="HOST" value="{{ old('HOST') }}"
                                        class="w300{{ $errors->has('HOST') ? ' error' : '' }}" maxlength="30">
                                    <br /><span class="must">{{ $errors->first('HOST') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th><span class="float_l">ポート番号</span><img src="{{ asset('img/i_must.jpg') }}"
                                        alt="必須" class="pl10 mr10 float_r"></th>
                                <td style="width:750px;">
                                    <input type="text" name="PORT" value="{{ old('PORT') }}"
                                        class="w300{{ $errors->has('PORT') ? ' error' : '' }}" maxlength="30">
                                    <br /><span class="must">{{ $errors->first('PORT') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th>SMTPユーザ</th>
                                <td style="width:750px;">
                                    <input type="text" name="USER" value="{{ old('USER') }}"
                                        class="w300{{ $errors->has('USER') ? ' error' : '' }}" maxlength="30">
                                    <br /><span class="usernavi">{{ $usernavi['MAIL_SMTP_USER'] }}</span>
                                    <br /><span class="must">{{ $errors->first('USER') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                            <tr>
                                <th>SMTPパスワード</th>
                                <td style="width:750px;">
                                    <input type="text" name="PASS" value="{{ old('PASS') }}"
                                        class="w300{{ $errors->has('PASS') ? ' error' : '' }}" maxlength="30">
                                    <br /><span class="usernavi">{{ $usernavi['MAIL_SMTP_PW'] }}</span>
                                    <br /><span class="must">{{ $errors->first('PASS') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td>
                            </tr>
                        </table>
                    </div>
                    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
                    <div class="edit_btn">
                        <input type="image" src="{{ asset('img/bt_save.jpg') }}" name="submit" alt="保存する"
                            class="imgover">
                        <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" name="cancel" alt="キャンセル"
                            class="imgover">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <input type="hidden" name="CON_ID" value="1">
@endsection
