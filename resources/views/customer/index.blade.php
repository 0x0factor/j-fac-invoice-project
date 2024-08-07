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
@endsection

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
    @if (session('status'))
        {{ session('status') }}
    @endif

    <form method="GET" action="{{ route('customer.index') }}">
        @csrf
        <div id="contents">
            <div class="arrow_under">
                <img src="{{ asset('img/i_arrow_under.jpg') }}">
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
                            <td><input type="text" name="NAME" class="w350" value="{{ $search_name['NAME'] }}"></td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td><input type="text" name="ADDRESS" class="w350" value="{{ $search_name['ADDRESS'] }}"></td>
                        </tr>
                    </table>
                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <button
                                        onclick="document.getElementById('CustomerIndexForm').submit();" style="border: none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="" />
                                    </button>
                                </td>
                                <td style="border:none;">
                                    <button onclick="reset_forms();" style="border: none;">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="" />
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
            </div>
    </form>

    <div class="new_document">
        <a href="{{ route('customer.add') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="" />
        </a>
    </div>

    <h3>
        <div class="edit_02_customer">
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
        <img src="{{ asset('img/bg_contents_top.jpg') }}">
        <div class="list_area">
            <form method="POST" action="{{ route('customer.index') }}">
                @csrf
                @if (is_array($list))
                    <table width="900" cellpadding="0" cellspacing="0" border="0" style="break-word:break-all;"
                        id="index_table">
                        <thead>
                            <tr>
                                <th width="50" class="w50"><input type="checkbox" name="action.select_all"
                                        class="chk_all" onclick="select_all();"></th>

                                <th class="w50">
                                    <a href="{{ route('item.index', ['sort' => 'CST_ID']) }}">No.</a>
                                </th>
                                <th class="w250">
                                    <a href="{{ route('item.index', ['sort' => 'NAME_KANA']) }}">顧客名</a>
                                </th>
                                <th class="w250">
                                    <a href="{{ route('item.index', ['sort' => 'CNT_ID']) }}">住所</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('item.index', ['sort' => 'PHONE_NO1']) }}">電話番号</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('item.index', ['sort' => 'CHR_ID']) }}">担当者</a>
                                </th>
                                @if ($user['AUTHORITY'] != 1)
                                    <th class='w100'>
                                        <a href="{{ route('item.index', ['sort' => 'USR_ID']) }}">作成者</a>
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $key => $val)

                                <tr>
                                    <td>
                                        @if ($val['CST_ID'])
                                            <input type="checkbox" name="{{ $val['CST_ID'] }}" class="chk">
                                        @endif
                                    </td>
                                    <td>{{ $val['CST_ID'] }}</td>
                                    <td>
                                        @if ($val['CST_ID'] == 1)
                                            <a
                                                href="{{ route('customer.check', ['id' => $val['CST_ID']]) }}">{{ $val['NAME'] }}</a>
                                        @else
                                            {{ $val['NAME'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['CNT_ID'] || $val['ADDRESS'] || $val['SEARCH_ADDRESS'])
                                            <!-- @if ($val['CNT_ID'])
                                                {{ $val['CNT_ID'] }}
                                            @endif
                                            {{ $val['ADDRESS'] }} -->
                                            {{ $val['SEARCH_ADDRESS'] }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        @if (
                                            !empty($val['PHONE_NO1']) ||
                                                !empty($val['PHONE_NO2']) ||
                                                !empty($val['PHONE_NO3']))
                                            {{ $val['PHONE_NO1'] . '-' . $val['PHONE_NO2'] . '-' . $val['PHONE_NO3'] }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['CHR_ID'])
                                            {{ $charges[$val['CHR_ID']], 'Charge', 'CHARGE_NAME' }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    @if ($user['AUTHORITY'] != 1)
                                        <td>{{ $user['NAME'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <div class="list_btn">
                    @csrf
                    <input type="image" src="{{ asset('/img/document/bt_delete2.jpg') }}" name="delete" alt="削除"
                        onclick="return del();" class="mr5">
                </div>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
    </div>
    </div>
@endsection
