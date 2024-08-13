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
    // var form = {
    //     tax_rates: @json($taxRates),
    //     tax_rates_option: @json($taxClassforJson),
    //     tax_operation_date: @json($taxOperationDate)
    // };
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
                            class="w120 hoverLine @if (isset($error['ITEM']['NO'][$i])) error @endif">
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
                        onclick="form.f_addline(null);">
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
            <br />
            <span class="w180">&nbsp;</span>
            <span class="show_btdetail_on" style="{{ !isset($error['DISCOUNT']) || !$error['DISCOUNT'] ? 'display:none' : '' }}">
                <img src="{{ asset('img/button/d_up.png') }}" class="imgover" alt="on" onclick="return detail_toggle('on');" />
                金額詳細設定を非表示にする
            </span>
            <span class="show_btdetail_off" onclick="return detail_toggle('off');" style="{{ isset($error['DISCOUNT']) && $error['DISCOUNT'] ? 'display:none' : '' }}">
                <img src="{{ asset('img/button/d_down.png') }}" class="imgover" alt="off" />
                金額詳細設定を表示する
            </span>
        </div>

        <div id="detail" style="{{ !isset($error['DISCOUNT']) && !old('TAX_FRACTION_TIMING') ? 'display:none' : '' }}">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td colspan="8">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" class="pb5" />
                    </td>
                </tr>
                <tr>
                    <th class="txt_top w100">割引設定</th>
                    <td colspan="3" style="width:780px;">
                        <input type="text" name="DISCOUNT" maxlength="15" class="w140 {{ isset($error['DISCOUNT']) && $error['DISCOUNT'] >= 1 ? 'error' : '' }} {{ $errors->has('DISCOUNT') ? 'error' : '' }}" value="{{ old('DISCOUNT') }}" />

                        @foreach($discount ?? [] as $value => $label)
                            <label class="ml10 mr5 txt_mid">
                                <input type="radio" name="DISCOUNT_TYPE" value="{{ $value }}" {{ old('DISCOUNT_TYPE') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                        <br />
                        <span class="must">{{ isset($error['DISCOUNT']) && $error['DISCOUNT'] == 1 ? '割引が長すぎます' : '' }}</span>
                        <span class="must">{{ isset($error['DISCOUNT']) && $error['DISCOUNT'] == 3 ? '複数の消費税区分が設定されている場合は、割引設定は利用できません。' : $errors->first('DISCOUNT') }}</span>
                        <br /><br />
                        <span class="usernavi">{{ $usernavi['DISCOUNT'] }}</span>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" />
                    </td>
                </tr>
                <tr>
                    <th class="w100">数量小数表示</th>
                    <td colspan="3" style="width:780px;">

                        @foreach($decimal ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="DECIMAL_QUANTITY" value="{{ $value }}" {{ old('DECIMAL_QUANTITY') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach

                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" />
                    </td>
                </tr>
                <tr>
                    <th class="w100">単価小数表示</th>
                    <td colspan="3" style="width:780px;">
                        @foreach($decimal ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="DECIMAL_UNITPRICE" value="{{ $value }}" {{ old('DECIMAL_UNITPRICE') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" />
                    </td>
                </tr>
                <tr>
                    <th class="w100">消費税設定</th>
                    <td id="EXCISE" class="w240">
                        @foreach($excises ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="EXCISE" value="{{ $value }}" {{ old('EXCISE') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                    </td>
                    <th class="w100">消費税端数処理</th>
                    <td id="TAX_FRACTION" class="w440">
                        @foreach($fractions ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="TAX_FRACTION" value="{{ $value }}" {{ old('TAX_FRACTION') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" />
                    </td>
                </tr>
                <tr>
                    <th class="w100">消費税端数計算</th>
                    <td id="TAX_FRACTION_TIMING" class='w240 {{ $errors->has('TAX_FRACTION_TIMING') ? 'error' : '' }}'>
                        @foreach($tax_fraction_timing ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="TAX_FRACTION_TIMING" value="{{ $value }}" {{ old('TAX_FRACTION_TIMING') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                        @if($errors->has('TAX_FRACTION_TIMING'))
                            <span class="must">{{ $errors->first('TAX_FRACTION_TIMING') }}</span>
                        @endif
                        <br />
                        <span class="usernavi">※発行日を2023年10月01日以降に設定した場合、消費税端数計算は法律により自動的に「帳票単位」に設定されます</span>
                    </td>
                    <th class="w100">基本端数処理</th>
                    <td id="FRACTION" class="w440">
                        @foreach($fractions ?? [] as $value => $label)
                            <label class="ml20 mr5 txt_mid">
                                <input type="radio" name="FRACTION" value="{{ $value }}" {{ old('FRACTION') == $value ? 'checked' : '' }} />
                                {{ $label }}
                            </label>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>

        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td colspan="4" class="line">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" />
                </td>
            </tr>
            <tr>
                <td class="pt10">
                    <img src="{{ asset('img/button/i_subtotal.jpg') }}" alt="小計" />
                </td>
                <td class="pt10">
                    <input type="text" name="SUBTOTAL" class="w200" readonly value="{{ old('SUBTOTAL') }}" />
                </td>
                <td class="pt10">
                    <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税" />
                </td>
                <td class="pt10">
                    <input type="text" name="SALES_TAX" class="w200" readonly value="{{ old('SALES_TAX') }}" />
                </td>
            </tr>
            <tr>
                <td class="pt10">
                    <img src="{{ asset('img/i_total.jpg') }}" alt="合計" />
                </td>
                <td colspan="3" class="pt10">
                    <input type="text" name="TOTAL" class="w200" readonly value="{{ old('TOTAL') }}" />
                </td>
            </tr>
        </table>

        <table width="880" cellpadding="0" cellspacing="0" border="0" id="every_tax_table">
            <tr>
                <td colspan="8">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" class="pb5" />
                </td>
            </tr>
            <tr id="ten_rate_tax">
                <td class="pt10">
                    <img src="{{ asset('img/button/i_10_tax.jpg') }}" alt="10%消費税" />
                </td>
                <td class="pt10">
                    <input type="text" name="TEN_TAX" class="w200" readonly value="{{ old('TEN_TAX') }}" />
                </td>
                <td class="pt10">
                    <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税" />
                </td>
                <td class="pt10">
                    <input type="text" name="TEN_TAX_AMOUNT" class="w200" readonly value="{{ old('TEN_TAX_AMOUNT') }}" />
                </td>
            </tr>
            <tr id="eight_rate_tax">
                <td class="pt10">
                    <img src="{{ asset('img/button/i_8_tax.jpg') }}" alt="8%消費税" />
                </td>
                <td class="pt10">
                    <input type="text" name="EIGHT_TAX" class="w200" readonly value="{{ old('EIGHT_TAX') }}" />
                </td>
                <td class="pt10">
                    <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税" />
                </td>
                <td class="pt10">
                    <input type="text" name="EIGHT_TAX_AMOUNT" class="w200" readonly value="{{ old('EIGHT_TAX_AMOUNT') }}" />
                </td>
            </tr>
        </table>
    </div>
</div>
