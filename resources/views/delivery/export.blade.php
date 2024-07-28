@extends('layout.default')

@section('content')
    {{-- Complete message --}}
    @if (session()->has('status'))
        {{ session('status') }}
    @endif

    <div id="contents">
        <div class="search_box">
            <div class="search_area">
                <form method="POST" action="{{ route('delivery.index') }}">
                    @csrf
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td>
                                以下のプルダウンより抽出する期間を設定してください。期間は納品書の発行日となります。
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <input type="datetime-local" name="DATE1" class="form-control">
                                ～
                                <input type="datetime-local" name="DATE2" class="form-control">
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <button type="submit" name="download" class="imgover" alt="検索する">
                            <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                        </button>
                    </div>
                </form>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
@endsection
