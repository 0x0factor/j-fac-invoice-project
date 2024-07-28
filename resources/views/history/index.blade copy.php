@extends('layout.default')

@section('content')
@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    try {
        window.addEventListener("load", initTableRollovers('index_table'), false);
    } catch(e) {
        window.attachEvent("onload", initTableRollovers('index_table'));
    }

    $(function() {
        setBeforeSubmit('{{ class_basename(request()->route()->getController()) . ucfirst(request()->route()->getActionMethod()) }}Form');
    });
});
</script>
@endsection
<!-- resources/views/history/index.blade.php -->

<!-- Flash Message -->
@if(session()->has('flash_notification.message'))
    <div class="alert alert-{{ session('flash_notification.level') }}">
        {{ session('flash_notification.message') }}
    </div>
@endif

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3><div class="search"><span class="edit_txt">&nbsp;</span></div></h3>
    <div class="search_box">
        <div class="search_area">
            <form action="{{ url('deliveries') }}" method="POST">
                @csrf
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                            以下のプルダウンより抽出する期間を設定してください。期間は納品書の発行日となります。
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="datetime-local" name="DATE1" class="w100 p2 date cal" readonly>
                            　～　
                            <input type="datetime-local" name="DATE2" class="w100 p2 date cal" readonly>
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <input type="submit" name="download" value="検索する" class="btn-submit">
                </div>
            </form>
        </div>
        <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
    </div>
    <div id="calid"></div>

    <h3><div class="edit_02_history"><span class="edit_txt">&nbsp;</span></div></h3>

    <div class="contents_box mb40">
        <div id="pagination">
            {{ $paginator->total() }}
        </div>

        <div id="pagination">
            {!! $paginator->previousPageUrl() ? '<a href="' . $paginator->previousPageUrl() . '">' . __('前へ') . '</a>' : '<span class="disabled">前へ</span>' !!}
            |
            {!! $paginator->nextPageUrl() ? '<a href="' . $paginator->nextPageUrl() . '">' . __('次へ') . '</a>' : '<span class="disabled">次へ</span>' !!}
        </div>

        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="list_area">
            @if(is_array($histories) && count($history) > 0)
                <form action="{{ url('action') }}" method="POST">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w200">日付</th>
                                <th class="w100">ユーザ名</th>
                                <th class="w200">動作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($history as $key => $val)
                                <tr>
                                    <td>{{ $val['History']['ACTION_DATE'] }}</td>
                                    <td>{{ $val['User']['NAME'] }}</td>
                                    <td>
                                        @php
                                            $actionText = '';
                                            switch ($val['History']['ACTION']) {
                                                case 'login':
                                                    $actionText = 'ログインしました';
                                                    break;
                                                case 'logout':
                                                    $actionText = 'ログアウトしました';
                                                    break;
                                                case 'create_quote':
                                                    $actionText = '見積書のID(' . link_to_action('QuoteController@check', $val['History']['RPT_ID'], $val['History']['RPT_ID']) . ')を作成しました';
                                                    break;
                                                // Add other cases as needed
                                                default:
                                                    break;
                                            }
                                            echo $actionText;
                                        @endphp
                                    </td>
                                </tr>
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
