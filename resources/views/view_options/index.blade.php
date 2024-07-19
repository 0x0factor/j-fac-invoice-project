@extends('layout.default')

@section('content')


@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide">
        <p>こちらのページはデザイン設定確認の画面です。@if($user["AUTHORITY"]==0)<br />「編集する」ボタンを押下するとデザイン設定を変更できます。@endif</p>
    </div>
</div>
<br class="clear" />

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow">
    </div>
    <h3>
        <div class="edit_02_view_option">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                @foreach($options as $option)
                    @php
                        $option_name = $option['ViewOption']['OPTION_NAME'];
                        $option_name_jp = $option['ViewOption']['OPTION_NAME_JP'];
                        $option_value = $option['ViewOption']['OPTION_VALUE'];
                    @endphp
                    <tr>
                        <th width="130px">{{ $option_name_jp }}</th>
                        <td width="750px">
                            @if($option_name === 'logo')
                                @if(!empty($option_value))
                                    <img src="{{ asset('cms/' . $option_value) }}" height="40" alt="Logo">
                                    {{ $option_value }}<br /><br />
                                @endif
                            @else
                                {!! nl2br(e($option_value)) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Bottom Background">
    </div>
</div>

@if($user['AUTHORITY'] == 0)
    <div class="edit_btn">
        <a href="{{ route('view_options.edit') }}">
            <img src="{{ asset('img/bt_edit.jpg') }}" class="imgover" alt="編集する">
        </a>
    </div>
@endif

<input type="hidden" name="CMP_ID" value="1">
@endsection
