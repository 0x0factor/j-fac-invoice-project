@extends('layout.default')

@section('content')
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="search"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="search_box">
        <div class="search_area">
            <form action="{{ route('administers.index') }}" method="GET">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ユーザID</th>
                        <td>
                            <input type="text" name="LOGIN_ID" class="w350" value="{{ request()->input('LOGIN_ID') }}">
                        </td>
                    </tr>
                    <tr>
                        <th>ユーザ名</th>
                        <td>
                            <input type="text" name="NAME" class="w350" value="{{ request()->input('NAME') }}">
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('search-form').reset();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
        <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
    </div>

    <div class="new_document">
        <a href="{{ route('administers.create') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="">
        </a>
    </div>

    <h3>
        <div class="edit_02_administer"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box mb40">
        <div id='pagination'>
            {{ $administers->total() }}
        </div>

        <div id='pagination'>
            {{ $administers->links() }}
        </div>

        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="list_area">
            @if($administers->isNotEmpty())
                <form action="{{ route('administers.action') }}" method="POST">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <a href="{{ route('administers.index', ['sort' => 'USR_ID']) }}">No</a>
                                </th>
                                <th class="w300">
                                    <a href="{{ route('administers.index', ['sort' => 'LOGIN_ID']) }}">ユーザID</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('administers.index', ['sort' => 'NAME_KANA']) }}">ユーザ名</a>
                                </th>
                                <th class="w300">
                                    <a href="{{ route('administers.index', ['sort' => 'MAIL']) }}">メール</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('administers.index', ['sort' => 'STATUS']) }}">ステータス</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($administers as $administer)
                                @if($administer->AUTHORITY != 0)
                                    <tr>
                                        <td>{{ $administer->USR_ID }}</td>
                                        <td>
                                            <a href="{{ route('administers.show', $administer->USR_ID) }}">{{ $administer->LOGIN_ID }}</a>
                                        </td>
                                        <td>{{ $administer->NAME }}</td>
                                        <td>{{ $administer->MAIL ?: '&nbsp;' }}</td>
                                        <td>{{ $status[$administer->STATUS] }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </form>
            @endif
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>
</div>
@endsection
