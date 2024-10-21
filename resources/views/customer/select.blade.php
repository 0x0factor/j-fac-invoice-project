@extends('layout.default')

@section('scripts')
    <script>
        try {
            window.addEventListener("load", initTableRollovers('index_table'), false);
        } catch (e) {
            window.attachEvent("onload", initTableRollovers('index_table'));
        }
    </script>
@endsection

@section('content')

    {{-- Flash message --}}
    @if (session('flash_message'))
        <div class="flash-message">
            {{ session('flash_message') }}
        </div>
    @endif


    <div id="contents">
        <form action="/customers/select" method="GET" novalidate>
            <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}"></div>

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
                            <td><input type="text" name="NAME" class="w350" value="{{$search_name}}"></td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <button onclick="$('#CustomerSelectForm').submit();" class="btn btn-primary" style="border: none;">
                            <img src="{{ asset('img/bt_search.jpg') }}">
                        </button>
                    </div>
                </div>
                <img src="{{ asset('/img/document/bg_search_bottom.jpg') }}" class='block'>
            </div>
        </form>

        <h3>
            <div class="edit_02_customer"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="contents_box mb40">
            <div id="pagination"> {{ $paginator->total() }}  件中 {{ ($paginator->count() * ($paginator-> currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator-> currentPage()) }} 件表示中</div>
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
                @if (is_array($list))
                    <table width="900" cellpadding="0" cellspacing="0" border="0" style="break-word:break-all;"
                        id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <a href="{{ route('customer.select', ['sort' => 'CST_ID']) }}">No.</a>
                                </th>
                                <th class="w250">
                                    <a href="{{ route('customer.select', ['sort' => 'NAME_KANA']) }}">顧客名</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('customer.select', ['sort' => 'PHONE_NO1']) }}">電話番号</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('customer.select', ['sort' => 'CHR_ID']) }}">担当者</a>
                                </th>
                                <th class="w400">
                                    帳票
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($list as $val)
                                <tr>
                                    <td>{{ $val['CST_ID'] }}</td>
                                    <td>
                                        <a
                                            href="{{ route('customer.check', $val['CST_ID']) }}">{{ $val['NAME'] }}</a>
                                    </td>
                                    <td>
                                        @if (
                                            !empty($val['PHONE_NO1']) ||
                                                !empty($val['PHONE_NO2']) ||
                                                !empty($val['PHONE_NO3']))
                                            {{ $val['PHONE_NO1'] }}-{{ $val['PHONE_NO2'] }}-{{ $val['PHONE_NO3'] }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['CHR_ID'])
                                            {!! nl2br(e($charges[$val['CHR_ID']]['CHARGE_NAME'])) !!}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        <a
                                            href="{{ route('quote.index', ['customer' => $val['CST_ID']]) }}">見積書</a>
                                        ({{ $inv_num[$val['CST_ID']]['Quote'] }}件) /
                                        <a
                                            href="{{ route('bill.index', ['customer' => $val['CST_ID']]) }}">請求書</a>
                                        ({{ $inv_num[$val['CST_ID']]['Bill'] }}件) /
                                        <a
                                            href="{{ route('delivery.index', ['customer' => $val['CST_ID']]) }}">納品書</a>
                                        ({{ $inv_num[$val['CST_ID']]['Delivery'] }}件)
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class='block'>
        </div>
    </div>
    </div>
@endsection
