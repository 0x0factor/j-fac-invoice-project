@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは商品登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると商品を作成できます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3><div class="edit_01_item"><span class="edit_txt">&nbsp;</span></div></h3>

    <form action="{{ route('item.add') }}" method="post" class="Item">
        @csrf

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM') ? 'txt_top' : '' }}">
                            商品<img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM" value="{{ old('ITEM') }}" class="w300{{ $errors->has('ITEM') ? ' error' : '' }}" maxlength="80">
                            <br /><span class="usernavi">{{ $usernavi['ITEM'] }}</span>
                            <br /><span class="must">{{ $errors->first('ITEM') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td>
                    </tr>

                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM_KANA') ? 'txt_top' : '' }}">商品名カナ</th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM_KANA" value="{{ old('ITEM_KANA') }}" class="w300{{ $errors->has('ITEM_KANA') ? ' error' : '' }}" maxlength="50">
                            <br /><span class="usernavi">{{ $usernavi['ITEM_KANA'] }}</span>
                            <br /><span class="must">{{ $errors->first('ITEM_KANA') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td>
                    </tr>

                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM_CODE') ? 'txt_top' : '' }}">商品コード</th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM_CODE" value="{{ old('ITEM_CODE') }}" class="w300{{ $errors->has('ITEM_CODE') ? ' error' : '' }}" maxlength="8">
                            <br /><span class="usernavi">{{ $usernavi['ITEM_CODE'] }}</span>
                            <br /><span class="must">{{ $errors->first('ITEM_CODE') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td>
                    </tr>

                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('UNIT') ? 'txt_top' : '' }}">単位</th>
                        <td style="width:750px;">
                            <input type="text" name="UNIT" value="{{ old('UNIT') }}" class="w300{{ $errors->has('UNIT') ? ' error' : '' }}" maxlength="8">
                            <br /><span class="usernavi">{{ $usernavi['ITM_UNIT'] }}</span>
                            <br /><span class="must">{{ $errors->first('UNIT') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td>
                    </tr>

                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('UNIT_PRICE') ? 'txt_top' : '' }}">価格</th>
                        <td style="width:750px;">
                            <input type="text" name="UNIT_PRICE" value="{{ old('UNIT_PRICE') }}" class="w300{{ $errors->has('UNIT_PRICE') ? ' error' : '' }}" maxlength="9">
                            <br /><span class="usernavi">{{ $usernavi['ITM_PRICE'] }}</span>
                            <br /><span class="must">{{ $errors->first('UNIT_PRICE') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Solid"></td>
                    </tr>

                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('TAX_CLASS') ? 'txt_top' : '' }}">税区分</th>
                        <td style="width:750px;">
                            {{-- Assuming $excises is an array of radio options --}}
                            @if(is_array($excises) && !empty($excises))
                                @foreach($excises as $value => $label)
                                    <label>
                                        <input type="radio" name="TAX_CLASS" value="{{ $value }}" {{ old('TAX_CLASS') == $value ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                    @if(!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            @else
                                <p>No excises available.</p>
                            @endif

                            <br /><span class="usernavi">{{ $usernavi['TAX_CLASS'] }}</span>
                            <br /><span class="must">{{ $errors->first('TAX_CLASS') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>

        <div class="edit_btn">
            <input type="image" src="{{ asset('img/bt_save.jpg') }}" name="submit" alt="保存する" class="imgover imgcheck">
            <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" name="cancel" alt="キャンセル" class="imgover imgcheck">
        </div>

        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
        <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
    </form>
</div>
@endsection
