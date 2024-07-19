<link rel="stylesheet" href="{{ asset('path/to/popup.css') }}">

<form id="popupForm" action="{{ route('route_name') }}" method="POST">
    @csrf
    <div id="popup_contents">
        <img src="{{ asset('/img/popup/tl_entry.jpg') }}" style="padding-bottom:10px;">
        <input type="hidden" name="type" value="customer">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="440" cellpadding="0" cellspacing="0" border="0">
                    <tr class="popup_item">
                        <th style="width:130px;">商品@php echo $html->image('i_must.jpg',array('alt'=>'必須','class'=>'pl10')); @endphp</th>
                        <td style="width:310px;"><input type="text" name="ITEM" class="w300" maxlength="60"></td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td></tr>
                    <tr class="popup_item_kana">
                        <th style="width:130px;">商品名カナ</th>
                        <td style="width:310px;"><input type="text" name="ITEM_KANA" class="w300" maxlength="50"></td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td></tr>
                    <tr class="popup_item_code">
                        <th style="width:130px;">商品コード</th>
                        <td style="width:310px;"><input type="text" name="ITEM_CODE" class="w300" maxlength="8"></td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td></tr>
                    <tr class="popup_unit">
                        <th style="width:130px;">単位</th>
                        <td style="width:310px;"><input type="text" name="UNIT" class="w300" maxlength="8"></td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td></tr>
                    <tr class="popup_unitprice">
                        <th style="width:130px;">価格</th>
                        <td style="width:310px;"><input type="text" name="UNIT_PRICE" class="w300" maxlength="8"></td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td></tr>
                    <tr>
                        <th style="width:130px;">税区分</th>
                        <td style="width:310px;">
                            <label><input type="radio" name="TAX_CLASS" value="2" @if($TaxClass == 2) checked @endif> 外税</label>
                            <label><input type="radio" name="TAX_CLASS" value="1" @if($TaxClass == 1) checked @endif> 内税</label>
                            <label><input type="radio" name="TAX_CLASS" value="3" @if($TaxClass == 3) checked @endif> 非課税</label>
                        </td>
                    </tr>
                </table>
                <div class="save_btn">
                    <input type="hidden" name="type" value="item">
                    <button type="submit" onclick="return popupclass.popupinsert('item')">
                        <img src="{{ asset('bt_save2.jpg') }}" alt="Save" class="save-btn">
                    </button>
                    <a href="#" onclick="return popupclass.popup_close();">
                        <img src="{{ asset('bt_cancel_s.jpg') }}" alt="Cancel">
                    </a>
                    @csrf
                    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
                </div>
            </div>
        </div>
    </div>
</form>
