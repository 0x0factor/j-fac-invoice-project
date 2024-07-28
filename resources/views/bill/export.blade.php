@extends('layout.default')

@section('content')
    @if (session()->has('flash_message'))
        {{ session('flash_message') }}
    @endif

    <div id="contents">
        <div class="search_box">
            <div class="search_area">
                <form action="{{ route('bill.index') }}" method="POST">
                    @csrf
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                以下のプルダウンより抽出する期間を設定してください。期間は請求書の発行日となります。
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="datetime-local" name="DATE1">
                                ～
                                <input type="datetime-local" name="DATE2">
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <button type="submit">
                            <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                        </button>
                    </div>
                </form>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" />
        </div>
    </div>
@endsection
