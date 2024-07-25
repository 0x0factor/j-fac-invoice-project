@extends('layout.default')

@section('content')
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは郵便番号の管理画面です。<br>
        <a href="http://www.post.japanpost.jp/zipcode/dl/kogaki-zip.html" target="_blank">日本郵便のサイト</a>から、「全国一括」をダウンロードし、KEN_ALL.CSVをアップロードしてください。
        </p>
    </div>
</div>
<br class="clear">

<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
    <h3><div class="edit_02_edit_postcode"><span class="edit_txt">&nbsp;</span></div></h3>

    <form action="{{ route('zipcode.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:200px;">郵便番号件数</th>
                        <td>{{ $count }}件</td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                    <tr>
                        <th style="width:200px;">CSVデータアップロード</th>
                        <td>
                            <input type="file" name="Post[Csv]">
                            <button type="submit" onclick="this.style.display='none'">アップロード</button>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                    <tr>
                        <th style="width:200px;">郵便番号の修復</th>
                        <td>
                            更新に失敗した場合は、
                            <a href="{{ route('zipcode.reset') }}">こちら</a>をクリックすると郵便番号を初期状態に戻すことができます。
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
        </div>
    </form>
    <div class="edit_btn"></div>
</div>
<input type="hidden" name="CMP_ID" value="1">
@endsection
