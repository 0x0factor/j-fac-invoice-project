@extends('layout.default')

@section('content')
<script>
    window.addEventListener("load", function() {
        initTableRollovers('index_table');
    });
</script>

<script>
    function select_all() {
        $(".chk").prop("checked", $(".chk_all").prop("checked"));
        $('input[name="delete"]').prop('disabled', false);
        $('input[name="reproduce"]').prop('disabled', false);
    }
</script>

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>
    <h3><div class="quote_search"><span class="edit_txt">&nbsp;</span></div></h3>
    <div class="quote_search_box">
        <div class="quote_search_area">
            <form method="GET" action="{{ route('quote.index') }}">
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
                            @if(isset($status))
                            @foreach($status as $key => $value)
                                <label><input type="checkbox" name="STATUS[]" value="{{ $key }}"> {{ $value }}</label>
                            @endforeach
                            @endif
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
                                <a href="#" onclick="document.forms[0].submit();"><img src="{{ asset('img/bt_search.jpg') }}" alt="検索する"></a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();"><img src="{{ asset('img/bt_search_reset.jpg') }}" alt="リセット"></a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <div id="calid"></div>

        <div class="new_document">
            <a href="{{ route('quote.create') }}"><img src="{{ asset('img/bt_new.jpg') }}" alt="新規作成"></a>
            <a href="{{ route('quote.export') }}"><img src="{{ asset('img/bt_excel.jpg') }}" alt="エクスポート"></a>
        </div>

        <h3><div class="edit_02_quote"><span class="edit_txt">&nbsp;</span></div></h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $quotes->count() }}
            </div>
            <div id='pagination'>
                {{ $quotes->links() }}
            </div>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="list_area">
                @if($quotes->isNotEmpty())
                <form method="POST" action="{{ route('quote.action') }}">
                    @csrf
                    @method('DELETE')
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                        <tr>
                            <th class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                            <th class="w50">@sortablelink('MQT_ID', 'No.')</th>
                            <th class="w100">@sortablelink('customer.NAME_KANA', '顧客名')</th>
                            <th class="w150">@sortablelink('SUBJECT', '件名')</th>
                            <th class="w100">@sortablelink('TOTAL', '合計金額')</th>
                            <th class="w100">@sortablelink('ISSUE_DATE', '発行日')</th>
                            @if(auth()->user()->AUTHORITY != 1)
                                <th class="w150">@sortablelink('USR_ID', '作成者') / @sortablelink('UPDATE_USR_ID', '更新者')</th>
                            @endif
                            <th class="w100">@sortablelink('STATUS', '発行ステータス')</th>
                            <th class="w100">メモ</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($quotes as $quote)
                        <tr>
                            <td class="v50"><input type="checkbox" name="selected_quotes[]" value="{{ $quote->MQT_ID }}" class="chk"></td>
                            <td class="v50">{{ $quote->MQT_ID }}</td>
                            <td class="v100">{{ $quote->customer->NAME }}</td>
                            <td class="v100"><a href="{{ route('quote.edit', $quote->MQT_ID) }}">{{ $quote->SUBJECT }}</a></td>
                            <td class="v150">{{ $quote->TOTAL }}円</td>
                            <td class="v150">{{ $quote->ISSUE_DATE }}</td>
                            @if(auth()->user()->AUTHORITY != 1)
                                <td class="v50">{{ $quote->user->NAME }} / {{ $quote->updateUser->NAME ?? '&nbsp;' }}</td>
                            @endif
                            <td class="v50">{{ $status[$quote->STATUS] }}</td>
                            <td class="v100">{{ $quote->MEMO ?? '&nbsp;' }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div class="list_btn">
                        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to delete these quotes?');"><img src="{{ asset('img/document/bt_delete2.jpg') }}" alt="削除"></button>
                        <button type="submit" name="reproduce_quote"><img src="{{ asset('img/bt_01.jpg') }}" alt="複製"></button>
                        <button type="submit" name="reproduce_bill"><img src="{{ asset('img/bt_02.jpg') }}" alt="複製"></button>
                        <button type="submit" name="reproduce_delivery"><img src="{{ asset('img/bt_03.jpg') }}" alt="複製"></button>
                    </div>
                </form>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
        </div>
    </div>
</div>
@endsection
