@extends('layout.default')

@section('scripts')
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
            @if (isset($name) && isset($action))
                setBeforeSubmit('{{ $name . ucfirst($action) . 'Form' }}');
            @else
                console.error("Name or action is not set.");
            @endif
        });
    </script>
@endsection

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp

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
                            <td><input type="text" name="NO" class="w300" value="{{$searchData['NO']}}"></td>
                            <th>件名</th>
                            <td><input type="text" name="SUBJECT" class="w300" value="{{$searchData['SUBJECT']}}"></td>
                        </tr>
                        <tr>
                            <th>顧客名</th>
                            <td><input type="text" name="NAME" class="w300" value="{{$searchData['NAME']}}"></td>
                            <th>自社担当者</th>
                            <td colspan="3"><input type="text" name="CHR_USR_NAME" class="w300" value="{{$searchData['CHR_USR_NAME']}}"></td>
                        </tr>
                        <tr>
                            <th>作成者</th>
                            <td><input type="text" name="USR_NAME" class="w300" value="{{$searchData['USR_NAME']}}"></td>
                            <th>更新者</th>
                            <td><input type="text" name="UPD_USR_NAME" class="w300" value="{{$searchData['UPD_USR_NAME']}}"></td>
                        </tr>
                        <tr>
                            <th>発行ステータス</th>
                            <td colspan="3">

                                @foreach ($status as $key => $value)
                                <div class="checkbox">

                                    <input type="checkbox" name="STATUS[]" value="{{ $key }}" id="DeliverySTATUS{{ $key }}" {{ in_array($key, request('STATUS', $searchStatus) ?? []) ? 'checked' : '' }}>
                                    <label for="DeliverySTATUS{{ $key }}">{{ $value }}</label>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                    </table>

                    <div class="quote_extend">
                        <div class="quote_extend_btn" id="quote_open_btn">
                            <img src="{{ asset('img/button/d_down.png') }}" class="imgover" alt="off"
                                onclick="toggle_quote_extend_open();">
                            詳細検索を表示する
                        </div>
                        <div class="quote_extend_btn" id="quote_close_btn" style="display:none;">
                            <img src="{{ asset('img/button/d_up.png') }}" class="imgover" alt="off"
                                onclick="toggle_quote_extend_close();">
                            詳細検索を非表示にする
                        </div>
                        <div class="quote_extend_area">
                            <table width="940" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <th>商品名</th>
                                    <td><input type="text" name="ITEM_NAME" class="w300" value="{{$searchData['ITEM_NAME']}}"></td>
                                    <th>商品コード</th>
                                    <td><input type="text" name="ITEM_CODE" class="w300" value="{{$searchData['ITEM_CODE']}}"></td>
                                </tr>
                                <tr>
                                    <th>合計金額</th>
                                    <td><input type="text" name="TOTAL_FROM" class="w100" value="{{$searchData['TOTAL_FROM']}}"> 円 ～ <input type="text"
                                            name="TOTAL_TO" class="w100" value="{{$searchData['TOTAL_TO']}}"> 円</td>
                                </tr>
                                <tr>
                                    <th>発行日 開始日</th>
                                    <td width="320">
                                        <input type="text" name="ACTION_DATE_FROM" class="w100 p2 date cal" readonly>
                                        <img src="{{ asset('img/bt_now.jpg') }}" id="ACTION_DATE_FROM" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];">
                                        <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                            onclick="return cal1.write();">
                                        <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_FROM').value = '';">
                                    </td>
                                    <th>発行日 終了日</th>
                                    <td width="320">
                                        <input type="text" name="ACTION_DATE_TO" id="ACTION_DATE_TO" class="w100 p2 date cal" readonly>
                                        <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];">
                                        <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                            onclick="return cal2.write();">
                                        <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_TO').value = '';">
                                    </td>
                                </tr>
                                <tr>
                                    <th>備考</th>
                                    <td><input type="text" name="NOTE" class="w300" value="{{$searchData['NOTE']}}"></td>
                                    <th>メモ</th>
                                    <td><input type="text" name="MEMO" class="w300" value="{{$searchData['MEMO']}}"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="quote_search_btn">
                        <table style="margin:0 auto">
                            <tr>
                                <td style="border:none;">
                                    <button onclick="document.getElementById('searchForm').submit();" style="border:none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="" class="imgover">
                                    </button>
                                </td>
                                <td style="border:none;">
                                    <button onclick="reset_forms(); return false;" style="border:none;">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt=""
                                            class="imgover">
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

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
                    {{ $paginator->total() }} 件中 {{ ($paginator->count() * ($paginator-> currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator-> currentPage()) }} 件表示中
                </div>

                <div id='pagination'>
                    <!-- Previous Page Link -->
                    @if ($paginator->onFirstPage())
                        <span class="disabled">
                            << {{ __('前へ') }}</span> |
                            @else
                                <a href="{{ $paginator->previousPageUrl() }}" rel="prev">
                                    << {{ __('前へ') }}</a> |
                    @endif

                    <!-- Pagination Elements -->
                    @foreach ($paginator->links()->elements as $element)
                        <!-- "Three Dots" Separator -->
                        @if (is_string($element))
                            <span class="disabled">{{ $element }}</span> |
                        @endif

                        <!-- Array Of Links -->
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="active">{{ $page }}</span> |
                                @else
                                    <a href="{{ $url }}">{{ $page }}</a> |
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
                    @if (is_array($list))
                        <form action="{{ route('delivery.index') }}" method="POST">
                            <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                                <thead>
                                    <tr>
                                        <th class="w50"><input type="checkbox" name="action.select_all"
                                                class="chk_all" onclick="select_all();"></th>
                                        <th class="w50">
                                            <a href="{{ route('delivery.index', ['sort' => 'MDV_ID']) }}">No.</a>
                                        </th>
                                        <th class="w100">
                                            <a href="{{ route('delivery.index', ['sort' => 'NAME_KANA']) }}">顧客名</a>
                                        </th>
                                        <th class="w150">
                                            <a href="{{ route('delivery.index', ['sort' => 'SUBJECT']) }}">件名</a>
                                        </th>
                                        <th class="w100">
                                            <a href="{{ route('delivery.index', ['sort' => 'CAST_TOTAL']) }}">合計金額</a>
                                        </th>
                                        <th class="w100">
                                            <a href="{{ route('delivery.index', ['sort' => 'ISSUE_DATE']) }}">発行日</a>
                                        </th>
                                        @if ($user['AUTHORITY'] != 1)
                                            <th class="w150">
                                                <a href="{{ route('delivery.index', ['sort' => 'USR_ID']) }}">作成者</a>/
                                                <a
                                                    href="{{ route('delivery.index', ['sort' => 'UPDATE_USR_ID']) }}">更新者</a>

                                            </th>
                                        @endif
                                        <th class="w100">
                                            <a href="{{ route('delivery.index', ['sort' => 'STATUS']) }}">発行ステータス</a>
                                        </th>
                                        <th class="w100">メモ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $key => $val)
                                        <tr>
                                            <td><input type="checkbox" name="{{ $val['MDV_ID'] }}"
                                                    class="chk"></td>

                                            @if (isset($authcheck[$key]))
                                                <div class="auth{{ $val['Delivery']['MDV_ID'] }}" style="display:none;">
                                                    {{ $authcheck[$key] }}
                                                </div>
                                            @endif
                                            <td>{{ nl2br($val['MDV_ID']) }}
                                            </td>
                                            <td>{{ nl2br($val['Customer']['NAME'] ?? "") }}</td>
                                            <td>
                                                <a href="{{ route('delivery.check', $val['MDV_ID']) }}">
                                                    {{ $val['SUBJECT'] }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ isset($val['TOTAL']) ? nl2br($val['TOTAL']) . '円' : '&nbsp;' }}
                                            </td>
                                            <td>{{ $val['ISSUE_DATE'] ? $val['ISSUE_DATE'] : '&nbsp;' }}
                                            </td>
                                            @if ($user['AUTHORITY'] != 1)

                                                <td>
                                                    {{ nl2br($val['USER']['NAME']) }} /
                                                    {{ $val['UPDATEUSER']['NAME'] ? $val['UPDATEUSER']['NAME']:'' }}
                                                </td>
                                            @endif
                                            <td>{{ $status[$val['STATUS']] }}</td>
                                            <td>{{ $val['MEMO'] ? nl2br($val['MEMO']) : '&nbsp;' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="list_btn">
                                <input type="image" src="{{ asset('img/document/bt_delete2.jpg') }}" name="delete"
                                    alt="削除" onclick="return del();" class="mr5" disabled="">
                                <input type="image" src="{{ asset('img/bt_01.jpg') }}" name="reproduce_quote"
                                    alt="複製" class="mr5">
                                <input type="image" src="{{ asset('img/bt_02.jpg') }}" name="reproduce_bill"
                                    alt="複製" class="mr5">
                                <input type="image" src="{{ asset('img/bt_03.jpg') }}" name="reproduce_delivery"
                                    alt="複製" class="mr5">

                                {{-- Include status_change element --}}
                                @include('elements.status_change')


                                {{-- Hidden fields --}}
                                @if (isset($customer_id))
                                    <input type="hidden" name="Customer.id" value="{{ $customer_id }}">
                                @endif

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="image" src="{{ asset('img/document/bt_delete2.jpg') }}" name="delete"
                                    alt="削除" onclick="return del();" class="mr5">

                            </div>
                        </form>
                    @endif
                </div>
                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
            </div>
        </div>
    </form>
@endsection
