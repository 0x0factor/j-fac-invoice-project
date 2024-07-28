@extends('layout.default')
@section('scripts')
    @include('elements.form.scripts')
@endsection
@section('content')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif


    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
            <p>こちらのページは見積書編集の画面です。<br>必要な情報を入力の上「保存する」ボタンを押下すると見積書の変更を保存できます。</p>
        </div>
    </div>
    <br class="clear">

    <!-- contents_Start -->
    <div id="contents">
        @include('elements.form.basic_information')
        @include('elements.arrow_under')
        @include('elements.form.details')
        @include('elements.arrow_under')
        @include('elements.form.quote_other')
        @include('elements.arrow_under')
        @include('elements.form.management')
    </div>
    <!-- contents_End -->

    <div id="itemlist" style="display:none;">{!! nl2br(e($itemlist)) !!}</div>
@endsection
