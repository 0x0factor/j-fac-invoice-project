@extends('layout.default')

@section('content')
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}">
        <p>こちらのページは商品登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると商品の変更を保存できます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}"></div>

    <h3><div class="edit_01_item"><span class="edit_txt">&nbsp;</span></div></h3>
    <form method="POST" action="{{ route('item.store') }}" class="Item">
        @csrf
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM') ? 'txt_top' : '' }}">商品
                            <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                        </th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM" value="{{ old('ITEM') }}" class="w300{{ $errors->has('ITEM') ? ' error' : '' }}" maxlength="80">
                            <br><span class="usernavi">{{ $usernavi['ITEM'] }}</span>
                            <br><span class="must">{{ $errors->first('ITEM') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM_KANA') ? 'txt_top' : '' }}">商品名カナ</th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM_KANA" value="{{ old('ITEM_KANA') }}" class="w300{{ $errors->has('ITEM_KANA') ? ' error' : '' }}" maxlength="50">
                            <br><span class="usernavi">{{ $usernavi['ITEM_KANA'] }}</span>
                            <br><span class="must">{{ $errors->first('ITEM_KANA') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ITEM_CODE') ? 'txt_top' : '' }}">商品コード</th>
                        <td style="width:750px;">
                            <input type="text" name="ITEM_CODE" value="{{ old('ITEM_CODE') }}" class="w300{{ $errors->has('ITEM_CODE') ? ' error' : '' }}" maxlength="8">
                            <br><span class="usernavi">{{ $usernavi['ITEM_CODE'] }}</span>
                            <br><span class="must">{{ $errors->first('ITEM_CODE') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('UNIT') ? 'txt_top' : '' }}">単位</th>
                        <td style="width:750px;">
                            <input type="text" name="UNIT" value="{{ old('UNIT') }}" class="w300{{ $errors->has('UNIT') ? ' error' : '' }}" maxlength="8">
                            <br><span class="usernavi">{{ $usernavi['ITM_UNIT'] }}</span>
                            <br><span class="must">{{ $errors->first('UNIT') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('UNIT_PRICE') ? 'txt_top' : '' }}">価格</th>
                        <td style="width:750px;">
                            <input type="text" name="UNIT_PRICE" value="{{ old('UNIT_PRICE') }}" class="w300{{ $errors->has('UNIT_PRICE') ? ' error' : '' }}" maxlength="9">
                            <br><span class="usernavi">{{ $usernavi['ITM_PRICE'] }}</span>
                            <br><span class="must">{{ $errors->first('UNIT_PRICE') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('TAX_CLASS') ? 'txt_top' : '' }}">税区分</th>
                        <td style="width:750px;">
                            {{-- Replace with your actual radio button implementation --}}
                            {{-- Example: --}}
                            <input type="radio" name="TAX_CLASS" value="1"> Option 1
                            <input type="radio" name="TAX_CLASS" value="2"> Option 2
                            <br><span class="usernavi">{{ $usernavi['TAX_CLASS'] }}</span>
                            <br><span class="must">{{ $errors->first('TAX_CLASS') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>
        <div class="edit_btn">
            <input type="submit" name="submit" value="保存する" class="imgover imgcheck">
            <input type="submit" name="cancel" value="キャンセル" class="imgover imgcheck">
        </div>
    </form>
</div>
{{-- Replace with actual hidden token generation --}}
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
<input type="hidden" name="ITM_ID">
@endsection
