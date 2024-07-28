@php
    $formType = $name; // Assuming $name is defined in your controller or passed to the view

    // Prepare tax options, excluding "0"
    $taxOption = isset($taxClass) && is_array($taxClass) ? $taxClass : [];

    if (isset($taxOption['0'])) {
        unset($taxOption['0']);
    }

    // Prepare tax class for JSON
    $taxClassforJson = [];

    foreach ($taxOption as $key => $name) {
        $taxClassforJson[] = [
            'name' => $name,
            'key' => $key,
        ];
    }

    // Debugging output
    // dd($taxClassforJson);

@endphp

<script type="text/javascript">
    var form = {
        tax_rates: @json($taxRates),
        tax_rates_option: @json($taxClassforJson),
        tax_operation_date: @json($taxOperationDate)
    };
</script>

<h3>
    <div class="edit_02" align="right">
        <span class="show_bt2_on">
            <img src="{{ asset('img/button/hide.png') }}" class="imgover" alt="on"
                onclick="return edit2_toggle('on');">
        </span>
        <span class="show_bt2_off" style="display:none" onClick="return edit2_toggle('off');">
            <img src="{{ asset('img/button/show.png') }}" class="imgover" alt="off">
        </span>
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Top">
    <div class="contents_area2">
        <span class="usernavi">{{ $usernavi['ITEM_LIST'] }}</span>
        <table id="detail_table" width="920px" cellpadding="0" cellspacing="0" border="0">
            <tr>
                @if (isset($error['ITEM']['FLAG']) && $error['ITEM']['FLAG'] != 0)

                    @for ($i = 0; $i < $dataline; $i++)
                        @isset($error['ITEM']['NO'][$i])
                            <td></td>
                            <td colspan=7>
                                <font color='red'>商品名の{{ ++$error['ITEM']['NO'][$i] }}行目にエラーがあります</font>
                            </td>
                </tr>
                <tr>
                @endisset
                @endfor

                @endif
                @if (isset($error['ITEM_NO']['FLAG']) && $error['ITEM_NO']['FLAG'] != 0)

                    @for ($i = 0; $i < $dataline; $i++)
                        @if (isset($error['ITEM_NO']['NO'][$i]))
                            <td></td>
                            <td colspan="7">
                                <font color='red'>NOの{{ $error['ITEM_NO']['NO'][$i] }}行目にエラーがあります</font>
                            </td>
            </tr>
            <tr>
                @endif
                @endfor

                @endif

                <!-- Repeat for other error checks -->
            </tr>
            <tr>
                <td class="w24">&nbsp;</td>
                <th class="w39">No.</th>
                <th class="w70">商品コード</th>
                <th class="w167">品目名</th>
                <th class="w74">数量</th>
                <th class="w54">単位</th>
                <th class="w94">単価</th>
                <th class="w114">金額</th>
                <th class="w260">行属性 / 税区分</th>
                <td class="w24">&nbsp;</td>
            </tr>
            @for ($i = 0; $i < $dataline; $i++)
                <tr class="row_{{ $i }}">
                    <td>
                        <img src="{{ asset('img/bt_delete.jpg') }}" alt="×" url="#" class="delbtn"
                            onclick="return form.f_delline({{ $i }});">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[ITEM_NO][]" maxlength="2"
                            class="w31 @if (isset($error['ITEM_NO']['NO'][$i])) error @endif">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[ITEM_CODE][]" maxlength="8"
                            class="w64 @if (isset($error['ITEM_CODE']['NO'][$i])) error @endif">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[ITEM][]" maxlength="80"
                            class="w120 @if (isset($error['ITEM']['NO'][$i])) error @endif">
                        <span id="INSERT_ITEM_IMG{{ $i }}">
                            <img src="{{ asset('img/bt_select3.jpg') }}" style="margin: 0px 0px 2px" alt="商品選択"
                                url="#"
                                onclick="form.focusline = {{ $i }}; focusLine(); return popupclass.popupajax('select_item');">
                        </span>
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[QUANTITY][]" maxlength="7"
                            onkeyup="recalculation('{{ $formType }}')"
                            class="w63 @if (isset($error['QUANTITY']['NO'][$i])) error @endif">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[UNIT][]" maxlength="8"
                            class="w45 @if (isset($error['UNIT']['NO'][$i])) error @endif">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[UNIT_PRICE][]" maxlength="9"
                            onkeyup="recalculation('{{ $formType }}')"
                            class="w73 @if (isset($error['UNIT_PRICE']['NO'][$i])) error @endif">
                    </td>
                    <td>
                        <input type="text" name="{{ $formType }}item[AMOUNT][]" class="w103"
                            readonly="readonly" onchange="recalculation('{{ $formType }}')">
                    </td>
                    <td>
                        <select name="{{ $formType }}item[LINE_ATTRIBUTE][]" class="w103"
                            onchange="changeAttribute('{{ $formType }}', {{ $i }}, value);">
                            @if (is_array($lineAttribute))
                                @foreach ($lineAttribute as $key => $value)
                                    <option value="{{ $key }}"
                                        @if ($key == old($formType . 'item[LINE_ATTRIBUTE][]')) selected @endif>{{ $value }}</option>
                                @endforeach
                            @else
                                <!-- Optionally handle the case where $lineAttribute is not an array -->
                                <option value="">No options available</option>
                            @endif

                        </select>
                        <select name="{{ $formType }}item[TAX_CLASS][]" class="w105"
                            onchange="changeTaxClass('{{ $formType }}', {{ $i }}, value);">
                            @if (is_array($taxClass))
                                @foreach ($taxClass as $key => $value)
                                    <option value="{{ $key }}"
                                        @if ($key == old($formType . 'item[TAX_CLASS][]')) selected @endif>{{ $value }}</option>
                                @endforeach
                            @else
                                <!-- Optionally handle the case where $taxClass is not an array -->
                                <option value="">No options available</option>
                            @endif

                        </select>
                        <input type="hidden" name="{{ $formType }}item[DISCOUNT][]">
                        <input type="hidden" name="{{ $formType }}item[DISCOUNT_TYPE][]">
                    </td>
                    <td>
                        <img src="{{ asset('img/bt_up.jpg') }}" class="btn_up" alt="×" url="javascript:void(0);"
                            onclick="form.focusline={{ $i }}; form.f_up();">
                        <img src="{{ asset('img/bt_down.jpg') }}" class="btn_down" alt="×"
                            url="javascript:void(0);" onclick="form.focusline={{ $i }}; form.f_down();">
                    </td>
                </tr>
            @endfor
            <tr>
                <td colspan="8" class="pl30">
                    <img src="{{ asset('img/bt_add.jpg') }}" alt="行を追加する" url="javascript:void(0)"
                        onclick="return form.f_addline(null);">
                    <img src="{{ asset('img/button/insert.png') }}" alt="行を挿入する" url="javascript:void(0)"
                        onclick="form.f_insert(); return false;">
                    <img src="{{ asset('img/bt_break.png') }}" alt="改ページを挿入する" url="javascript:void(0)"
                        onclick="form.f_insert(8); return false;">
                    <span onclick="form.f_up()" style="background-color: #CCCCCC">
                        <img src="{{ asset('img/button/up.png') }}" class="imgover" alt="off">
                    </span>
                    <span onclick="form.f_down()" style="background-color: #CCCCCC">
                        <img src="{{ asset('img/button/down.png') }}" class="imgover" alt="on">
                    </span>
                    <br>
                    <span class="usernavi"> {{ $usernavi['MOVE_LINE'] }} </span>
                    <span class="usernavi"> {{ $usernavi['ADD_LINE'] }} </span>
                </td>
                <td colspan="2">
                    <img src="{{ asset('img/bt_clear.png') }}" alt="リセット" url="#"
                        onclick="return form.f_reset('null');" class="float_r">
                </td>
            </tr>
        </table>
    </div>

    <div class="contents_area3">
        <div align="left">
            <br>
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </div>
</div>
