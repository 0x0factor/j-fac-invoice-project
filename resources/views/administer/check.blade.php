<?php // 完了メッセージ
use Illuminate\Support\Facades\Session;
?>
@if (Session::has('message'))
    <div class="alert alert-success">
        {{ Session::get('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_adduser.jpg') }}" alt="Add User">
        <p>こちらのページはユーザ確認の画面です。<br>「編集する」ボタンを押すとユーザを編集できます。</p>
    </div>
</div>
<br class="clear">

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>

    <h3><div class="edit_01_administer"><span class="edit_txt">&nbsp;</span></div></h3>

    {!! Form::open(['url' => route('administers.edit', $params['Administer']['USR_ID']), 'method' => 'post', 'class' => 'Administer']) !!}
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th>ステータス</th>
                    <td>{{ $status[$params['Administer']['STATUS']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">名前</th>
                    <td style="width:710px;">{!! nl2br(e($params['Administer']['NAME'])) !!}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">名前カナ</th>
                    <td style="width:710px;">{!! nl2br(e($params['Administer']['NAME_KANA'])) !!}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">部署名</th>
                    <td style="width:710px;">{!! nl2br(e($params['Administer']['UNIT'])) !!}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">メールアドレス</th>
                    <td style="width:710px;">{!! nl2br(e($params['Administer']['MAIL'])) !!}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">権限</th>
                    <td style="width:710px;">{{ $authority[$params['Administer']['AUTHORITY']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">ユーザID</th>
                    <td style="width:710px;">{!! nl2br(e($params['Administer']['LOGIN_ID'])) !!}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td></tr>
                <tr>
                    <th style="width:170px;">パスワード</th>
                    <td style="width:710px;">************</td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
    </div>
    <div class="edit_btn">
        <a href="{{ route('administers.edit', $params['Administer']['USR_ID']) }}" class="imgover">
            <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
        </a>
        {!! Form::close() !!}
        <form action="{{ route('administers.index') }}" method="get" style="display:inline;">
            <button type="submit" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
            </button>
        </form>
    </div>
</div>
