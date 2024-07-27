@extends('layout.default')

@section('content')

@push('scripts')
    <script src="{{ asset('path/to/your/script.js') }}"></script>
    <script>
        // Inline script if necessary
    </script>
@endpush
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="Guide">
        <p>こちらのページは見積書編集の画面です。<br>必要な情報を入力の上「保存する」ボタンを押下すると見積書を作成できます。</p>
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

<div id="itemlist" style="display:none;">
    {!! nl2br(e($itemlist)) !!}
</div>
@endsection
