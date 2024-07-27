@extends('layout.default')

@section('content')
@push('scripts')
@include('elements.form.scripts')
@endpush

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" />
        <p>こちらのページは納品書作成の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると納品書を作成できます。</p>
    </div>
</div>
<br class="clear" />

<!-- contents_Start -->
<div id="contents">
@include('elements.form.basic_information')
@include('elements.arrow_under')
@include('elements.form.details')
@include('elements.arrow_under')
@include('elements.form.delivery_other')
@include('elements.arrow_under')
@include('elements.form.management')
</div>
<!-- contents_End -->


<div id="itemlist" style="display:none;">{!! nl2br(e($itemlist)) !!}</div>
@endsection
