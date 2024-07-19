@extends('layout.default')

@section('content')
@if(session()->has('status'))
    {{ session('status') }}
@endif

@include('elements.form.scripts')

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
        <p>こちらのページは請求書編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押すと請求書の変更を保存できます。</p>
    </div>
</div>
<br class="clear">

<!-- contents_Start -->
<div id="contents">
    @include('elements.form.basic_infomation')
    @include('elements.arrow_under')
    @include('elements.form.details')
    @include('elements.arrow_under')
    @include('elements.form.bill_other')
    @include('elements.arrow_under')
    @include('elements.form.management')
</div>
<!-- contents_End -->

<div id="itemlist" style="display:none;">{!! nl2br($itemlist) !!}</div>
@endsection