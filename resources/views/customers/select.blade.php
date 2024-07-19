@extends('layout.default')

@section('content')
<script>
    window.addEventListener("load", function() {
        initTableRollovers('index_table');
    }, false);
</script>

@if (session()->has('flash_message'))
    {{ session('flash_message') }}
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
                        <td><input type="text" name="NAME" class="w350" value=""></td>
                    </tr>
                </table>

                <div class="search_btn">
                    <a href="#" onclick="$('#CustomerSelectForm').submit();" class="btn btn-primary">
                        <img src="{{ asset('img/bt_search.jpg') }}">
                    </a>
                </div>
            </div>
            <img src="{{ asset('/img/document/bg_search_bottom.jpg') }}" class='block'>
        </div>
    </form>

    <h3>
        <div class="edit_02_customer"><span class="edit_txt">&nbsp;</span></div>
    </h3>
    <div class="contents_box mb40">
        <div id="pagination">0 件中 0 - 0 件を表示</div>
        <div id="pagination">
		    <span class="disabled">&lt;&lt; 前へ</span> || <span class="disabled">次へ &gt;&gt;</span>
        </div>
        <img src="{{ asset('img/bg_contents_top.jpg') }}">
        <div class="list_area">
            <table width="900" cellpadding="0" cellspacing="0" border="0" style="break-word:break-all;"
                id="index_table">
                <thead>
                    <tr>
                        <th class="w50"><a href="" class="asc">No.</a></th>
                        <th class="w250"><a href="" class="asc">顧客名</a></th>
                        <th class="w100"><a href="" class="asc">電話番号</a></th>
                        <th class="w100"><a href="" class="asc">担当者</a></th>
                        <th class="w400">帳票</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td>
                        <td>

                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            &nbsp;
                        </td>
                        <td>
                            <a href="{{ route('quotes.index')}}">
                                見積書
                            </a>
                            <a href="{{ route('bills.index')}}">
                                請求書
                            </a>
                            <a href="{{ route('deliveries.index')}}">
                                納品書
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class='block'>
    </div>
    </div>
</div>
@endsection
