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
    @if(isset($name) && isset($action))
        setBeforeSubmit('{{ $name . ucfirst($action) . 'Form' }}');
    @else
        console.error("Name or action is not set.");
    @endif
});
</script>


@if (session()->has('status'))
    {{ session('status') }}
@endif

<form method="GET" action="{{ route('customer_charge.index') }}">
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
                        <th>担当者名</th>
                        <td>
                            <input type="text" name="CHARGE_NAME" value="{{ request('CHARGE_NAME') }}" class="w350">
                        </td>
                    </tr>
                    <tr>
                        <th>企業名</th>
                        <td>
                            <input type="text" name="COMPANY_NAME" value="{{ request('COMPANY_NAME') }}" class="w350">
                        </td>
                    </tr>
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS" class="form-control">
                                <option value="" {{ empty(request('STATUS')) ? 'selected' : '' }}>項目を選んでください</option>
                                @foreach ($status ?? [] as $key => $value)
                                    <option value="{{ $key }}" {{ request('STATUS') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach

                            </select>
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('searchForm').submit();">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>

        <div class="new_document">
            <a href="{{ route('customer_charge.add') }}">
                <img src="{{ asset('img/bt_new.jpg') }}" alt="">
            </a>
        </div>

        <h3>
            <div class="edit_02_c_charge">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $paginator->total() }}件中 0 - 0 件を表示
            </div>
            <div id='pagination'>
                <!-- Previous Page Link -->
                @if ($paginator->onFirstPage())
                    <span class="disabled"><< {{ __('前へ') }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><< {{ __('前へ') }}</a>
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
                    <form method="POST" action="{{ route('customer_charge.index') }}" id="indexForm">
                        @csrf
                        <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                            <thead>
                            <tr>
                                <th class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                                <th class="w50">
                                    <a href="{{ route('item.index', ['sort' => 'CHRC_ID']) }}">No.</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('item.index', ['sort' => 'CHARGE_NAME']) }}">担当者</a>
                                </th>
                                <th class="w250">
                                    <a href="{{ route('item.index', ['sort' => 'CST_ID']) }}">顧客名</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('item.index', ['sort' => 'PHONE_NO1']) }}">電話番号</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('item.index', ['sort' => 'STATUS']) }}">ステータス</a>
                                </th>
                                @if ($user['AUTHORITY'] != 1)
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
                                        @if (!isset($delcheck[$val['CustomerCharge']['CHRC_ID']]) || !$delcheck[$val['CustomerCharge']['CHRC_ID']])
                                            <input type="checkbox" name="{{ $val['CustomerCharge']['CHRC_ID'] }}" class="chk">
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>{{ $val['CustomerCharge']['CHRC_ID'] }}</td>
                                    <td>
                                        @php
                                            $chargeNameLink = $authcheck[$val['CustomerCharge']['CHRC_ID']] ? route('customer_charge.check', $val['CustomerCharge']['CHRC_ID']) : null;
                                        @endphp
                                        {!! $chargeNameLink ? '<a href="'.$chargeNameLink.'">' : '' !!}
                                        {{ $customHtml->ht2br($val['CustomerCharge']['CHARGE_NAME'], 'CustomerCharge', 'CHARGE_NAME') }}
                                        {!! $chargeNameLink ? '</a>' : '' !!}
                                    </td>
                                    <td>{{ $val['CustomerCharge']['CST_ID'] != 0 ? $customer[$val['CustomerCharge']['CST_ID']] : "&nbsp;" }}</td>
                                    <td>
                                        @if (!empty($val['CustomerCharge']['PHONE_NO1']) || !empty($val['CustomerCharge']['PHONE_NO2']) || !empty($val['CustomerCharge']['PHONE_NO3']))
                                            {{ $val['CustomerCharge']['PHONE_NO1']."-".$val['CustomerCharge']['PHONE_NO2']."-".$val['CustomerCharge']['PHONE_NO3'] }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>{{ $status[$val['CustomerCharge']['STATUS']] }}</td>
                                    @if ($user['AUTHORITY'] != 1)
                                        <td>{{ $val['User']['NAME'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="list_btn">
                            <input type="image" src="{{ asset('/img/document/bt_delete2.jpg') }}" alt="削除" name="delete" class="mr5" onclick="return del();">
                        </div>
                    </form>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
</form>
@endsection
