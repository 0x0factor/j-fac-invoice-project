@extends('layout.default')

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
    <script>
        try {
            window.addEventListener("load", initTableRollovers('index_table'), false);
        } catch (e) {
            window.attachEvent("onload", initTableRollovers('index_table'));
        }
    </script>

    <script>
        function select_all() {
            $(".chk").attr("checked", $(".chk_all").attr("checked"));
            $('input[name="delete"]').attr('disabled', '');
            $('input[name="reproduce"]').attr('disabled', '');
        }
    </script>

    <script>
        $(function() {
            @if (isset($name) && isset($action))
                setBeforeSubmit('{{ $name . ucfirst($action) . 'Form' }}');
            @else
                console.error("Name or action is not set.");
            @endif
        });
    </script>
    {{-- Display Flash Messages --}}
    @if (session('flash_message'))
        <div class="flash-message">
            {{ session('flash_message') }}
        </div>
    @endif

    {{-- Form Start --}}
    <form method="GET" action="{{ route('totalbill.index') }}">
        <div id="contents">
            <div class="arrow_under">
                <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
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
                            <th>顧客名</th>
                            <td><input type="text" name="NAME" class="w350" value="{{ request('NAME') }}"></td>
                        </tr>
                        <tr>
                            <th>件名</th>
                            <td><input type="text" name="SUBJECT" class="w350" value="{{ request('SUBJECT') }}"></td>
                        </tr>
                        <tr>
                            <th>発行日 開始日</th>
                            <td width="320">
                                <input type="text" id="ACTION_DATE_FROM" name="ACTION_DATE_FROM" class="w100 p2 date cal"
                                    readonly value="{{ request('ACTION_DATE_FROM') }}">
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime"
                                    onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                    onclick="showCalendar('ACTION_DATE_FROM');">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime"
                                    onclick="document.getElementById('ACTION_DATE_FROM').value = '';">
                            </td>
                        </tr>
                        <tr>
                            <th>発行日 終了日</th>
                            <td width="320">
                                <input type="text" id="ACTION_DATE_TO" name="ACTION_DATE_TO" class="w100 p2 date cal"
                                    readonly value="{{ request('ACTION_DATE_TO') }}">
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime"
                                    onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                    onclick="showCalendar('ACTION_DATE_TO');">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime"
                                    onclick="document.getElementById('ACTION_DATE_TO').value = '';">
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <button onclick="$('#TotalbillIndexForm').submit();" style="border: none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="Search">
                                    </button>
                                    <!-- <button type="submit">
                                            <img src="{{ asset('img/bt_search.jpg') }}" alt="Search">
                                    </button> -->
                                </td>
                                <td style="border:none;">
                                    <button href="#" onclick="reset_forms();" style="border: none;">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="Reset">
                                    </button>

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <img src="{{ asset('/img/document/bg_search_bottom.jpg') }}" class="block" alt="Search Bottom">
            </div>

            <div id="calid"></div>

            {{-- Form End --}}
    </form>

    <div class="new_document">
        <a href="{{ route('totalbill.add') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="New">
        </a>
    </div>

    <h3>
        <div class="edit_02_totalbill">
            <span class="edit_txt">&nbsp;</span>
        </div>
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

        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="list_area">
            @if (is_array($list))
                <form method="POST" action="{{ route('totalbill.add') }}">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <input type="checkbox" class="chk_all" onclick="selectAll();">
                                </th>
                                <th class="w50">
                                    <a href="{{ route('totalbill.index', ['sort' => 'TBL_ID']) }}">No.</a>
                                </th>
                                <th class="w150">
                                    <a href="{{ route('totalbill.index', ['sort' => 'NAME']) }}">顧客名</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('totalbill.index', ['sort' => 'SUBJECT']) }}">件名</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('totalbill.index', ['sort' => 'CAST_THISM_BILL']) }}">合計金額</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('totalbill.index', ['sort' => 'USR_ID']) }}">発行日</a>
                                </th>
                                @if ($user['AUTHORITY'] != 1)
                                    <th class="w150">
                                        <a href="{{ route('totalbill.index', ['sort' => 'UPDATE_USR_ID']) }}">作成者/更新者</a>
                                    </th>
                                @endif
                                <th class="w100">
                                    <a href="{{ route('totalbill.index', ['sort' => 'EDIT_STAT']) }}">発行ステータス</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $val)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $val['Totalbill']['TBL_ID'] }}"
                                            class="chk"></td>
                                    <td>{{ $val['Totalbill']['TBL_ID'] }}</td>
                                    <td>{{ $val['Customer']['NAME'] }}</td>
                                    <td><a
                                            href="{{ route('totalbill.show', ['id' => $val['Totalbill']['TBL_ID']]) }}">{{ $val['Totalbill']['SUBJECT'] }}</a>
                                    </td>
                                    <td>{{ isset($val['Totalbill']['THISM_BILL']) ? $val['Totalbill']['THISM_BILL'] . '円' : '&nbsp;' }}
                                    </td>
                                    <td>{{ $val['Totalbill']['ISSUE_DATE'] ?: '&nbsp;' }}</td>
                                    @if ($user['AUTHORITY'] != 1)
                                        <td>{{ $val['User']['NAME'] }} / {{ $val['UpdateUser']['NAME'] ?? '&nbsp;' }}</td>
                                    @endif
                                    <td>{{ $edit_stat[$val['Totalbill']['EDIT_STAT']] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="list_btn">
                        <input type="hidden" name="data[Security][token]" value="hiddenToken()" id="SecurityToken">
                        <input type="image" src="{{ asset('img/document/bt_delete2.jpg') }}" name="delete"
                            alt="削除" onclick="return del();" class="mr5" disabled="">
                    </div>
                </form>
            @endif
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
    </div>
    </div>

    {{-- JavaScript --}}
    <script>
        function showCalendar(inputId) {
            // Implement your calendar logic here
        }

        function resetForms() {
            document.querySelectorAll('form').forEach(form => form.reset());
        }

        function selectAll() {
            document.querySelectorAll('.chk').forEach(checkbox => checkbox.checked = true);
        }
    </script>
@endsection
