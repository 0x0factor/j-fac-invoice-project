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
    <div class="arrow_under">
        <img src="{{ asset('i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            {{-- Laravel Form Open --}}
            {!! Form::open(['route' => 'customers.store', 'method' => 'post', 'class' => 'Customer']) !!}
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th style="width:130px;" class="{{ $errors->has('NAME') ? 'txt_top' : '' }}">
                        <span class="float_l">社名</span>
                        {!! Html::image('i_must.jpg', '必須', ['class' => 'pl10 mr10 float_r']) !!}
                    </th>
                    <td style="width:750px;">
                        {!! Form::text('NAME', null, ['class' => 'w300' . ($errors->has('NAME') ? ' error' : ''),
                        'maxlength' => 60]) !!}
                        <br /><span class="usernavi">{{ $usernavi['CMP_NAME'] }}</span>
                        <br /><span class="must">{{ $errors->first('NAME') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="line">{!! Html::image('i_line_solid.gif') !!}</td>
                </tr>
                <tr>
                    <th style="width:130px;" class="{{ $errors->has('NAME_KANA') ? 'txt_top' : '' }}">
                        <span class="float_l">社名カナ</span>
                    </th>
                    <td style="width:750px;">
                        {!! Form::text('NAME_KANA', null, ['class' => 'w300' . ($errors->has('NAME_KANA') ? ' error' :
                        ''), 'maxlength' => 100]) !!}
                        <br /><span class="must">{{ $errors->first('NAME_KANA') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="line">{!! Html::image('i_line_solid.gif') !!}</td>
                </tr>
                <tr>
                    <th class="{{ $errors->has('HONOR_TITLE') ? 'txt_top' : '' }}">
                        <span class="float_l">敬称</span>
                    </th>
                    <td id="HONOR" colspan="3">
                        {!! Form::radio('HONOR_CODE', $honor, null, ['class' => 'ml20 mr5 txt_mid', 'label' => false,
                        'legend' => false]) !!}
                        {!! Form::text('HONOR_TITLE', null, ['class' => 'w160 mr10' . ($errors->has('HONOR_TITLE') ? '
                        error' : ''), 'maxlength' => 8]) !!}
                        <br /><span class="usernavi">{{ $usernavi['HONOR'] }}</span>
                        <br /><span class="must">{{ $errors->first('HONOR_TITLE') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="line">{!! Html::image('i_line_solid.gif') !!}</td>
                </tr>
                <tr>
                    <th style="width:130px;"
                        class="{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}">
                        <span class="float_l">郵便番号</span>
                    </th>
                    <td style="width:750px;">
                        {!! Form::text("POSTCODE1", null, ['class' => 'w60' . ($errors->has('POSTCODE1') ||
                        $errors->has('POSTCODE2') ? ' error' : ''), 'maxlength' => 3]) !!}
                        <span class="pl5 pr5">-</span>
                        {!! Form::text("POSTCODE2", null, ['class' => 'w60' . ($errors->has('POSTCODE1') ||
                        $errors->has('POSTCODE2') ? ' error' : ''), 'maxlength' => 4]) !!}
                        <div>{!! $ajax->div("target") !!}{!! $ajax->divEnd("target") !!}</div>
                        <br /><span class="usernavi">{{ $usernavi['POSTCODE'] }}</span>
                        <br /><span class="must">
                            @if ($errors->has('POSTCODE1'))
                            {{ $errors->first('POSTCODE1') }}
                            @elseif ($errors->has('POSTCODE2'))
                            {{ $errors->first('POSTCODE2') }}
                            @endif
                        </span>
                    </td>
                </tr>
                <!-- Continue converting remaining table rows -->
            </table>

            {{-- Laravel Form Close --}}
            {!! Form::close() !!}
        </div>
        <img src="{{ asset('bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>

    <div class="arrow_under">
        <img src="{{ asset('i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="company_02"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box">
        <span class="usernavi">{{ $usernavi['CUSTOMER_PAYMENT'] }}</span>
        <img src="{{ asset('images/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th style="width:130px;" {{ $form->error("CUTOOFF_DATE") ? ' class="txt_top"' : '' }}>締日</th>
                    <td style="width:750px;">
                        {{ $form->input('CUTOOFF_SELECT', $cutooff_select, ['class' => 'txt_mid']) }}
                        {{ $form->text("CUTOOFF_DATE", ['class' => 'w60 mr5 ml5' . ($form->error('CUTOOFF_DATE') ? ' error' : ''), 'maxlength' => 2]) }}日
                        <br><span class="usernavi">{{ $usernavi['CST_CUTOOFF'] }}</span>
                        <br><span class="must">{{ $form->error('CUTOOFF_DATE') }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th{{ $form->error("PAYMENT_DAY") ? ' class="txt_top"' : '' }}>支払日</th>
                        <td>
                            {{ $form->input('PAYMENT_MONTH', null, ['label' => false, 'div' => false, 'options' => $payment, 'class' => 'w120 mr20', 'size' => '1']) }}
                            {{ $form->input('PAYMENT_SELECT', $payment_select) }}
                            {{ $form->text("PAYMENT_DAY", ['class' => 'w60 mr5 ml5' . ($form->error('PAYMENT_DAY') ? ' error' : ''), 'maxlength' => 2]) }}日
                            <br><span class="usernavi">{{ $usernavi['CST_PAYMENT'] }}</span>
                            <br><span class="must">{{ $form->error('PAYMENT_DAY') }}</span>
                        </td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th>消費税設定</th>
                    <td>{{ $form->input('EXCISE', $excises) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th>消費税端数処理</th>
                    <td>{{ $form->input('TAX_FRACTION', $fractions) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th>消費税端数計算</th>
                    <td>{{ $form->input('TAX_FRACTION_TIMING', $tax_fraction_timing) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th>基本端数処理</th>
                    <td>{{ $form->input('FRACTION', $fractions) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="line"><img src="{{ asset('images/i_line_solid.gif') }}" alt=""></td>
                </tr>
                <tr>
                    <th class="txt_top">備考</th>
                    <td>
                        {{ $form->textarea('NOTE', ['class' => 'textarea' . ($form->error('NOTE') ? ' error' : ''), 'maxlength' => 1000]) }}
                        <br><span class="must">{{ $form->error('NOTE') }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('images/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>


    <div class="edit_btn">
        <button type="submit" name="submit" class="imgover">
            <img src="{{ asset('path/to/bt_save.jpg') }}" alt="保存する">
        </button>
        <button type="submit" name="cancel" class="imgover">
            <img src="{{ asset('path/to/bt_cancel.jpg') }}" alt="キャンセル">
        </button>
    </div>
</div>

<form method="POST" action="{{ route('your.route.name') }}">
    @csrf

    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
</form>
@endsection
