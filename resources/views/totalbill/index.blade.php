@extends('layout.default')

@section('content')

<script><!--
try{
	window.addEventListener("load",initTableRollovers('index_table'),false);
 }catch(e){
 	window.attachEvent("onload",initTableRollovers('index_table'));
}
--></script>
<script><!--
function select_all() {
	$(".chk").attr("checked", $(".chk_all").attr("checked"));
	$('input[name="delete"]').attr('disabled','');
	$('input[name="reproduce"]').attr('disabled','');
}

--></script>

<script>
$(function() {
	setBeforeSubmit('<?php echo $this->name.ucfirst($this->action).'Form'; ?>');
});
</script>
{{-- Display Flash Messages --}}
@if (session('flash_message'))
    <div class="flash-message">
        {{ session('flash_message') }}
    </div>
@endif

{{-- Form Start --}}
<form method="GET" action="{{ route('totalbill.index') }}">
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
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
                        <td><input type="text" name="NAME" class="w350" value="{{ request('NAME') }}"></td>
                    </tr>
                    <tr>
                        <th>件名</th>
                        <td><input type="text" name="SUBJECT" class="w350" value="{{ request('SUBJECT') }}"></td>
                    </tr>
                    <tr>
                        <th>発行日 開始日</th>
                        <td width="320">
                            <input type="text" id="ACTION_DATE_FROM" name="ACTION_DATE_FROM" class="w100 p2 date cal" readonly value="{{ request('ACTION_DATE_FROM') }}">
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_FROM').value = new Date().toISOString().split('T')[0];">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="showCalendar('ACTION_DATE_FROM');">
                            <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_FROM').value = '';">
                        </td>
                    </tr>
                    <tr>
                        <th>発行日 終了日</th>
                        <td width="320">
                            <input type="text" id="ACTION_DATE_TO" name="ACTION_DATE_TO" class="w100 p2 date cal" readonly value="{{ request('ACTION_DATE_TO') }}">
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('ACTION_DATE_TO').value = new Date().toISOString().split('T')[0];">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="showCalendar('ACTION_DATE_TO');">
                            <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('ACTION_DATE_TO').value = '';">
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <button type="submit">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="Search">
                                </button>
                            </td>
                            <td style="border:none;">
                                <button type="button" onclick="resetForms()">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="Reset">
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/bg_search_bottom.jpg') }}" class="block" alt="Search Bottom">
        </div>

        <div id="calid"></div>

        {{-- Form End --}}
    </form>

    <div class="new_document">
        <a href="{{ route('totalbill.create') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="New">
        </a>
    </div>

    <h3>
        <div class="edit_02_totalbill">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box mb40">
        <div id='pagination'>
            {{ $paginator->total() }}
        </div>

        <div id='pagination'>
            {{ $paginator->onFirstPage() ? '<< ' . __('前へ') : '' }}
            |
            {{ $paginator->links() }}
            |
            {{ $paginator->hasMorePages() ? __('次へ') . ' >>' : '' }}
        </div>

        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="list_area">
            @if(is_array($list))
                <form method="POST" action="{{ route('totalbill.action') }}">
                    @csrf
                    <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                        <thead>
                            <tr>
                                <th class="w50">
                                    <input type="checkbox" class="chk_all" onclick="selectAll();">
                                </th>
                                <th class="w50">{{ __('No.') }}</th>
                                <th class="w150">{{ __('顧客名') }}</th>
                                <th class="w200">{{ __('件名') }}</th>
                                <th class="w100">{{ __('合計金額') }}</th>
                                <th class="w100">{{ __('発行日') }}</th>
                                @if($user['AUTHORITY'] != 1)
                                    <th class="w150">{{ __('作成者') }} / {{ __('更新者') }}</th>
                                @endif
                                <th class="w100">{{ __('発行ステータス') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($list as $val)
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="{{ $val['Totalbill']['TBL_ID'] }}" class="chk"></td>
                                    <td>{{ $val['Totalbill']['TBL_ID'] }}</td>
                                    <td>{{ $val['Customer']['NAME'] }}</td>
                                    <td><a href="{{ route('totalbill.show', ['id' => $val['Totalbill']['TBL_ID']]) }}">{{ $val['Totalbill']['SUBJECT'] }}</a></td>
                                    <td>{{ isset($val['Totalbill']['THISM_BILL']) ? $val['Totalbill']['THISM_BILL'] . '円' : '&nbsp;' }}</td>
                                    <td>{{ $val['Totalbill']['ISSUE_DATE'] ?: '&nbsp;' }}</td>
                                    @if($user['AUTHORITY'] != 1)
                                        <td>{{ $val['User']['NAME'] }} / {{ $val['UpdateUser']['NAME'] ?? '&nbsp;' }}</td>
                                    @endif
                                    <td>{{ $edit_stat[$val['Totalbill']['EDIT_STAT']] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="list_btn">
                        <button type="submit" onclick="return confirm('削除してもよろしいですか？');">
                            <img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete">
                        </button>
                    </div>
                </form>
            @endif
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
    </div>
</div>

{{-- JavaScript --}}
<script>
    function showCalendar(inputId) {
        // Implement your calendar logic here
    }

    function resetForms() {
        document.querySelectorAll('form').forEach(form => form.reset());
    }

    function selectAll() {
        document.querySelectorAll('.chk').forEach(checkbox => checkbox.checked = true);
    }
</script>
@endsection
