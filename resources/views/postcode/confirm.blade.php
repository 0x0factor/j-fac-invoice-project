@extends('layout.default')

@section('content')
@if(session()->has('flash'))
    {{ session('flash') }}
@endif
<br class="clear" />
<!-- header_End -->
<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" /></div>
    <h3><div class="edit_02_edit_postcode"><span class="edit_txt">&nbsp;</span></div></h3>
    <form action="{{ route('update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" />
            <div class="contents_area">
                <a href="{{ route('query', ['sql' => urlencode($sqlRes['sql']), 'backup' => urlencode($backup)]) }}">
                    {{ $sqlRes['count'] }}件のデータを{{ $action }}します。
                </a>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
        </div>
    </form>
    <div class="edit_btn">
    </div>
</div>
<input type="hidden" name="CMP_ID" value="1">
@endsection
