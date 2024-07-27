@extends('layout.default')

@section('content')
<!-- Include CSS -->
@push('style')
<link rel="stylesheet" href="{{ asset('path/to/popup.css') }}">
@endpush

<!-- Include JS -->
@push('scripts')

<script src="{{ asset('path/to/scripts.js') }}"></script>
@endpush
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="Guide Image">
        <p>こちらのページは請求書編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押すと請求書を作成できます。</p>
    </div>
</div>
<br class="clear">

<!-- contents_Start -->
<div id="contents">
    @include('elements.form.basic_information')
    @include('elements.arrow_under')
    @include('elements.form.details')
    @include('elements.arrow_under')
    @include('elements.form.bill_other')
    @include('elements.arrow_under')
    @include('elements.form.management')
</div>
<!-- contents_End -->
<div id="itemlist" style="display:none;">{!! nl2br(e($itemlist)) !!}</div>
@endsection
