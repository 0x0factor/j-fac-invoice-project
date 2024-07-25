@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
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

@if (session()->has('flash'))
    {{ session('flash') }}
@endif

<form action="{{ route('delivery.index') }}" method="get">
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="quote_search"><span class="edit_txt">&nbsp;</span></div>
    </h3>
    <div class="quote_search_box">
        <div class="quote_search_area">
            <table width="940" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th>管理番号</th>
                    <td><input type="text" name="NO" class="w300"></td>
                    <th>件名</th>
                    <td><input type="text" name="SUBJECT" class="w300"></td>
                </tr>
                <tr>
                    <th>顧客名</th>
                    <td><input type="text" name="NAME" class="w300"></td>
                    <th>自社担当者</th>
                    <td colspan="3"><input type="text" name="CHR_USR_NAME" class="w300"></td>
                </tr>
                <tr>
                    <th>作成者</th>
                    <td><input type="text" name="USR_NAME" class="w300"></td>
                    <th>更新者</th>
                    <td><input type="text" name="UPD_USR_NAME" class="w300"></td>
                </tr>
                <tr>
                    <th>発行ステータス</th>
                    <td colspan="3">
                        <select name="STATUS[]" multiple class="w300">
                            @foreach ($status as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>

            <div class="quote_extend">
                <div class="quote_extend_btn" id="quote_open_btn">
                    <img src="{{ asset('img/button/d_down.png') }}" class="imgover" alt="off" onclick="toggle_quote_extend_open();">
                    詳細検索を表示する
                </div>
                <div class="quote_extend_btn" id="quote_close_btn" style="display:none;">
                    <img src="{{ asset('img/button/d_up.png') }}" class="imgover" alt="off" onclick="toggle_quote_extend_close();">
                    詳細検索を非表示にする
                </div>
                <div class="quote_extend_area">
                    <table width="940" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>商品名</th>
                            <td><input type="text" name="ITEM_NAME" class="w300"></td>
                            <th>商品コード</th>
                            <td><input type="text" name="ITEM_CODE" class="w300"></td>
                        </tr>
                        <tr>
                            <th>合計金額</th>
                            <td><input type="text" name="TOTAL_FROM" class="w100"> 円 ～ <input type="text" name="TOTAL_TO" class="w100"> 円</td>
                        </tr>
                        <tr>
                            <th>発行日 開始日</th>
                            <td width="320">
                                <input type="text" name="ACTION_DATE_FROM" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal1.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime">
                            </td>
                            <th>発行日 終了日</th>
                            <td width="320">
                                <input type="text" name="ACTION_DATE_TO" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal2.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime">
                            </td>
                        </tr>
                        <tr>
                            <th>備考</th>
                            <td><input type="text" name="NOTE" class="w300"></td>
                            <th>メモ</th>
                            <td><input type="text" name="MEMO" class="w300"></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="quote_search_btn">
                <table style="margin:0 auto">
                    <tr>
                        <td style="border:none;">
                            <a href="#" onclick="document.querySelector('#{{ $this->name . ucfirst($this->action) }}Form').submit(); return false;">
                                <img src="{{ asset('img/bt_search.jpg') }}" alt="" class="imgover">
                            </a>
                        </td>
                        <td style="border:none;">
                            <a href="#" onclick="reset_forms(); return false;">
                                <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="" class="imgover">
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</form>

<div class="new_document">
    <a href="{{ route('delivery.add') }}">
        <img src="{{ asset('img/bt_new.jpg') }}" alt="" class="imgover">
    </a>
    <a href="{{ route('delivery.export') }}">
        <img src="{{ asset('img/bt_excel.jpg') }}" alt="" class="imgover">
    </a>
</div>

<h3>
    <div class="edit_02_deliver"><span class="edit_txt">&nbsp;</span></div>
</h3>

<div class="contents_box mb40">
    <div id='pagination'>
        {{ $paginator->data['Delivery']['count'] }}
        {{ $paginator->total() }}件中 0 - 0 件を表示
    </div>

    <div id='pagination'>
        <!-- Previous Page Link -->
        @if ($paginator->onFirstPage())
            <span class="disabled"><< {{ __('前へ') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><< {{ __('前へ') }}</a>
        @endif

        <!-- Pagination Elements -->
        @foreach ($paginator->links()->elements as $element)
            <!-- "Three Dots" Separator -->
            @if (is_string($element))
                <span class="disabled">{{ $element }}</span>
            @endif

            <!-- Array Of Links -->
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <!-- Next Page Link -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
        @else
            <span class="disabled">{{ __('次へ') }} >></span>
        @endif
    </div>


    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="" />

    <div class="list_area">
        @if (!empty($list))
            <form action="{{ route('delivery.action') }}" method="post">
                <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                    <thead>
                        <tr>
                            <th class="w50"><input type="checkbox" name="action.select_all" class="chk_all" onclick="select_all();"></th>
                            <th class="w50">{{ $customHtml->sortLink('No.', 'Delivery.MDV_ID') }}</th>
                            <th class="w100">{{ $customHtml->sortLink('顧客名', 'Customer.NAME_KANA') }}</th>
                            <th class="w150">{{ $customHtml->sortLink('件名', 'Delivery.SUBJECT') }}</th>
                            <th class="w100">{{ $customHtml->sortLink('合計金額', 'Delivery.CAST_TOTAL') }}</th>
                            <th class="w100">{{ $customHtml->sortLink('発行日', 'Delivery.ISSUE_DATE') }}</th>
                            @if ($user['AUTHORITY'] != 1)
                                <th class="w150">{{ $customHtml->sortLink('作成者', 'Delivery.USR_ID') }} /
                                    {{ $customHtml->sortLink('更新者', 'Delivery.UPDATE_USR_ID') }}
                                </th>
                            @endif
                            <th class="w100">{{ $customHtml->sortLink('発行ステータス', 'Delivery.STATUS') }}</th>
                            <th class="w100">メモ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($list as $key => $val)
                            <tr>
                                <td><input type="checkbox" name="{{ $val['Delivery']['MDV_ID'] }}" class="chk"></td>
                                {{-- Assuming $authcheck is defined --}}
                                @if (isset($authcheck[$key]))
                                    <div class="auth{{ $val['Delivery']['MDV_ID'] }}" style="display:none;">
                                        {{ $authcheck[$key] }}
                                    </div>
                                @endif
                                <td>{{ $customHtml->ht2br($val['Delivery']['MDV_ID'], 'Delivery', 'MDV_ID') }}</td>
                                <td>{{ $customHtml->ht2br($val['Customer']['NAME'], 'Customer', 'NAME') }}</td>
                                <td>
                                    <a href="{{ route('delivery.check', $val['Delivery']['MDV_ID']) }}">
                                        {{ $val['Delivery']['SUBJECT'] }}
                                    </a>
                                </td>
                                <td>
                                    {{ isset($val['Delivery']['TOTAL']) ? $customHtml->ht2br($val['Delivery']['TOTAL'], 'Delivery', 'TOTAL') . '円' : '&nbsp;' }}
                                </td>
                                <td>{{ $val['Delivery']['ISSUE_DATE'] ? $val['Delivery']['ISSUE_DATE'] : '&nbsp;' }}</td>
                                @if ($user['AUTHORITY'] != 1)
                                    <td>
                                        {{ $customHtml->ht2br($val['User']['NAME'], 'Delivery', 'NAME') }} /
                                        {{ $val['UpdateUser']['NAME'] ?? '&nbsp;' }}
                                    </td>
                                @endif
                                <td>{{ $status[$val['Delivery']['STATUS']] }}</td>
                                <td>{{ $val['Delivery']['MEMO'] ? $customHtml->ht2br($val['Delivery']['MEMO'], 'Delivery', 'MEMO') : '&nbsp;' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="list_btn">
                    <button type="submit" name="delete" class="mr5">
                        <img src="{{ asset('img/document/bt_delete2.jpg') }}" alt="削除">
                    </button>
                    <button type="submit" name="reproduce_quote" class="mr5">
                        <img src="{{ asset('img/bt_01.jpg') }}" alt="複製">
                    </button>
                    <button type="submit" name="reproduce_bill" class="mr5">
                        <img src="{{ asset('img/bt_02.jpg') }}" alt="複製">
                    </button>
                    <button type="submit" name="reproduce_delivery" class="mr5">
                        <img src="{{ asset('img/bt_03.jpg') }}" alt="複製">
                    </button>

                    {{-- Include status_change element --}}
                    @include('status_change')

                    {{-- Hidden fields --}}
                    @if (isset($customer_id))
                        <input type="hidden" name="Customer.id" value="{{ $customer_id }}">
                    @endif
                    {{ $customHtml->hiddenToken() }}
                </div>
            </form>
        @endif
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>
</div>
@endsection
