@extends('layout.default')

@section('scripts')
    <script>
        try {
            window.addEventListener("load", initTableRollovers('index_table'), false);
        } catch (e) {
            window.attachEvent("onload", initTableRollovers('index_table'));
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
@endsection
@section('content')

    @if (session()->has('message'))
        <div class="alert alert-info">{{ session('message') }}</div>
    @endif

    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>
        <h3>
            <div class="search">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>
        <form action="{{ url('mails') }}" method="GET">
            <div class="search_box">
                <div class="search_area">
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>件名</th>
                            <td><input type="text" name="SUBJECT" class="w350"></td>
                        </tr>
                        <tr>
                            <th>送付先</th>
                            <td><input type="text" name="CUSTOMER_CHARGE" class="w350"></td>
                        </tr>
                        <tr>
                            <th>ステータス</th>
                            <td>
                                @foreach ($mailstatus as $key => $value)
                                    <label><input type="checkbox" name="STATUS[]" value="{{ $key }}">
                                        {{ $value }}</label>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>種別</th>
                            <td>
                                @foreach ($type as $key => $value)
                                    <label><input type="checkbox" name="TYPE[]" value="{{ $key }}">
                                        {{ $value }}</label>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                    <div class="search_btn">
                        <a href="#" onclick="document.forms[0].submit();">
                            <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                        </a>
                    </div>
                </div>
                <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
            </div>
        </form>

        <h3>
            <div class="edit_02_mail">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $paginator->total() }}件中 0 - 0 件を表示
            </div>

            <div id='pagination'>
                @if ($paginator->onFirstPage())
                    <span class="disabled">
                        << {{ __('前へ') }}</span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" rel="prev">
                                << {{ __('前へ') }}</a>
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


                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
                @else
                    <span class="disabled">{{ __('次へ') }} >></span>
                @endif
            </div>

            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="list_area">
                @if (is_array($list))
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <a href="{{ route('mail.index', ['sort' => 'ITM_ID']) }}">No.</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('mail.index', ['sort' => 'RCV_NAME']) }}">送信先</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('mail.index', ['sort' => 'TYPE']) }}">種別</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('mail.index', ['sort' => 'SUBJECT']) }}">件名</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('mail.index', ['sort' => 'CUSTOMER']) }}">顧客名</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('mail.index', ['sort' => 'STATUS']) }}">ステータス</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('mail.index', ['sort' => 'SND_DATE']) }}">送信日</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('mail.index', ['sort' => 'RCV_DATE']) }}">受信日</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $val)
                                <tr>
                                    <td>{{ $val['Mail']['TML_ID'] }}</td>
                                    <td>{{ $val['Mail']['RCV_NAME'] . '(' . $val['Mail']['RECEIVER'] . ')' }}</td>
                                    <td>{{ $type[$val['Mail']['TYPE']] }}</td>
                                    <td>{{ $val['Mail']['SUBJECT'] }}</td>
                                    <td>{{ $val['Mail']['CUSTOMER'] }}</td>
                                    <td>
                                        @if ($val['Mail']['STATUS'] != 0)
                                            <a
                                                href="{{ url('mails/check/' . $val['Mail']['TML_ID']) }}">{{ $mailstatus[$val['Mail']['STATUS']] }}</a>
                                        @else
                                            {{ $mailstatus[$val['Mail']['STATUS']] }}
                                        @endif
                                    </td>
                                    <td>{{ $val['Mail']['SND_DATE'] }}</td>
                                    <td>{{ $val['Mail']['RCV_DATE'] ?: '&nbsp;' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
@endsection
