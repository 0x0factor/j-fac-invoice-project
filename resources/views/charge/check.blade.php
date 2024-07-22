@extends('layout.default')

@section('content')
<!-- resources/views/charge/view.blade.php -->

<!-- 完了メッセージ -->
@if(session()->has('flash'))
    <div class="flash-message">
        {{ session('flash') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは自社担当者一覧の画面です。<br />「編集する」ボタンを押下すると自社担当者を変更できます。</p>
    </div>
</div>

<br class="clear" />
<!-- header_End -->
<!-- contents_Start -->
<div class="edit_btn">
    @if($editauth)
        <a href="{{ route('charge.edit', ['charge' => $params['Charge']['CHR_ID']]) }}" class="imgover">
            <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
        </a>
    @endif
    <form method="POST" action="{{ route('charge.moveback') }}" style="display:inline;">
        @csrf
        <a href="javascript:move_to_index();" class="imgover">
            <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
        </a>
    </form>
</div>

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>
    <h3>
        <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
    </h3>
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <form method="POST" action="{{ route('charge.update', ['charge' => $params['Charge']['CHR_ID']]) }}" enctype="multipart/form-data" class="Charge">
                @csrf
                @method('PUT')
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ステータス</th>
                        <td>{{ $status[$params['Charge']['STATUS']] }}</td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                    <tr>
                        <th style="width:150px;"><span class="float_l">担当者名</span></th>
                        <td style="width:730px;">
                            {!! nl2br(e($params['Charge']['CHARGE_NAME'])) !!}
                        </td>
                    </tr>
                    <!-- Continue with other table rows similarly -->
                </table>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>
    <div class="edit_btn">
        @if($editauth)
            <a href="{{ route('charge.edit', ['charge' => $params['Charge']['CHR_ID']]) }}" class="imgover">
                <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
            </a>
        @endif
        <a href="{{ route('charge.index') }}" class="imgover">
            <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
        </a>
    </div>
</div>

<input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
@endsection
