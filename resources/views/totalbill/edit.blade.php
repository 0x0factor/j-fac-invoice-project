@extends('layout.default')

@section('content')

<script type="text/javascript">
<!--
	function customer_reset() {
		$('#SETCUSTOMER').children('input[type=text]').val('');
		$('#SETCUSTOMER').children('input[type=hidden]').val('');
		return false;
	}

	function cstchr_reset() {
		$('#SETCUSTOMERCHARGE').children('input[type=text]').val('');
		$('#SETCUSTOMERCHARGE').children('input[type=text]').removeAttr('readonly')
		$('#SETCUSTOMERCHARGE').children('input[type=hidden]').val('');
		$('#SETCCUNIT').children('input[type=text]').val('');
		$('#SETCCUNIT').children('input[type=text]').removeAttr('readonly')
		return false;
}
// -->
</script>
@extends('layouts.app')

@section('content')

<div id="guide">
	<div id="guide_box" class="clearfix">
		<img src="{{ asset('img/i_guide02.jpg') }}" alt="Guide">
		<p>こちらのページは合計請求書編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押すと合計請求書を作成できます。</p>
	</div>
</div>
<br class="clear" />

<!-- contents_Start -->
<div id="contents">
    <form method="POST" action="{{ route('totalbill.update', $totalbill->id) }}" class="Totalbill">
        @csrf
        @method('PUT')
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow"></div>
        <h3><div class="edit_01"><span class="edit_txt">&nbsp;</span></div></h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th class="{{ $errors->has('NO') ? 'txt_top' : '' }}">管理番号</th>
                        <td width="320">
                            <input type="text" name="NO" value="{{ old('NO', $totalbill->NO) }}" class="w180 p2 {{ $errors->has('NO') ? 'error' : '' }}" maxlength="20">
                            <br /><span class="usernavi">{{ $usernavi['NO'] }}</span>
                            <br /><span class="must">{{ $errors->first('NO') }}</span>
                        </td>
                        <th class="{{ $errors->has('DATE') ? 'txt_top' : '' }}"><span class="float_l">発行日</span><img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                        <td width="320">
                            <input type="text" id="issue_date" name="DATE" value="{{ old('DATE', $totalbill->DATE) }}" class="w100 p2 date cal {{ $errors->has('DATE') ? 'error' : '' }}" readonly>
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" id="show_calendar">
                            <div id="calid"></div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td><span class="usernavi">{{ $usernavi['DATE'] }}</span>
                        <br /><span class="must">{{ $errors->first('ISSUE_DATE') }}</span></td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th class="{{ $errors->has('SUBJECT') ? 'txt_top' : '' }}"><span class="float_l">件名</span><img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                        <td colspan="3">
                            <input type="text" name="SUBJECT" value="{{ old('SUBJECT', $totalbill->SUBJECT) }}" class="w320 mr10 {{ $errors->has('SUBJECT') ? 'error' : '' }}" maxlength="80" onkeyup="count_str('subject_rest', value, 40)">
                            <span id="subject_rest"></span>
                            <br /><span class="usernavi">{{ $usernavi['SUBJECT'] }}</span>
                            <br /><span class="must">{{ $errors->first('SUBJECT') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th class="{{ $errors->has('CST_ID') ? 'txt_top' : '' }}"><span class="float_l">顧客名</span><img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                        <td id="SETCUSTOMER" colspan="3">
                            <input type="text" name="CUSTOMER_NAME" value="{{ old('CUSTOMER_NAME', $totalbill->CUSTOMER_NAME) }}" class="w130 {{ $errors->has('CST_ID') ? 'error' : '' }}" readonly>
                            <input type="hidden" name="CST_ID" value="{{ old('CST_ID', $totalbill->CST_ID) }}">
                            <a href="#" onclick="return popupclass.popupajax('select_customer');"><img src="{{ asset('img/bt_select2.jpg') }}" alt="Select"></a>
                            <a href="#" onclick="return customer_reset();"><img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete"></a>
                            <br /><span class="usernavi">{{ $usernavi['CST_ID'] }}</span>
                            <br /><span class="must">{{ $errors->first('CST_ID') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th style="width:170px;" class="{{ $errors->has('NO') ? 'txt_top' : '' }}">顧客担当者名</th>
                        <td style="width:270px;" id="SETCUSTOMERCHARGE">
                            <input type="text" name="CUSTOMER_CHARGE_NAME" value="{{ old('CUSTOMER_CHARGE_NAME', $totalbill->CUSTOMER_CHARGE_NAME) }}" class="w120 p2 {{ isset($error['CUSTOMER_CHARGE_NAME']) ? 'error' : '' }}" readonly>
                            <input type="hidden" name="CHRC_ID" value="{{ old('CHRC_ID', $totalbill->CHRC_ID) }}">
                            <a href="#" onclick="return popupclass.popupajax('customer_charge');"><img src="{{ asset('img/bt_select2.jpg') }}" alt="Select"></a>
                            <a href="#" onclick="return cstchr_reset();"><img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete"></a>
                            <br /><span class="must">{{ $error['CUSTOMER_CHARGE_NAME'] }}</span>
                        </td>
                        <th style="width:170px;">担当者部署名</th>
                        <td style="width:270px;" id="SETCCUNIT">
                            <input type="text" name="CUSTOMER_CHARGE_UNIT" value="{{ old('CUSTOMER_CHARGE_UNIT', $totalbill->CUSTOMER_CHARGE_UNIT) }}" class="w180 p2 {{ isset($error['CUSTOMER_CHARGE_UNIT']) ? 'error' : '' }}" readonly>
                            <br /><span class="must">{{ $error['CUSTOMER_CHARGE_UNIT'] }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th class="{{ $errors->has('HONOR_TITLE') ? 'txt_top' : '' }}"><span class="float_l">敬称</span></th>
                        <td id="HONOR" colspan="3">
                            @foreach($honor as $key => $value)
                                <label class="ml20 mr5 txt_mid">
                                    <input type="radio" name="HONOR_CODE" value="{{ $key }}" {{ old('HONOR_CODE', $totalbill->HONOR_CODE) == $key ? 'checked' : '' }}>
                                    {{ $value }}
                                </label>
                            @endforeach
                            <input type="text" name="HONOR_TITLE" value="{{ old('HONOR_TITLE', $totalbill->HONOR_TITLE) }}" class="w160 mr10 {{ $errors->has('HONOR_TITLE') ? 'error' : '' }}" maxlength="8">
                            <br /><span class="usernavi">{{ $usernavi['HONOR'] }}</span>
                            <br /><span class="must">{{ $errors->first('HONOR_TITLE') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th class="{{ $errors->has('DUE_DATE') ? 'txt_top' : '' }}"><span class="float_l">振込期限</span></th>
                        <td colspan="3">
                            <input type="text" name="DUE_DATE" value="{{ old('DUE_DATE', $totalbill->DUE_DATE) }}" class="w100 p2 date cal {{ $errors->has('DUE_DATE') ? 'error' : '' }}" readonly>
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" id="show_calendar2">
                            <div id="calid2"></div>
                            <br /><span class="usernavi">{{ $usernavi['DUE_DATE'] }}</span>
                            <br /><span class="must">{{ $errors->first('DUE_DATE') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <th class="{{ $errors->has('MEMO') ? 'txt_top' : '' }}"><span class="float_l">備考</span><img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                        <td colspan="3">
                            <textarea name="MEMO" class="w750 p3 {{ $errors->has('MEMO') ? 'error' : '' }}" rows="8" onkeyup="count_str('memo_rest', value, 100)">{{ old('MEMO', $totalbill->MEMO) }}</textarea>
                            <span id="memo_rest"></span>
                            <br /><span class="usernavi">{{ $usernavi['MEMO'] }}</span>
                            <br /><span class="must">{{ $errors->first('MEMO') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td></tr>
                    <tr>
                        <td colspan="4" class="center"><input type="image" src="{{ asset('img/bt_save.jpg') }}" alt="保存する" class="btn_save"></td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_btm.jpg') }}" alt="Background">
        </div>
    </form>
</div>

<!-- JavaScript to initialize the calendar -->
<script>
    $(document).ready(function() {
        $('#issue_date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $('#due_date').datepicker({
            dateFormat: 'yy-mm-dd'
        });
    });

    function count_str(id, value, limit) {
        var remaining = limit - value.length;
        $('#' + id).text('残り ' + remaining + ' 文字');
    }
</script>

@endsection
