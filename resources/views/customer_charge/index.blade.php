@extends('layout.default')

@section('content')
<script><!--
try{
	window.addEventListener("load",initTableRollovers('index_table'),false);
 }catch(e){
 	window.attachEvent("onload",initTableRollovers('index_table'));
}
--></script>
<script><!--
function select_all() {
	$(".chk").attr("checked", $(".chk_all").attr("checked"));
	$('input[name="delete"]').attr('disabled','');
	$('input[name="reproduce"]').attr('disabled','');
}

--></script>
<script>
$(function() {
	setBeforeSubmit('<?php echo $this->name.ucfirst($this->action).'Form'; ?>');
});
</script>

@if (session()->has('status'))
    {{ session('status') }}
@endif

<form method="GET" action="{{ route('customer_charge.index') }}">
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>

        <h3>
            <div class="search">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>

        <div class="search_box">
            <div class="search_area">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>担当者名</th>
                        <td>
                            <input type="text" name="CHARGE_NAME" value="{{ request('CHARGE_NAME') }}" class="w350">
                        </td>
                    </tr>
                    <tr>
                        <th>企業名</th>
                        <td>
                            <input type="text" name="COMPANY_NAME" value="{{ request('COMPANY_NAME') }}" class="w350">
                        </td>
                    </tr>
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS" class="form-control">
                                <option value="" {{ empty(request('STATUS')) ? 'selected' : '' }}>項目を選んでください</option>
                                @foreach ($status as $key => $value)
                                    <option value="{{ $key }}" {{ request('STATUS') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('searchForm').submit();">
                                    <img src="{{ asset('bt_search.jpg') }}" alt="">
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();">
                                    <img src="{{ asset('bt_search_reset.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
</form>

<div class="new_document">
    <a href="{{ route('customer_charge.add') }}">
        <img src="{{ asset('bt_new.jpg') }}" alt="">
    </a>
</div>

<h3>
    <div class="edit_02_c_charge">
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box mb40">
    <div id='pagination'>
        {{ $paginator->total() }}
    </div>
    <div id='pagination'>
        {{ $list->links() }}
    </div>

    <img src="{{ asset('bg_contents_top.jpg') }}" alt="">
    <div class="list_area">
        @if ($list->count() > 0)
            <form method="POST" action="{{ route('customer_charge.index') }}" id="indexForm">
                @csrf
                <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                    <thead>
                    <tr>
                        <th class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                        <th class="w50">{{ $customHtml->sortLink('No.', 'CustomerCharge.CHRC_ID') }}</th>
                        <th class="w200">{{ $customHtml->sortLink('担当者', 'CustomerCharge.CHARGE_NAME') }}</th>
                        <th class="w250">{{ $customHtml->sortLink('顧客名', 'CustomerCharge.CST_ID') }}</th>
                        <th class="w100">{{ $customHtml->sortLink('電話番号', 'CustomerCharge.PHONE_NO1') }}</th>
                        <th class="w100">{{ $customHtml->sortLink('ステータス', 'CustomerCharge.STATUS') }}</th>
                        @if ($user['AUTHORITY'] != 1)
                            <th class="w100">{{ $customHtml->sortLink('作成者', 'CustomerCharge.USR_ID') }}</th>
                        @endif
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($list as $key => $val)
                        <tr>
                            <td>
                                @if (!isset($delcheck[$val['CustomerCharge']['CHRC_ID']]) || !$delcheck[$val['CustomerCharge']['CHRC_ID']])
                                    <input type="checkbox" name="{{ $val['CustomerCharge']['CHRC_ID'] }}" class="chk">
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            <td>{{ $val['CustomerCharge']['CHRC_ID'] }}</td>
                            <td>
                                @php
                                    $chargeNameLink = $authcheck[$val['CustomerCharge']['CHRC_ID']] ? route('customer_charge.check', $val['CustomerCharge']['CHRC_ID']) : null;
                                @endphp
                                {!! $chargeNameLink ? '<a href="'.$chargeNameLink.'">' : '' !!}
                                {{ $customHtml->ht2br($val['CustomerCharge']['CHARGE_NAME'], 'CustomerCharge', 'CHARGE_NAME') }}
                                {!! $chargeNameLink ? '</a>' : '' !!}
                            </td>
                            <td>{{ $val['CustomerCharge']['CST_ID'] != 0 ? $customer[$val['CustomerCharge']['CST_ID']] : "&nbsp;" }}</td>
                            <td>
                                @if (!empty($val['CustomerCharge']['PHONE_NO1']) || !empty($val['CustomerCharge']['PHONE_NO2']) || !empty($val['CustomerCharge']['PHONE_NO3']))
                                    {{ $val['CustomerCharge']['PHONE_NO1']."-".$val['CustomerCharge']['PHONE_NO2']."-".$val['CustomerCharge']['PHONE_NO3'] }}
                                @else
                                    &nbsp;
                                @endif
                            </td>
                            <td>{{ $status[$val['CustomerCharge']['STATUS']] }}</td>
                            @if ($user['AUTHORITY'] != 1)
                                <td>{{ $val['User']['NAME'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <div class="list_btn">
                    <input type="submit" value="削除" name="delete" class="mr5" onclick="return del();">
                </div>
            </form>
        @endif
    </div>
    <img src="{{ asset('bg_contents_bottom.jpg') }}" class="block" alt="">
</div>
@endsection
