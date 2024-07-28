@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" />
        <p>こちらのページは自社担当者編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると自社担当者の変更を保存できます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" />
    </div>

    <h3>
        <div class="edit_01">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" />
        <div class="contents_area">
            <form method="POST" action="{{ route('charge.add') }}" enctype="multipart/form-data">
                @csrf
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS">
                                @foreach($status as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>
                    <tr>
                        <th style="width:150px;">担当者名</th>
                        <td style="width:730px;">
                            <input type="text" name="CHARGE_NAME" class="w300" maxlength="60">
                            <br /><span class="usernavi">{{ $status['CHARGE_NAME'] }}</span>
                        </td>
                    </tr>
                    <!-- Continue with other form inputs similarly -->
                </table>

                <div class="SEAL_METHOD">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:150px;">&nbsp;</th>
                            <td>
                                <!-- Handle file upload and other form elements -->
                                <input type="file" name="image">
                                <input type="checkbox" name="DEL_SEAL" style="width:30px;">削除
                                <br /><span class="usernavi">{{ $status['SEAL'] }}</span>
                                <!-- Add error messages handling -->
                            </td>
                        </tr>
                    </table>
                </div>

                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <!-- Additional form fields and sections -->
                </table>

                <div class="edit_btn">
                    <input type="image" src="{{ asset('img/bt_save.jpg') }}" name="submit" alt="保存する" class="imgover">
                    <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" name="cancel" alt="キャンセル" class="imgover">
                </div>

                <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
                <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                <!-- CSRF token -->
                @csrf
                <input type="hidden" name="CHR_ID">
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
    </div>
</div>
@endsection
