@extends('layout.default')

@section('script')
<script type="text/javascript">
<!--
function charge_reset() {
    $('#SETCHARGE').children('input[type=text]').val('');
    $('#SETCHARGE').children('input[type=hidden]').val(0);
    return false;
}
//
-->
</script>
@endsection

@section('content')

<!-- guide section -->
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('/img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは顧客情報設定の画面です。<br>必要な情報を入力の上「保存する」ボタンを押下すると顧客情報を作成できます。</p>
    </div>
</div>
<br class="clear">

<!-- contents section -->
<div id="contents">
    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>

    <h3><div class="edit_01"><span class="edit_txt">&nbsp;</span></div></h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="contents_area">
            <form action="{{ route('customer.store') }}" method="POST" class="Customer">
                @csrf
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('NAME') ? 'txt_top' : '' }}">
                            <span class="float_l">社名</span>
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                        </th>
                        <td style="width:750px;">
                            <input type="text" name="NAME" class="w300{{ $errors->has('NAME') ? ' error' : '' }}" maxlength="60" value="{{ old('NAME') }}">
                            <br /><span class="usernavi">{{ $usernavi['CMP_NAME'] ?? '' }}</span>
                            <br /><span class="must">{{ $errors->first('NAME') }}</span>
                        </td>
                    </tr>
                    <!-- More rows similar to the above for other fields like NAME_KANA, POSTCODE1, POSTCODE2, etc. -->
                    <!-- Example for NAME_KANA -->
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('NAME_KANA') ? 'txt_top' : '' }}">
                            <span class="float_l">社名カナ</span>
                        </th>
                        <td style="width:750px;">
                            <input type="text" name="NAME_KANA" class="w300{{ $errors->has('NAME_KANA') ? ' error' : '' }}" maxlength="100" value="{{ old('NAME_KANA') }}">
                            <br /><span class="must">{{ $errors->first('NAME_KANA') }}</span>
                        </td>
                    </tr>
                    <!-- Continue similarly for other fields -->
                </table>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
    </div>

    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>

    <h3><div class="company_02"><span class="edit_txt">&nbsp;</span></div></h3>

    <div class="contents_box">
        <span class="usernavi">{{ $usernavi['CUSTOMER_PAYMENT'] ?? '' }}</span>
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <!-- More rows similar to the above for other fields like CUTOOFF_DATE, PAYMENT_DAY, EXCISE, etc. -->
                <!-- Example for CUTOOFF_DATE -->
                <tr>
                    <th style="width:130px;" class="{{ $errors->has('CUTOOFF_DATE') ? 'txt_top' : '' }}">締日</th>
                    <td style="width:750px;">
                        <select name="CUTOOFF_SELECT" class="txt_mid">
                            @foreach($cutooff_select as $key => $value)
                                <option value="{{ $key }}" {{ old('CUTOOFF_SELECT') == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="CUTOOFF_DATE" class="w60 mr5 ml5{{ $errors->has('CUTOOFF_DATE') ? ' error' : '' }}" maxlength="2" value="{{ old('CUTOOFF_DATE') }}">日
                        <br /><span class="usernavi">{{ $usernavi['CST_CUTOOFF'] ?? '' }}</span>
                        <br /><span class="must">{{ $errors->first('CUTOOFF_DATE') }}</span>
                    </td>
                </tr>
                <!-- Continue similarly for other fields -->
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
    </div>

    <div class="edit_btn">
        <button type="submit" name="submit" class="imgover"><img src="{{ asset('img/bt_save.jpg') }}" alt="保存する"></button>
        <button type="submit" name="cancel" class="imgover"><img src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル"></button>
    </div>
</div>

<form method="POST" action="{{ route('your.route.name') }}">
    @csrf

    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
</form>
@endsection
