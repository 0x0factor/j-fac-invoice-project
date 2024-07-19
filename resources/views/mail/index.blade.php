<script><!--
try{
	window.addEventListener("load",initTableRollovers('index_table'),false);
 }catch(e){
 	window.attachEvent("onload",initTableRollovers('index_table'));
}
--></script>

<script>
$(function() {
	setBeforeSubmit('<?php echo $this->name.ucfirst($this->action).'Form'; ?>');
});
</script>

@if(session()->has('message'))
    <div class="alert alert-info">{{ session('message') }}</div>
@endif

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('i_arrow_under.jpg') }}" alt="">
    </div>
    <h3>
        <div class="search">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>
    <form action="{{ url('mails') }}" method="GET">
        <div class="search_box">
            <div class="search_area">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr><th>件名</th><td><input type="text" name="SUBJECT" class="w350"></td></tr>
                    <tr><th>送付先</th><td><input type="text" name="CUSTOMER_CHARGE" class="w350"></td></tr>
                    <tr><th>ステータス</th><td>
                        @foreach($mailstatus as $key => $value)
                            <label><input type="checkbox" name="STATUS[]" value="{{ $key }}"> {{ $value }}</label>
                        @endforeach
                    </td></tr>
                    <tr><th>種別</th><td>
                        @foreach($type as $key => $value)
                            <label><input type="checkbox" name="TYPE[]" value="{{ $key }}"> {{ $value }}</label>
                        @endforeach
                    </td></tr>
                </table>
                <div class="search_btn">
                    <a href="#" onclick="document.forms[0].submit();">
                        <img src="{{ asset('bt_search.jpg') }}" alt="">
                    </a>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>
    </form>

    <h3>
        <div class="edit_02_mail">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box mb40">
        <div id='pagination'>
            {{ $paginator->total() }}
        </div>

        <div id='pagination'>
            {!! $paginator->previous('<< '.__('前へ'), ['class' => 'disabled', 'tag' => 'span']) !!} |
            {!! $paginator->links() !!} |
            {!! $paginator->next(__('次へ').' >>', ['class' => 'disabled', 'tag' => 'span']) !!}
        </div>

        <img src="{{ asset('bg_contents_top.jpg') }}" alt="">
        <div class="list_area">
            @if(is_array($list))
                <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                    <thead>
                        <tr>
                            <th class="w50">@sortablelink('Mail.TML_ID', 'No.')</th>
                            <th class="w100">@sortablelink('Mail.RCV_NAME', '送信先')</th>
                            <th class="w100">@sortablelink('Mail.TYPE', '種別')</th>
                            <th class="w200">@sortablelink('Mail.SUBJECT', '件名')</th>
                            <th class="w200">@sortablelink('Mail.CUSTOMER', '顧客名')</th>
                            <th class="w100">@sortablelink('Mail.STATUS', 'ステータス')</th>
                            <th class="w100">@sortablelink('Mail.SND_DATE', '送信日')</th>
                            <th class="w100">@sortablelink('Mail.RCV_DATE', '受信日')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $val)
                            <tr>
                                <td>{{ $val['Mail']['TML_ID'] }}</td>
                                <td>{{ $val['Mail']['RCV_NAME']."(".$val['Mail']['RECEIVER'].")" }}</td>
                                <td>{{ $type[$val['Mail']['TYPE']] }}</td>
                                <td>{{ $val['Mail']['SUBJECT'] }}</td>
                                <td>{{ $val['Mail']['CUSTOMER'] }}</td>
                                <td>
                                    @if($val['Mail']['STATUS'] != 0)
                                        <a href="{{ url('mails/check/'.$val['Mail']['TML_ID']) }}">{{ $mailstatus[$val['Mail']['STATUS']] }}</a>
                                    @else
                                        {{ $mailstatus[$val['Mail']['STATUS']] }}
                                    @endif
                                </td>
                                <td>{{ $val['Mail']['SND_DATE'] }}</td>
                                <td>{{ $val['Mail']['RCV_DATE'] ?: '&nbsp;' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <img src="{{ asset('bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>
</div>
