@extends('layout.default')

@section('content')
<!-- Include CSS -->
<link rel="stylesheet" href="{{ asset('path/to/popup.css') }}">

<!-- Include JS -->
<script src="{{ asset('path/to/scripts.js') }}"></script>
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('path/to/i_guide02.jpg') }}" alt="Guide Image">
        <p>こちらのページは請求書編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押すと請求書を作成できます。</p>
    </div>
</div>
<br class="clear">
<div id="contents">
    @include('elements.form.basic_infomation')
    @include('elements.form.arrow_under')
    @include('elements.form.details')
    @include('elements.form.arrow_under')
    @include('elements.form.bill_other')
    @include('elements.form.arrow_under')
    @include('elements.form.management')
</div>
<div id="itemlist" style="display:none;">{{ $customHtml->ht2br($itemlist) }}</div>
@endsection
