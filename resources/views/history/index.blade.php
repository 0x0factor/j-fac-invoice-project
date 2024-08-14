@extends('layout.default')

@section('content')
    @php
        $formType = $formType ?? 'History';
        $controller = strtolower($formType);
        $action = request()->route()->getActionMethod();
    @endphp

<!-- Flash Message -->
@if(session()->has('flash_notification.message'))
    <div class="alert alert-{{ session('flash_notification.level') }}">
        {{ session('flash_notification.message') }}
    </div>
@endif

    <div id="contents">
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>

        <h3><div class="search"><span class="edit_txt">&nbsp;</span></div></h3>
        <div class="search_box">
            <div class="search_area">
                <form method="GET" action="{{ route('history.index') }}">
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>日付 FROM</th>
                            <td width="320">
                                <input type="text" name="data[{{$formType}}][ACTION_DATE_FROM]" id="ACTION_DATE_FROM" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal1.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_FROM').value = '';">
                            </td>
                        </tr>
                        <tr>
                            <th>日付 TO</th>
                            <td width="320">
                                <input type="text" name="data[{{$formType}}][ACTION_DATE_TO]" id="ACTION_DATE_TO" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal2.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_TO').value = '';">
                            </td>
                        </tr>
                        <tr>
                            <th>ユーザ名</th>
                            <td><input type="text" name="NAME" class="w350" value="{{$searchData['NAME']}}"></td>
                        </tr>
                        <tr>
                            <th>動作種別</th>
                            <td>
                            @foreach($actions as $key => $value)
                            <div class="checkbox">

                                <input type="checkbox" name="ACTION[]" value="{{ $key }}" id="HistoryACTION{{ $key }}"
                                {{ in_array($key, request()->input('ACTION', [])) ? 'checked' : '' }}>
                                <label>
                                    {{ $value }}
                                </label>
                            </div>
                            @endforeach
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <button type="submit" style="border:none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="検索">
                                    </button>
                                </td>
                                <td style="border:none;">
                                    <button type="button" onclick="resetForms()" style="border:none;">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="リセット">
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
        </div>

        <h3><div class="edit_02_history"><span class="edit_txt">&nbsp;</span></div></h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $histories->total() }} 件中 {{ ($histories->count() * ($histories-> currentPage() - 1) + 1) }} - {{ ($histories->count() * $histories-> currentPage()) }} 件表示中
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

            <img src="{{ asset('img/bg_contents_top.jpg') }}">
            <div class="list_area">
                <form method="POST" action="{{ route('history.index') }}">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w200">
                                    <a href="{{ route('history.index', ['sort' => 'ACTION_DATE']) }}">日付</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('history.index', ['sort' => 'NAME']) }}">ユーザ名</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('history.index', ['sort' => 'ACTION']) }}">動作</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($histories as $history)
                                <tr>
                                    <td>{{ $history->ACTION_DATE }}</td>
                                    <td>{{ $history->user->NAME }}</td>
                                    <td>
                                        @if($history->ACTION == '0')
                                            ログインしました
                                        @elseif($history->ACTION == '1')
                                            ログアウトしました
                                        @elseif($history->ACTION == '2')

                                            見積書のID(<a href="{{ route('quote.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '3')
                                            見積書のID(<a href="{{ route('quote.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '4')
                                            見積書のID({{ implode(',', $ids[$key]) }})を削除しました
                                        @elseif($history->ACTION == '5')
                                            請求書のID(<a href="{{ route('bill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '6')
                                            請求書のID(<a href="{{ route('bill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '7')
                                            請求書のID({{ implode(',', $ids[$key]) }})を削除しました
                                        @elseif($history->ACTION == '8')
                                            納品書のID(<a href="{{ route('delivery.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '9')
                                            納品書のID(<a href="{{ route('delivery.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '10')
                                            納品書のID({{ implode(',', $ids[$key]) }})を削除しました
                                        @elseif($history->ACTION == '11')
                                            合計請求書のID(<a href="{{ route('totalbill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '12')
                                            合計請求書のID(<a href="{{ route('totalbill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '13')
                                            合計請求書のID({{ implode(',', $ids[$key]) }})を削除しました
                                        @elseif($history->ACTION == '14')
                                            定期請求書雛形のID(<a href="{{ route('regularbill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '15')
                                            定期請求書雛形のID(<a href="{{ route('regularbill.check', ['id' => $history['RPT_ID']]) }}">{{ $history['RPT_ID'] }}</a>)を作成しました
                                        @elseif($history->ACTION == '16')
                                            定期請求書雛形のID({{ implode(',', $ids[$key]) }})を削除しました
                                        @elseif($history->ACTION == '17')
                                            定期請求書雛形から請求書のID(
                                            @foreach($ids[$key] as $id)
                                                {{ $loop->first ? '' : ', ' }}<a href="{{ route('bill.check', ['id' => $id]) }}">{{ $id }}</a>
                                            @endforeach
                                            )を作成しました
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="list_btn">
                    </div>
                </form>
            </div>

            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            try {
                window.addEventListener("load", initTableRollovers('index_table'), false);
            } catch(e) {
                window.attachEvent("onload", initTableRollovers('index_table'));
            }

            $(function() {
                setBeforeSubmit('{{ class_basename(request()->route()->getController()) . ucfirst(request()->route()->getActionMethod()) }}Form');
            });
        });

    </script>
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
