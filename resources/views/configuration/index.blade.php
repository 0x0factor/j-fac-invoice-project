@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
@if(session()->has('flash'))
    {{ session('flash') }}
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
        <p>こちらのページは環境設定確認の画面です。</p>
    </div>
</div>
<br class="clear" />

<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Image">
    </div>

    <h3>
        <div class="edit_02_edit_mail"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top Image">

        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th>送信者名</th>
                    <td style="width:750px;">
                        {{ nl2br(e($params['Configuration']['FROM_NAME'] ?? '')) }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                <tr>
                    <th>送信者アドレス</th>
                    <td style="width:750px;">
                        {{ nl2br(e($params['Configuration']['FROM'] ?? '')) }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                <tr>
                    <th style="width:130px;"><span class="float_l">SMTPの使用</span></th>
                    <td style="width:750px;">
                    {{ $status[$params['Configuration']['STATUS'] ?? 'default_key'] ?? '無効' }}
                    </td>
                </tr>
            </table>

            <input type="hidden" name="smtp_frag" value="{{ $params['Configuration']['STATUS'] ?? '' }}">


            <div class="Smtpuse">
                <table>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th style="width:130px;"><span class="float_l">プロトコル</span></th>
                        <td style="width:750px;">
                            {{ isset($params['Configuration']['SECURITY']) && $params['Configuration']['SECURITY'] !== null && isset($protocol[$params['Configuration']['PROTOCOL']]) ? $protocol[$params['Configuration']['PROTOCOL']] : '' }}

                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th style="width:130px;"><span class="float_l">SMTPセキュリティ</span></th>
                        <td style="width:750px;">
                        {{ isset($params['Configuration']['SECURITY']) && $params['Configuration']['SECURITY'] != null ? $security[$params['Configuration']['SECURITY']] : '' }}
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th>SMTPサーバ</th>
                        <td style="width:750px;">
                            {{ isset($params['Configuration']['HOST']) ? nl2br($params['Configuration']['HOST']) : '' }}
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th>ポート番号</th>
                        <td style="width:750px;">
                            {{ isset($params['Configuration']['PORT']) ? nl2br($params['Configuration']['PORT']) : '' }}
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th>SMTPユーザ</th>
                        <td style="width:750px;">
                            {{ isset($params['Configuration']['USER']) ? nl2br($params['Configuration']['USER']) : '' }}
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th>SMTPパスワード</th>
                        <td style="width:750px;">
                            {{ isset($params['Configuration']['PASS']) ? nl2br($params['Configuration']['PASS']) : '' }}
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                </table>
            </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Background Bottom Image" class="block">
    </div>

    <div class="edit_btn">
        @if($user['AUTHORITY'] == 0)
            <a href="{{ url('configurations/edit') }}" class="imgover" title="編集する">
                <img src="{{ asset('img/bt_edit.jpg') }}" alt="Edit Button">
            </a>
        @endif
    </div>
</div>

<input type="hidden" name="CMP_ID" value="1">
@endsection
