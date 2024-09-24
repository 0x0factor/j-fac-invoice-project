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

    <!-- Flash message -->
    @if (session('flash_message'))
        <div class="flash-message">
            {{ session('flash_message') }}
        </div>
    @endif


    <form action="{{ route('item.index') }}" method="get">
        @csrf
        <div id="contents">
            <div class="arrow_under">
                <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
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
                            <th>商品名</th>
                            <td><input type="text" name="ITEM" value="{{$search_name}}" class="w350"></td>
                        </tr>
                    </table>
                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <button
                                        onclick="document.getElementById('itemForm').submit();" style="border:none;">
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
                </div>
                <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
            </div>

            <div class="new_document">
                <a href="{{ route('item.add') }}">
                    <img src="{{ asset('img/bt_new.jpg') }}" alt="新規追加">
                </a>
            </div>


            <h3>
                <div class="edit_02_item">
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

                    <!-- Next Page Link -->
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
                    @else
                        <span class="disabled">{{ __('次へ') }} >></span>
                    @endif
                </div>


                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
                <div class="list_area">
                    @if (is_array($list))
                        <form action="{{ route('item.delete') }}" method="POST">
                            @csrf
                            <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                                <thead>
                                    <tr>
                                        <th class="w50">
                                            <input type="checkbox" name="action.select_all" class="chk_all"
                                                onclick="select_all();">
                                        </th>
                                        <th class="w50">
                                            <a href="{{ route('item.index', ['sort' => 'ITM_ID']) }}">No.</a>
                                        </th>
                                        <th class="w250">
                                            <a href="{{ route('item.index', ['sort' => 'ITEM_KANA']) }}">商品名</a>
                                        </th>
                                        <th class="w100">
                                            <a href="{{ route('item.index', ['sort' => 'ITEM_CODE']) }}">商品コード</a>
                                        </th>
                                        <th class="w200">
                                            <a href="{{ route('item.index', ['sort' => 'UNIT']) }}">単位</a>
                                        </th>
                                        <th class="w250">
                                            <a href="{{ route('item.index', ['sort' => 'UNIT_PRICE']) }}">価格</a>
                                        </th>
                                        <th class="w100">
                                            <a href="{{ route('item.index', ['sort' => 'TAX_CLASS']) }}">税区分</a>
                                        </th>
                                        @if ($user->AUTHORITY != 1)
                                            <th class="w100">
                                                <a href="{{ route('item.index', ['sort' => 'USR_ID']) }}">作成者</a>
                                            </th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $key => $val)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="ITM_ID[]"
                                                    value="{{ $val['ITM_ID'] ?? '' }}" class="chk"
                                                    style="width:30px;">
                                            </td>
                                            <td>{{ $key }}</td>
                                            <td>
                                                <a href="{{ route('item.check', $val['ITM_ID']) }}">{{ $val['ITEM'] }}</a>
                                            </td>
                                            <td>{{ $val['ITM_ID'] }}</td>
                                            <td>{{ $val['UNIT'] }}</td>
                                            <td>{{ $val['UNIT_PRICE'] }}</td>
                                            <td>{{ $val['TAX_CLASS'] }}</td>
                                            <td>{{ $val['customer']['NAME'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="list_btn">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="image" src="{{ asset('img/document/bt_delete2.jpg') }}" name="delete"
                                    alt="削除" onclick="return del();" class="mr5">
                            </div>
                        </form>
                    @endif
                </div>
                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
            </div>
        </div>

    </form>

@endsection
