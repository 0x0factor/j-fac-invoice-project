@extends('layout.default')

@section('link')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .disabled {
            color: grey;
            pointer-events: none;
            cursor: default;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize the datepickers
            $("#ACTION_DATE_FROM").datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function(dateText, inst) {
                    $(this).val(dateText);
                }
            });

            $("#ACTION_DATE_TO").datepicker({
                dateFormat: "yy-mm-dd",
                onSelect: function(dateText, inst) {
                    $(this).val(dateText);
                }
            });

            function initTableRollovers(id) {
                // Implement your table rollover logic here
            }

            try {
                window.addEventListener("load", function() {
                    initTableRollovers('index_table');
                }, false);
            } catch (e) {
                window.attachEvent("onload", function() {
                    initTableRollovers('index_table');
                });
            }

        });

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
    <!-- Display session flash messages -->
    @if (Session::has('message'))
        <div class="alert alert-info">{{ Session::get('message') }}</div>
    @endif

    <!-- Search Form -->
    <form action="{{ route('history.index') }}" method="GET">
        <div id="contents">
            <div class="arrow_under">
                <img src="{{ asset('img/i_arrow_under.jpg') }}" alt>
            </div>
            <h3>
                <div class="search">
                    <div class="edit_txt">
                        &nbsp;
                    </div>
                </div>
            </h3>
            <div class="search_box">
                <div class="search_area">
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>日付 FROM</th>
                            <td width="320">
                                <script language="JavaScript">
                                    var cal1 = new JKL.Calendar("calid", "HistoryIndexForm", "ACTION_DATE_FROM");
                                </script>
                                <input type="text" name="ACTION_DATE_FROM" id="ACTION_DATE_FROM" value="{{ request('ACTION_DATE_FROM') }}"
                                    readonly class="w100 p2 date cal" onchange="cal1.getFormValue(); cal1.hide();"
                                    id="HistoryACTIONDATEFROM">
                                <a href="#"><img src="{{ asset('img/bt_now.jpg') }}" alt="現在"
                                        class="nowtime" onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];"></a>
                                <a href="#"><img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー"
                                        class="pl5" onclick="return cal1.write();"></a>
                                <a href="#"><img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在"
                                        class="cleartime"></a>
                            </td>
                        </tr>
                        <tr>
                            <th>日付 To</th>
                            <td>
                                <input type="text" name="ACTION_DATE_TO" id="ACTION_DATE_TO" value="{{ request('ACTION_DATE_TO') }}" readonly
                                    class="w100 p2 date" onchange="cal2.getFormValue(); cal2.hide();"
                                    id="HistoryACTIONDATETO">
                                <a href="#"><img src="{{ asset('img/bt_now.jpg') }}" alt="現在"
                                        class="nowtime" onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];"></a>
                                <a href="#"><img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー"
                                        class="pl5" onclick="return cal2.write();"></a>
                                <a href="#"><img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在"
                                        class="cleartime"></a>
                            </td>
                        </tr>
                        <tr>
                            <th>ユーザ名</th>
                            <td><input type="text" name="NAME" value="{{ request('NAME') }}" class="w350"
                                    id="HistoryNAME"></td>
                        </tr>
                        <tr>
                            <th>動作種別</th>
                            <td>
                                @foreach ($action ?? [] as $key => $value)
                                    <div class="checkbox">
                                        <input type="checkbox" name="ACTION[]" value="{{ $key }}"
                                            @if (in_array($key, (array) request('ACTION', []))) checked @endif> {{ $value }}
                                    </div>
                                    <!-- <div>
                                        @switch($history->ACTION)
                                        @case('ログイン')
                                            ログインしました
                                        @break

                                        @case('ログアウト')
                                            ログアウトしました
                                        @break

                                        @case('見積書作成')
                                            見積書のID({{ $history->RPT_ID }})を作成しました
                                        @break

                                        @case('見積書更新')
                                            見積書のID({{ $history->RPT_ID }})を更新しました
                                        @break

                                        @case('見積書削除')
                                            見積書のID({{ $history->RPT_ID }})を削除しました
                                        @break

                                        @case('請求書作成')
                                            請求書のID({{ $history->RPT_ID }})を作成しました
                                        @break

                                        @case('請求書更新')
                                            請求書のID({{ $history->RPT_ID }})を更新しました
                                        @break

                                        @case('請求書削除')
                                            請求書のID({{ $history->RPT_ID }})を削除しました
                                        @break

                                        @case('納品書作成')
                                            納品書のID({{ $history->RPT_ID }})を作成しました
                                        @break

                                        @case('納品書更新')
                                            納品書のID({{ $history->RPT_ID }})を更新しました
                                        @break

                                        @case('納品書削除')
                                            納品書のID({{ $history->RPT_ID }})を削除しました
                                        @break

                                        @case('合計請求書作成')
                                            合計請求書のID({{ $history->RPT_ID }})を作成しました
                                        @break

                                        @case('合計請求書更新')
                                            合計請求書のID({{ $history->RPT_ID }})を更新しました
                                        @break

                                        @case('合計請求書削除')
                                            合計請求書のID({{ $history->RPT_ID }})を削除しました
                                        @break

                                        @case('定期請求書雛形作成')
                                            定期請求書雛形のID({{ $history->RPT_ID }})を作成しました
                                        @break

                                        @case('定期請求書雛形更新')
                                            定期請求書雛形のID({{ $history->RPT_ID }})を更新しました
                                        @break

                                        @case('定期請求書雛形削除')
                                            定期請求書雛形のID({{ $history->RPT_ID }})を削除しました
                                        @break

                                        @case('定期請求書作成')
                                            定期請求書雛形から請求書のID(
                                                                                        @foreach ($ids[$history->id] as $ikey => $ival)
                                            @if ($ikey > 0)
                                            ,
                                            @endif
                                                                                            <a href="{{ route('bills.check', $ival) }}">{{ $ival }}</a>
                                            @endforeach
                                                                                        )を作成しました
                                        @break
                                    @endswitch
                                    </div> -->
                                @endforeach
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <a href="#" onclick="$('#HistoryIndexForm').submit();">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="Search">
                                    </a>
                                </td>
                                <td style="border:none;">
                                    <a href="#" onclick="reset_forms();">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="Reset">
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
            </div>
            <h3>
                <div class="edit_02_history">
                    <span class="edit_txt">
                        &nbsp;
                    </span>
                </div>
            </h3>
            <div class="contents_box mb40">
                <div id='pagination'>
                    {{ $histories->total() }} 件中 41 - 60 件を表示
                </div>

                <div id='pagination'>
                    @if ($histories->onFirstPage())
                        <span class="disabled">
                            << {{ __('前へ') }}</span>
                            @else
                                <a href="{{ $histories->previousPageUrl() }}" rel="prev">
                                    << {{ __('前へ') }}</a>
                    @endif

                    {{ $histories->links() }}


                    @if ($histories->hasMorePages())
                        <a href="{{ $histories->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
                    @else
                        <span class="disabled">{{ __('次へ') }} >></span>
                    @endif
                </div>
                <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block">

                <div class="list_area">
                    <div style="display:none;">
                        <input type="hidden" name="_method" value="POST">

                    </div>
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w200">
                                    <a
                                        href="{{ route('history.index', array_merge(request()->all(), ['sort' => 'ACTION_DATE'])) }}">日付</a>
                                </th>
                                <th class="w100">
                                    <a
                                        href="{{ route('history.index', array_merge(request()->all(), ['sort' => 'NAME'])) }}">ユーザ名</a>
                                </th>
                                <th class="w200">
                                    <a
                                        href="{{ route('history.index', array_merge(request()->all(), ['sort' => 'ACTION'])) }}">動作</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $history)
                                <tr>
                                    <td>{{ $history->ACTION_DATE }}</td>
                                    <td>{{ $history->user->NAME }}</td>
                                    <td>
                                        @switch($history->ACTION)
                                            @case('0')
                                                ログインしました
                                            @break

                                            @case('1')
                                                ログアウトしました
                                            @break
                                        @endswitch
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="list_btn">
                    </div>
                </div>

                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt>
            </div>
        </div>
    </form>
    </div>
@endsection
