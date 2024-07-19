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
@if(session('status'))
    {{ session('status') }}
@endif

<form method="GET" action="{{ route('customer.index') }}">
    @csrf
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}">
        </div>

        <h3>
            <div class="search">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>
        <div class="search_box">
            <div class="search_area">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr><th>顧客名</th><td><input type="text" name="NAME" class="w350" value="{{ old('NAME') }}"></td></tr>
                    <tr><th>住所</th><td><input type="text" name="ADDRESS" class="w350" value="{{ old('ADDRESS') }}"></td></tr>
                </table>
                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('CustomerIndexForm').submit();">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="" />
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="" />
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
        </div>
    </form>

    <div class="new_document">
        <a href="{{ route('customers.add') }}">
            <img src="{{ asset('img/bt_new.jpg') }}" alt="" />
        </a>
    </div>

    <h3>
        <div class="edit_02_customer">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box mb40">
        <div id='pagination'>
            {{ $paginator->total() }}
        </div>
        <div id='pagination'>
            {!! $paginator->prev('<< '.__('前へ'), [], null, ['class'=>'disabled', 'tag' => 'span']) !!} |
            {!! $paginator->numbers().' | '.$paginator->next(__('次へ').' >>', [], null, ['tag' => 'span', 'class' => 'disabled']) !!}
        </div>
        <img src="{{ asset('img/bg_contents_top.jpg') }}">
        <div class="list_area">
            <form method="POST" action="{{ route('customer.index') }}">
                @csrf
                @if (is_array($list))
                    <table width="900" cellpadding="0" cellspacing="0" border="0" style="break-word:break-all;" id="index_table">
                        <thead>
                            <tr>
                                <th width="50" class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                                <th class="w50"><?php echo $customHtml->sortLink('No.', 'Customer.CST_ID'); ?></th>
                                <th class="w250"><?php echo $customHtml->sortLink('顧客名', 'Customer.NAME_KANA'); ?></th>
                                <th class="w250"><?php echo $customHtml->sortLink('住所', 'Customer.CNT_ID'); ?></th>
                                <th class="w100"><?php echo $customHtml->sortLink('電話番号', 'Customer.PHONE_NO1'); ?></th>
                                <th class="w200"><?php echo $customHtml->sortLink('担当者', 'Customer.CHR_ID'); ?></th>
                                @if ($user['AUTHORITY'] != 1)
                                    <th class='w100'><?php echo $customHtml->sortLink('作成者', 'Customer.USR_ID'); ?></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($list as $key => $val)
                                <tr>
                                    <td>@if (!$delcheck[$val['Customer']['CST_ID']])<input type="checkbox" name="{{ $val['Customer']['CST_ID'] }}" class="chk">@endif</td>
                                    <td>{{ $val['Customer']['CST_ID'] }}</td>
                                    <td>
                                        @if ($authcheck[$val['Customer']['CST_ID']] == 1)
                                            <a href="{{ route('customers.check', ['id' => $val['Customer']['CST_ID']]) }}">{{ $val['Customer']['NAME'] }}</a>
                                        @else
                                            {{ $customHtml->ht2br($val['Customer']['NAME']) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['Customer']['CNT_ID'] || $customHtml->ht2br($val['Customer']['ADDRESS'], 'Customer', 'ADDRESS'))
                                            @if ($val['Customer']['CNT_ID'])
                                                {{ $countys[$val['Customer']['CNT_ID']] }}
                                            @endif
                                            {{ $customHtml->ht2br($val['Customer']['ADDRESS'], 'Customer', 'ADDRESS') }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        @if (!empty($val['Customer']['PHONE_NO1']) || !empty($val['Customer']['PHONE_NO2']) || !empty($val['Customer']['PHONE_NO3']))
                                            {{ $val['Customer']['PHONE_NO1']."-".$val['Customer']['PHONE_NO2']."-".$val['Customer']['PHONE_NO3'] }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    <td>
                                        @if ($val['Customer']['CHR_ID'])
                                            {{ $customHtml->ht2br($charges[$val['Customer']['CHR_ID']], 'Charge', 'CHARGE_NAME') }}
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    @if ($user['AUTHORITY'] != 1)
                                        <td>{{ $val['User']['NAME'] }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
                <div class="list_btn">
                    @csrf
                    <input type="image" src="{{ asset('/img/document/bt_delete2.jpg') }}" name="delete" alt="削除" onclick="return del();" class="mr5">
                </div>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
    </div>
</div>
@endsection
