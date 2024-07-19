@extends('layout.default')

@section('content')

@if(session()->has('flash'))
    {{ session('flash') }}
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" />
        <p>こちらのページは見積書確認の画面です。<br />「編集する」ボタンを押すと見積書を編集することができます。</p>
    </div>
</div>

<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    @include('elements.arrow_under')
    @include('elements.form.check_buttons')
    @include('elements.form.check_basic_information')
    @include('elements.arrow_under')
    @include('elements.form.check_detail')
    @include('elements.arrow_under')
    <h3><div class="edit_03"><span class="edit_txt">&nbsp;</span></div></h3>

<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" />
    <div class="contents_area">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="w100">発行ステータス</th>
                <td class="w770">{{ $status[$customHtml->ht2br($param['Quote']['STATUS'],'Quote','STATUS')] }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="w100">納入期限</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['DEADLINE'],'Quote','DEADLINE') }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="w100">取引方法</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['DEAL'],'Quote','DEAL') }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="w100">納入場所</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['DELIVERY'],'Quote','DELIVERY') }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="w100">有効期限</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['DUE_DATE'],'Quote','DUE_DATE') }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="txt_top w100">備考</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['NOTE'],'Quote','NOTE') }}</td>
            </tr>
            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" /></td></tr>

            <tr>
                <th class="w100">メモ</th>
                <td class="w770">{{ $customHtml->ht2br($param['Quote']['MEMO'],'Quote','MEMO') }}</td>
            </tr>
        </table>
    </div>
    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
</div>

<div class="arrow_under">
    <img src="{{ asset('img/i_arrow_under.jpg') }}" />
</div>

<!-- Include other elements if needed -->
</div>
@endsection
