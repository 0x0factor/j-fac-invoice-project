@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<script type="text/javascript">
<!--
	function customer_reset() {
		$('#SETCUSTOMER').children('input[type=text]').val('');
		$('#SETCUSTOMER').children('input[type=hidden]').val(0);
		$("#INSERT_ADDRESS").html("");
		return false;
	}
// -->
</script>
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは取引先担当者登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると取引先担当者の変更を保存できます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="edit_01_c_charge"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <form method="POST" action="{{ route('customer_charge.store') }}" class="CustomerCharge">
                @csrf
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS" class="form-control">
                                @foreach($status as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:150px;">顧客名</th>
                        <td id="SETCUSTOMER" style="width:730px;">
                            @if($user['USR_ID'] == $customerCharge['USR_ID'])
                                <input type="text" name="CUSTOMER_NAME" value="{{ $customer }}" readonly class="w130">
                                <input type="hidden" name="CST_ID">
                                <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                    <img src="{{ asset('img/bt_registered.jpg') }}" alt="">
                                </a>
                                <a href="#" onclick="return customer_reset();">
                                    <img src="{{ asset('img/bt_reset.jpg') }}" alt="">
                                </a>
                            @else
                                {{ $customer }}
                                <input type="hidden" name="CST_ID">
                            @endif
                            <br>
                            <span id="INSERT_ADDRESS"></span>
                            <br>
                            <span class="usernavi">{{ $usernavi['CUSTOMER_ADDRESS'] }}</span>
                        </td>
                    </tr>
                    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <tr>
                        <th style="width:150px;" {{ $errors->has('CHARGE_NAME') ? 'class=txt_top' : '' }}>
                            <span class="float_l">担当者名</span>
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                        </th>
                        <td style="width:730px;">
                            <input type="text" name="CHARGE_NAME" value="{{ old('CHARGE_NAME') }}" class="w300{{ $errors->has('CHARGE_NAME') ? ' error' : '' }}" maxlength="60">
                            <br><span class="usernavi">{{ $usernavi['CHARGE_NAME'] }}</span>
                            <br><span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                    </tr>
                    <!-- Continue converting other form fields similarly -->
                </table>
                <div class="edit_btn">
                    <input type="image" src="{{ asset('img/bt_save.jpg') }}" alt="保存する" class="imgover">
                    <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル" class="imgover">
                </div>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>
</div>
<input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
@endsection
