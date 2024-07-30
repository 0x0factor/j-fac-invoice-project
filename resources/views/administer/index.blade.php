@extends('layout.default')

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            initTableRollovers('index_table');
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



<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="search"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="search_box">
        <div class="search_area">
            <form action="{{ route('administer.index') }}" method="GET">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ユーザID</th>
                        <td>
                            <input type="text" name="LOGIN_ID" class="w350"
                                value="{{ request()->input('LOGIN_ID') }}">
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
                                <a href="#"
                                    onclick="event.preventDefault(); document.getElementById('search-form').reset();">
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
        <a href="{{ route('administer.add') }}" method="GET">
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
            <!-- Previous Page Link -->
            @if ($administers->onFirstPage())
                <span class="disabled">
                    << {{ __('前へ') }}</span>
                    @else
                        <a href="{{ $administers->previousPageUrl() }}" rel="prev">
                            << {{ __('前へ') }}</a>
            @endif

            <!-- Pagination Elements -->
            @foreach ($administers->links()->elements as $element)
                <!-- "Three Dots" Separator -->
                @if (is_string($element))
                    <span class="disabled">{{ $element }}</span>
                @endif

                <!-- Array Of Links -->
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $administers->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            <!-- Next Page Link -->
            @if ($administers->hasMorePages())
                <a href="{{ $administers->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
            @else
                <span class="disabled">{{ __('次へ') }} >></span>
            @endif
        </div>


        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="list_area">
            @if (is_array($list))
                <form action="{{ route('administer.add') }}" method="POST">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <a href="{{ route('administer.index', ['sort' => 'USR_ID']) }}">No</a>
                                </th>
                                <th class="w300">
                                    <a href="{{ route('administer.index', ['sort' => 'LOGIN_ID']) }}">ユーザID</a>
                                </th>
                                <th class="w200">
                                    <a href="{{ route('administer.index', ['sort' => 'NAME_KANA']) }}">ユーザ名</a>
                                </th>
                                <th class="w300">
                                    <a href="{{ route('administer.index', ['sort' => 'MAIL']) }}">メール</a>
                                </th>
                                <th class="w100">
                                    <a href="{{ route('administer.index', ['sort' => 'STATUS']) }}">ステータス</a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $val)
                                @if ($val->AUTHORITY != 0)
                                    <tr>
                                        <td>{{ $val->USR_ID }}</td>
                                        <td>
                                            <a
                                                href="{{ route('administer.show', $val->USR_ID) }}">{{ $val->LOGIN_ID }}</a>
                                        </td>
                                        <td>{{ $val->NAME }}</td>
                                        <td>{{ $val->MAIL ?: '&nbsp;' }}</td>
                                        <td>{{ $status[$val->STATUS] }}</td>
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
