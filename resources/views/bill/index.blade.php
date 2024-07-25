@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<script>
document.addEventListener("DOMContentLoaded", function() {
    initTableRollovers('index_table');
});

function selectAll() {
    $(".chk").prop("checked", $(".chk_all").prop("checked"));
    $('input[name="delete"]').prop('disabled', '');
    $('input[name="reproduce"]').prop('disabled', '');
}

$(function() {
    setBeforeSubmit('{{ $formName }}');
});
</script>

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="quote_search">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="quote_search_box">
        <div class="quote_search_area">
            <form method="get" action="{{ route('bill.index') }}">
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
                            @foreach ($status as $key => $value)
                                <input type="checkbox" name="STATUS[]" value="{{ $key }}"> {{ $value }}
                            @endforeach
                        </td>
                    </tr>
                </table>

                <div class="quote_extend">
                    <div class="quote_extend_btn" id="quote_open_btn">
                        <img src="{{ asset('img/button/d_down.png') }}" class="imgover" alt="off" onclick="toggle_quote_extend_open();"> 詳細検索を表示する
                    </div>
                    <div class="quote_extend_btn" id="quote_close_btn" style="display:none;">
                        <img src="{{ asset('img/button/d_up.png') }}" class="imgover" alt="off" onclick="toggle_quote_extend_close();"> 詳細検索を非表示にする
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
                                <td>
                                    <input type="text" name="TOTAL_FROM" class="w100"> 円 ～
                                    <input type="text" name="TOTAL_TO" class="w100"> 円
                                </td>
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
                                <button type="submit">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="検索">
                                </button>
                            </td>
                            <td style="border:none;">
                                <button type="button" onclick="reset_forms();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="リセット">
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
    <div id="calid"></div>

    <div class="new_document">
        <a href="{{ route('bill.create') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="新規">
        </a>
        <a href="{{ route('bill.export') }}">
            <img src="{{ asset('img/bt_excel.jpg') }}" alt="エクスポート">
        </a>
    </div>

    <h3>
        <div class="edit_02_bill">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box mb40">
        <div id='pagination'>
            {{ $bills->total() }}
        </div>

        <div id='pagination'>
            {{ $bills->links() }}
        </div>

        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">

        <div class="list_area">
            @if ($bills->isNotEmpty())
                <form method="post" action="{{ route('bill.action') }}">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50"><input type="checkbox" class="chk_all" onclick="selectAll();"></th>
                                <th class="w50">@sortablelink('MBL_ID', 'No.')</th>
                                <th class="w100">@sortablelink('Customer.NAME_KANA', '顧客名')</th>
                                <th class="w150">@sortablelink('SUBJECT', '件名')</th>
                                <th class="w70">@sortablelink('CAST_TOTAL', '合計金額')</th>
                                <th class="w100">@sortablelink('ISSUE_DATE', '発行日')</th>
                                @if($user->AUTHORITY != 1)
                                    <th class="w100">@sortablelink('USR_ID', '作成者') / @sortablelink('UPDATE_USR_ID', '更新者')</th>
                                @endif
                                <th class="w80">@sortablelink('STATUS', '発行ステータス')</th>
                                <th class="w100">メモ</th>
                                <th class="w100">領収書作成</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($bills as $bill)
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="{{ $bill->MBL_ID }}" class="chk"></td>
                                    <td>{{ $bill->MBL_ID }}</td>
                                    <td>{{ $bill->customer->NAME }}</td>
                                    <td><a href="{{ route('bill.check', $bill->MBL_ID) }}">{{ $bill->SUBJECT }}</a></td>
                                    <td>{{ $bill->TOTAL ?? '&nbsp;' }}円</td>
                                    <td>{{ $bill->ISSUE_DATE ?? '&nbsp;' }}</td>
                                    @if($user->AUTHORITY != 1)
                                        <td>{{ $bill->user->USR_NAME }} / {{ $bill->updater->USR_NAME }}</td>
                                    @endif
                                    <td>{{ $status[$bill->STATUS] ?? '' }}</td>
                                    <td>{{ $bill->MEMO }}</td>
                                    <td>
                                        @if($bill->STATUS == 1)
                                            <a href="{{ route('receipts.create', ['mbl_id' => $bill->MBL_ID]) }}">
                                                <img src="{{ asset('img/button/receipt.jpg') }}" class="imgover" alt="領収書作成">
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="bill_actions">
                        <button type="submit" name="delete" class="btn btn-danger" disabled>削除</button>
                        <button type="submit" name="reproduce" class="btn btn-primary" disabled>複製</button>
                    </div>
                </form>
            @else
                <p>対象データがありません。</p>
            @endif
        </div>
    </div>

    <div class="pagination">
        {{ $bills->links() }}
    </div>
</div>
@endsection
