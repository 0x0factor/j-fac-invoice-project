@extends('layout.default')

@section('content')
    @if (session()->has('status'))
        {{ session('status') }}
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
            <p>こちらのページは請求書確認の画面です。<br />「編集する」ボタンを押すと請求書を編集することができます。</p>
        </div>
    </div>
    <br class="clear" />

    <!-- contents_Start -->
    <div id="contents">
        @include('elements.arrow_under')
        @include('elements.form.check_buttons')
        @include('elements.form.check_basic_information')
        @include('elements.arrow_under')
        @include('elements.form.check_detail')
        @include('elements.arrow_under')

        <h3>
            <div class="edit_03"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th class="w100">発行ステータス</th>
                        <td class="w770">{{ $status[$customHtml->ht2br($param['Bill']['STATUS'], 'Bill', 'STATUS')] }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <th class="w100">振込手数料</th>
                        <td class="w770">{{ $customHtml->ht2br($param['Bill']['FEE'], 'Bill', 'FEE') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <th class="w100">振込期限</th>
                        <td class="w770">{{ $customHtml->ht2br($param['Bill']['DUE_DATE'], 'Bill', 'DUE_DATE') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <th class="txt_top w100">備考</th>
                        <td class="w770">{{ $customHtml->ht2br($param['Bill']['NOTE'], 'Bill', 'NOTE') }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <th class="w100">メモ</th>
                        <td class="w770">{{ $customHtml->ht2br($param['Bill']['MEMO'], 'Bill', 'MEMO') }}</td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>
        @include('elements.arrow_under')
        {{-- @include('elements.form.check_management') --}}

        @include('elements.form.check_buttons')
    </div>
@endsection
