@extends('layout.default')

@section('scripts')
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

@endsection
@section('content')

    @php
        $formType = $formType ?? 'Quote';
        $controller = strtolower($formType);
        $action = request()->route()->getActionMethod();
    @endphp

    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp

    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>
        <h3>
            <div class="quote_search"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="quote_search_box">
            <div class="quote_search_area">
                <form method="GET" action="{{ route('quote.index') }}">
                    <table width="940" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>管理番号</th>
                            <td><input type="text" name="NO" class="w300" value="{{$searchData->NO}}" id="QuoteNO"></td>
                            <th>件名</th>
                            <td><input type="text" name="SUBJECT" class="w300" value="{{$searchData->SUBJECT}}" id="QuoteSUBJECT"></td>
                        </tr>
                        <tr>
                            <th>顧客名</th>
                            <td><input type="text" name="NAME" class="w300" value="{{$searchData->NAME}}" id="QuoteNAME"></td>
                            <th>自社担当者</th>
                            <td colspan="3"><input type="text" name="CHR_USR_NAME" class="w300" value="{{$searchData->CHR_USR_NAME}}" id="QuoteCHRUSRNAME"></td>
                        </tr>
                        <tr>
                            <th>作成者</th>
                            <td><input type="text" name="USR_NAME" class="w300" value="{{$searchData->USR_NAME}}" id="QuoteUSRNAME"></td>
                            <th>更新者</th>
                            <td><input type="text" name="UPD_USR_NAME" class="w300" value="{{$searchData->UPD_USR_NAME}}" id="QuoteUPDUSRNAME"></td>
                        </tr>
                        <tr>
                            <th>発行ステータス</th>
                            <td colspan="3">
                                @if (isset($status))
                                    @foreach ($status as $key => $value)
                                        <div class="checkbox">
                                            <input type="checkbox" name="STATUS[]" value="{{ $key }}" id="QuoteSTATUS{{ $key }}" {{ in_array($key, request('STATUS', $searchStatus) ?? []) ? 'checked' : '' }}>
                                            <label for="QuoteSTATUS{{ $key }}"> {{ $value }} </label>
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div class="quote_extend">
                        <div class="quote_extend_btn" id="quote_open_btn">
                            <img src="{{ asset('img/button/d_down.png') }}" class="imgover" alt="off"
                                onclick="toggle_quote_extend_open();">　詳細検索を表示する
                        </div>
                        <div class="quote_extend_btn" id="quote_close_btn" style="display:none;">
                            <img src="{{ asset('img/button/d_up.png') }}" class="imgover" alt="off"
                                onclick="toggle_quote_extend_close();">　詳細検索を非表示にする
                        </div>
                        <div class="quote_extend_area">
                            <table width="940" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <th>商品名</th>
                                    <td><input type="text" name="ITEM_NAME" class="w300" value="{{$searchData->ITEM_NAME}}" id="QuoteITEMNAME"></td>
                                    <th>商品コード</th>
                                    <td><input type="text" name="ITEM_CODE" class="w300" value="{{$searchData->ITEM_CODE}}" id="QuoteITEMCODE"></td>
                                </tr>
                                <tr>
                                    <th>合計金額</th>
                                    <td><input type="text" name="TOTAL_FROM" class="w100" value="{{$searchData->TOTAL_FROM}}" id="QuoteTOTALFROM"> 円 ～ <input type="text"
                                            name="TOTAL_TO" class="w100" value="{{$searchData->TOTAL_TO}}" id="QuoteTOTALTO"> 円</td>
                                </tr>
                                <tr>
                                    <th>発行日 開始日</th>
                                    <td width="320">
                                        <input type="text" name="data[{{$formType}}][ACTION_DATE_FROM]" value="{{$searchData->ACTION_DATE_FROM}}" id="ACTIONDATEFROM" class="w100 p2 date cal" value="{{ request('ACTION_DATE_FROM') }}" readonly>
                                        <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];">
                                        <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                            onclick="return cal1.write();">
                                        <img src="{{ asset('img/bt_s_reset.jpg') }}"     alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_FROM').value = '';">
                                    </td>
                                    <th>発行日 終了日</th>
                                    <td width="320">
                                        <input type="text" name="data[{{$formType}}][ACTION_DATE_TO]" value="{{$searchData->ACTION_DATE_TO}}" id="ACTIONDATETO" class="w100 p2 date cal" readonly>
                                        <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];">
                                        <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                            onclick="return cal2.write();">
                                        <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在"
                                            class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_TO').value = '';">
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
                                    <button onclick="$('#QuoteIndexForm').submit();" style="border:none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                                    </button>
                                </td>
                                <td style="border:none;">
                                    <button onclick="reset_forms();" style="border:none;">
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
            <a href="{{ route('quote.add') }}"><img src="{{ asset('img/bt_new.jpg') }}" alt="新規作成"></a>
            <a href="{{ route('quote.export') }}"><img src="{{ asset('img/bt_excel.jpg') }}" alt="エクスポート"></a>
        </div>

        <h3>
            <div class="edit_02_quote"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $paginator->total() }} 件中 {{ ($paginator->count() * ($paginator-> currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator-> currentPage()) }} 件表示中
            </div>
            <div id='pagination'>
                @if ($paginator->onFirstPage())
                    <span class="disabled">
                        << {{ __('前へ') }}</span> |
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}&sort={{ $sortField }}&direction={{ $sortDirection }}" rel="prev">
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
                                <a href="{{ $url }}&sort={{ $sortField }}&direction={{ $sortDirection }}">{{ $page }}</a> |
                            @endif
                        @endforeach
                    @endif
                @endforeach


                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}&sort={{ $sortField }}&direction={{ $sortDirection }}" rel="next">{{ __('次へ') }} >></a>
                @else
                    <span class="disabled">{{ __('次へ') }} >></span>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="list_area">

                @if (is_array($quotes))
                    <form method="POST" action="{{ route('quote.action') }} " id="QuoteActionForm"
                        accept-charset="utf-8">
                        @csrf
                        @method('DELETE')


                        <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                            <thead>
                                <tr>
                                    <th class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                                    @php
                                        $columns = [
                                            'MQT_ID' => 'No.',
                                            'CST_ID' => '顧客名',
                                            'SUBJECT' => '件名',
                                            'TOTAL' => '合計金額',
                                            'ISSUE_DATE' => '発行日',
                                            'USR_ID' => '作成者',
                                            'UPDATE_USR_ID' => '更新者',
                                            'STATUS' => '発行ステータス'
                                        ];
                                    @endphp

                                    @foreach($columns as $field => $label)
                                        @if($field !== 'USR_ID' && $field !== 'UPDATE_USR_ID' || $user->AUTHORITY != 1)
                                            <th class="{{ in_array($field, ['MQT_ID', 'STATUS']) ? 'w50' : (in_array($field, ['CST_ID', 'TOTAL', 'ISSUE_DATE']) ? 'w100' : 'w150') }}">
                                                <a href="{{ route('quote.index', ['sort' => $field, 'direction' => $sortField === $field && $sortDirection === 'asc' ? 'desc' : 'asc']) }}">
                                                    {{ $label }}
                                                    @if ($sortField === $field)
                                                        {{ $sortDirection === 'asc' ? '↓' : '↑' }}
                                                    @endif
                                                </a>
                                                @if($field === 'USR_ID')
                                                    /
                                                @endif
                                            </th>
                                        @endif
                                    @endforeach

                                    <th class="w100">メモ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($quotes as $key => $quote)
                                    <tr>
                                        <div class="auth{{ $quote->MQT_ID }}" style="display:none;">
                                            {{ $key}}
                                        </div>
                                        <td class="v50"><input type="checkbox" name="selected_quotes[]"
                                                value="{{ $quote->MQT_ID }}" class="chk"></td>
                                        <td class="v50">{{ $quote->MQT_ID }}</td>
                                        <td class="v100">{{ $quote->customer->NAME ?? 'N/A' }}</td>
                                        <td class="v100">
                                            <a href="{{ route('quote.check', $quote->MQT_ID) }}">{{ $quote->SUBJECT }}</a>
                                        </td>
                                        <td class="v150">{{ $quote->TOTAL }}円</td>
                                        <td class="v150">{{ $quote->ISSUE_DATE }}</td>
                                        @if (auth()->user()->AUTHORITY != 1)
                                            <td class="v50">{{ $quote->user->NAME }} /
                                                {{ $quote->updateUser->NAME ?? $quote['UpdateUser']['NAME'] }}</td>
                                        @endif

                                        @if($quote->STATUS == 1)
                                            <td class="v50">
                                                作成済み
                                            </td>
                                        @else
                                            <td class="v50">
                                                下書き
                                            </td>
                                        @endif
                                        <td class="v100">{{ $quote->MEMO ?? $quote['MEMO'] }}</td>
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

                                @include('elements.status_change')

                                @if (isset($customer_id))
                                    <input type="hidden" name="Customer.id" value="{{ $customer_id }}">
                                @endif
                                <input type="hidden" name="data[Security][token]" value="{{ csrf_token() }}"
                                id="SecurityToken">
                        </div>
                    </form>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
        </div>
    </div>

@endsection
@section('script')
    <script language="JavaScript">
        var lastDate = '';
        var cal1 = new JKL.Calendar("calid", "{{$formType.$action}}Form", "data[{{$formType}}][ACTION_DATE_FROM]");
        var cal2 = new JKL.Calendar("calid", "{{$formType.$action}}Form", "data[{{$formType}}][ACTION_DATE_TO]");

        setInterval(function(){
            var date = $('input.cal.date').val();
            if(lastDate != date){
                lastDate = date;
                var calcDate = new Date(date);
                if(calcDate.getFullYear() >= 2024 || (calcDate.getFullYear() >= 2023 && calcDate.getMonth() >= 9)){
                    $('#TAXFRACTIONTIMING1').attr('disabled', true);
                    $('#TAXFRACTIONTIMING0').click();
                } else {
                    $('#TAXFRACTIONTIMING1').removeAttr('disabled', true);
                }
            }
        },1000);
    </script>
@endsection
