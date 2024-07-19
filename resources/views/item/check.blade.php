@extends('layout.default')

@section('content')
<!-- resources/views/items/edit.blade.php -->

@if(session()->has('status'))
    {{ session('status') }}
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは商品確認の画面です。<br>「編集する」ボタンを押すと商品情報を編集することができます。</p>
    </div>
</div>
<br class="clear">
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="edit_01_item"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <form action="{{ route('items.edit', ['id' => $params['Item']['ITM_ID']]) }}" method="POST" class="Item">
        @csrf
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;">商品</th>
                        <td style="width:750px;">
                            {{ $params['Item']['ITEM'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>

                    <tr>
                        <th style="width:130px;">商品名カナ</th>
                        <td style="width:750px;">
                            {{ $params['Item']['ITEM_KANA'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>

                    <tr>
                        <th style="width:130px;">商品コード</th>
                        <td style="width:750px;">
                            {{ $params['Item']['ITEM_CODE'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>

                    <tr>
                        <th style="width:130px;">単位</th>
                        <td style="width:750px;">
                            {{ $params['Item']['UNIT'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>

                    <tr>
                        <th style="width:130px;">価格</th>
                        <td style="width:750px;">
                            {{ $params['Item']['UNIT_PRICE'] }}
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>

                    <tr>
                        <th style="width:130px;">税区分</th>
                        <td style="width:750px;">
                            {{ $excises[$params['Item']['TAX_CLASS']] }}
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>
        <div class="edit_btn">
            @if($editauth)
                <a href="{{ route('items.edit', ['id' => $params['Item']['ITM_ID']]) }}" class="imgover" onclick="event.preventDefault(); document.getElementById('editForm').submit();">
                    <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
                </a>
            @endif

            <form id="editForm" action="{{ route('items.edit', ['id' => $params['Item']['ITM_ID']]) }}" method="POST" style="display:inline;">
                @csrf
                <input type="hidden" name="ITM_ID" value="{{ $params['Item']['ITM_ID'] }}">
            </form>

            <form action="{{ route('items.index') }}" method="POST" style="display:inline;">
                @csrf
                <a href="javascript:move_to_index();" class="imgover">
                    <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
                </a>
            </form>
        </div>
    </form>
</div>
@endsection
