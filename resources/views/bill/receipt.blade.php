@extends('layout.default')

@section('content')
<!-- Flash Messages -->
@if(session()->has('flash'))
    {{ session('flash') }}
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
        <p>こちらのページは領収書作成画面です。<br />必要な情報を入力の上「保存する」ボタンを押すと領収書のPDFが出力できます。</p>
    </div>
</div>
<br class="clear">

<!-- contents_Start -->
<div id="contents">
    <form action="{{ route('receipt') }}" method="POST" class="Receipt">
        @csrf
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>

        <h3><div class="edit_01_receipt"><span class="edit_txt">&nbsp;</span></div></h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th{{ isset($error['CST_ID']) ? ' class="txt_top"' : '' }}>顧客名
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td width="320">
                            <input type="text" name="CST_ID" value="{{ $companys }}" class="w180 p2{{ isset($error['CST_ID']) ? ' error' : '' }}" maxlength="60">
                            <br><span class="usernavi">{{ $usernavi['CMP_NAME'] }}</span>
                            <br><span class="must">{{ isset($error['CST_ID']) ? $error['CST_ID'] : '' }}</span>
                        </td>
                        <th{{ isset($error['TOTAL']) ? ' class="txt_top"' : '' }}>金額
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td width="320">
                            <input type="text" name="TOTAL" class="w180 p2{{ isset($error['TOTAL']) ? ' error' : '' }}" readonly maxlength="16">
                            <br><span class="must">{{ isset($error['TOTAL']) ? $error['TOTAL'] : '' }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th>発行日</th>
                        <td width="320">
                            <input type="text" name="DATE" class="w100 p2 date cal{{ $errors->has('ISSUE_DATE') ? ' error' : '' }}" readonly onchange="cal1.getFormValue(); cal1.hide();">
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" onclick="return cal1.write();" class="pl5">
                            <div id="calid"></div>
                            <br><span class="usernavi">{{ $usernavi['DATE'] }}</span>
                        </td>
                        <th{{ isset($error['RECEIPT_NUMBER']) ? ' class="txt_top"' : '' }}>番号
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td width="320">
                            <input type="text" name="RECEIPT_NUMBER" class="w180 p2{{ isset($error['RECEIPT_NUMBER']) ? ' error' : '' }}" maxlength="20">
                            <br><span class="usernavi">{{ $usernavi['RECEIPT_NUMBER'] }}</span>
                            <br><span class="must">{{ isset($error['RECEIPT_NUMBER']) ? $error['RECEIPT_NUMBER'] : '' }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th{{ isset($error['PROVISO']) ? ' class="txt_top"' : '' }}>但書き
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td colspan="3">
                            <input type="text" name="PROVISO" class="w440 mr10{{ isset($error['PROVISO']) ? ' error' : '' }}" maxlength="40">
                            <br><span class="usernavi">{{ $usernavi['PROVISO'] }}</span>
                            <br><span class="must">{{ isset($error['PROVISO']) ? $error['PROVISO'] : '' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>

        <div class="edit_btn">
            <input type="submit" name="submit" value="保存する" class="imgover imgcheck">
            <input type="submit" name="cancel" value="キャンセル" class="imgover imgcheck">
        </div>
        <input type="hidden" name="USR_ID">
        <input type="hidden" name="MBL_ID">
    </form>
</div>
<!-- contents_End -->
@endsection
