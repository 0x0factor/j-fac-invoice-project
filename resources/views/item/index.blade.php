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

@if(session()->has('flash'))
    {{ session('flash') }}
@endif

<form action="{{ route('items.index') }}" method="get">
    @csrf
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
                        <th>商品名</th>
                        <td><input type="text" name="ITEM" value="{{ old('ITEM') }}" class="w350"></td>
                    </tr>
                </table>
                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="event.preventDefault(); document.getElementById('itemForm').submit();">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="リセット">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
</form>

<div class="new_document">
    <a href="{{ route('items.add') }}">
        <img src="{{ asset('img/bt_new.jpg') }}" alt="新規追加">
    </a>
</div>


<h3>
    <div class="edit_02_item">
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box mb40">
    <div id='pagination'>
        {{ $paginator->data['Item']['count'] }}
    </div>

    <div id='pagination'>
        {!! $paginator->prev('<< '.__('前へ', true), ['class'=>'disabled', 'tag' => 'span']) !!} |
        {!! $paginator->numbers() !!} |
        {!! $paginator->next(__('次へ', true).' >>', ['tag' => 'span', 'class' => 'disabled']) !!}
    </div>

    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">

    <div class="list_area">
        @if(is_array($list))
            <form action="{{ route('items.delete') }}" method="post">
                @csrf
                <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                    <thead>
                        <tr>
                            <th class="w50"><input type="checkbox" name="action.select_all" class="chk_all" onclick="select_all();"></th>
                            <th class="w50">{{ $customHtml->sortLink('No.', 'Item.ITM_ID') }}</th>
                            <th class="w250">{{ $customHtml->sortLink('商品名', 'Item.ITEM_KANA') }}</th>
                            <th class="w100">{{ $customHtml->sortLink('商品コード', 'Item.ITEM_CODE') }}</th>
                            <th class="w200">{{ $customHtml->sortLink('単位', 'Item.UNIT') }}</th>
                            <th class="w250">{{ $customHtml->sortLink('価格', 'Item.UNIT_PRICE') }}</th>
                            <th class="w100">{{ $customHtml->sortLink('税区分', 'Item.TAX_CLASS') }}</th>
                            @if($user['AUTHORITY'] != 1)
                                <th class="w100">{{ $customHtml->sortLink('作成者', 'Item.USR_ID') }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $key => $val)
                            <tr>
                                <td><input type="checkbox" name="ITM_ID[]" value="{{ $val['Item']['ITM_ID'] }}" class="chk" style="width:30px;"></td>
                                <td>{{ $val['Item']['ITM_ID'] }}</td>
                                <td><a href="{{ route('items.check', $val['Item']['ITM_ID']) }}">{{ $val['Item']['ITEM'] }}</a></td>
                                <td>{!! $customHtml->ht2br($val['Item']['ITEM_CODE'], 'Item', 'ITEM_CODE') ?: "&nbsp;" !!}</td>
                                <td>{!! $customHtml->ht2br($val['Item']['UNIT'], 'Item', 'UNIT') ?: "&nbsp;" !!}</td>
                                <td>{!! $customHtml->ht2br($val['Item']['UNIT_PRICE'], 'Item', 'UNIT_PRICE') ?: "&nbsp;" !!}</td>
                                <td>{{ $excises[$val['Item']['TAX_CLASS']] }}</td>
                                @if($user['AUTHORITY'] != 1)
                                    <td>{{ $val['User']['NAME'] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="submit" value="削除" onclick="return del();" class="mr5">
            </form>
        @endif
    </div>
    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
</div>
@endsection
