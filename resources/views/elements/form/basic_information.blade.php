@php
    $formType = $formType ?? 'Quote';
    $controller = strtolower($formType);
    $action = request()->route()->getActionMethod();
@endphp

<form method="POST" action="{{ route($controller . '.add') }}" class="{{ $formType }}">
    @csrf

    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>
    <div>
        <img src="{{ asset('img/document/i_flow.jpg') }}" alt="作成の流れ">
    </div>
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>

    <h3>
        <div class="edit_01" align="right">
            <span class="edit_txt">&nbsp;</span>
            <span class="show_bt1_on">
                <img src="{{ asset('img/button/hide.png') }}" class="imgover" alt="on"
                    onclick="return edit1_toggle('on');">
            </span>
            <span class="show_bt1_off" style="display:none" onclick="return edit1_toggle('off');">
                <img src="{{ asset('img/button/show.png') }}" class="imgover" alt="off">
            </span>
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th class="{{ $errors->has('NO') ? 'txt_top' : '' }}">管理番号</th>
                    <td width="320">
                        <input type="text" name="NO" class="w180 p2{{ $errors->has('NO') ? ' error' : '' }}"
                            maxlength="20" value="{{ old('NO') }}">
                        <br><span class="usernavi">{{ $usernavi['NO'] }}</span>
                        <br><span class="must">{{ $errors->first('NO') }}</span>
                    </td>
                    <th class="{{ $errors->has('DATE') ? 'txt_top' : '' }}">
                        <span class="float_l">発行日</span>
                        <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                    </th>
                    <td width="320">
                        <input type="text" name="DATE"
                            class="w100 p2 date cal{{ $errors->has('DATE') ? ' error' : '' }}" readonly
                            value="{{ old('DATE') }}">
                        <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                        <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                            onclick="return cal1.write();">
                        <div id="calid"></div>
                    </td>
                </tr>

                <tr>
                    <td colspan="3"></td>
                    <td>
                        <span class="usernavi">{{ $usernavi['DATE'] }}</span>
                        <br><span class="must">{{ $errors->first('ISSUE_DATE') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                </tr>

                <tr>
                    <th class="{{ $errors->has('SUBJECT') ? 'txt_top' : '' }}">
                        <span class="float_l">件名</span>
                        <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                    </th>
                    <td colspan="3">
                        <input type="text" name="SUBJECT"
                            class="w320 mr10{{ $errors->has('SUBJECT') ? ' error' : '' }}" maxlength="80"
                            onkeyup="count_strw('subject_rest', value, 40)" value="{{ old('SUBJECT') }}">
                        <span id="subject_rest"></span>
                        <br><span class="usernavi">{{ $usernavi['SUBJECT'] }}</span>
                        <br><span class="must">{{ $errors->first('SUBJECT') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                    </td>
                </tr>

                <tr>
                    <th style="width:170px;" class="{{ $errors->has('CST_ID') ? 'txt_top' : '' }}">
                        <span class="float_l">顧客名</span>
                        <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                    </th>
                    <td id="SETCUSTOMER" style="width:270px;">
                        <input type="text" name="CUSTOMER_NAME"
                            class="w130{{ $errors->has('CST_ID') ? ' error' : '' }}"
                            value="{{ old('CUSTOMER_NAME') }}" readonly>
                        <input type="hidden" name="CST_ID" value="{{ old('CST_ID') }}"
                            id="{{ $formType }}CSTID">
                        <a href="#" onclick="return popupclass.popupajax('select_customer');">
                            <img src="{{ asset('img/bt_select2.jpg') }}" alt="">
                        </a>
                        <a href="#" onclick="return customer_reset();">
                            <img src="{{ asset('img/bt_delete2.jpg') }}" alt="">
                        </a>
                        <br><span class="usernavi">{{ $usernavi['CST_ID'] }}</span>
                        <br><span class="must">{{ $errors->first('CST_ID') }}</span>
                    </td>
                    <th style="width:120px;" class="{{ $errors->has('NO') ? 'txt_top' : '' }}">顧客担当者名</th>
                    <td style="width:270px;" id="SETCUSTOMERCHARGE">
                        <input type="text" name="CUSTOMER_CHARGE_NAME"
                            class="w120 p2{{ $errors->has('CHRC_ID') ? ' error' : '' }}" maxlength="30"
                            value="{{ old('CUSTOMER_CHARGE_NAME') }}" readonly>
                        <input type="hidden" name="CHRC_ID" value="{{ old('CHRC_ID') }}">
                        <a href="#" onclick="return popupclass.popupajax('customer_charge');">
                            <img src="{{ asset('img/bt_select2.jpg') }}" alt="">
                        </a>
                        <a href="#" onclick="return cstchr_reset();">
                            <img src="{{ asset('img/bt_delete2.jpg') }}" alt="">
                        </a>
                        <br><span class="must">{{ $errors->first('CHRC_ID') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                </tr>

                <tr>
                    <th style="width:170px;" class="{{ $errors->has('NO') ? 'txt_top' : '' }}">自社担当者名</th>
                    <td style="width:270px;" id="SETCHARGE" colspan="3">
                        <input type="text" name="CHARGE_NAME"
                            class="w120 p2{{ $errors->has('CHR_ID') ? ' error' : '' }}" maxlength="30" readonly
                            value="{{ old('CHARGE_NAME') }}">
                        <input type="hidden" name="CHR_ID" value="{{ old('CHR_ID') }}">
                        <a href="#" onclick="return popupclass.popupajax('charge');">
                            <img src="{{ asset('img/bt_select2.jpg') }}" alt="">
                        </a>
                        <a href="#" onclick="return chr_reset();">
                            <img src="{{ asset('img/bt_delete2.jpg') }}" alt="">
                        </a>
                        <br><span class="must">{{ $errors->first('CHR_ID') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                </tr>

                <tr>
                    <th class="{{ $errors->has('HONOR_TITLE') ? 'txt_top' : '' }}">
                        <span class="float_l">敬称</span>
                    </th>
                    <td id="HONOR" colspan="3">

                        @isset($honor)
                            @foreach ($honor as $key => $value)
                                <input type="radio" name="HONOR_CODE" value="{{ $key }}"
                                    class="ml20 mr5 txt_mid" {{ old('HONOR_CODE') == $key ? 'checked' : '' }}>
                                {{ $value }}
                            @endforeach
                        @else
                            <p>No honor data available.</p>
                        @endisset

                        <input type="text" name="HONOR_TITLE"
                            class="w160 mr10{{ $errors->has('HONOR_TITLE') ? ' error' : '' }}" maxlength="8"
                            value="{{ old('HONOR_TITLE') }}">
                        <br><span class="usernavi">{{ $usernavi['HONOR'] }}</span>
                        <br><span class="must">{{ $errors->first('HONOR_TITLE') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                </tr>

                <tr>
                    <th class="{{ $errors->has('CMP_SEAL_FLG') ? 'txt_top' : '' }}">
                        <span class="float_l">自社印押印設定</span>
                    </th>
                    <td id="SET_CMP_SEAL_FLG" colspan="3">

                        @if (is_array($seal_flg) || is_object($seal_flg))
                            @foreach ($seal_flg as $key => $value)
                                <input type="radio" name="CMP_SEAL_FLG" value="{{ $key }}"
                                    class="ml20 mr5 txt_mid" {{ old('CMP_SEAL_FLG') == $key ? 'checked' : '' }}>
                                {{ $value }}
                            @endforeach
                        @else
                            <p>No seal flag data available.</p>
                        @endif

                        <br><span class="usernavi">{{ $usernavi['SEAL_FLG'] }}</span>
                        <br><span class="must">{{ $errors->first('CMP_SEAL_FLG') }}</span>
                    </td>
                </tr>

                <tr>
                    <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                </tr>

                <tr>
                    <th class="{{ $errors->has('CHR_SEAL_FLG') ? 'txt_top' : '' }}">
                        <span class="float_l">担当者印押印設定</span>
                    </th>
                    <td id="SET_CHR_SEAL_FLG" colspan="3">
                        @if (is_array($seal_flg) || is_object($seal_flg))
                            @foreach ($seal_flg as $key => $value)
                                <input type="radio" name="CHR_SEAL_FLG" value="{{ $key }}"
                                    class="ml20 mr5 txt_mid" {{ old('CHR_SEAL_FLG') == $key ? 'checked' : '' }}>
                                {{ $value }}
                            @endforeach
                        @else
                            <p>No seal flag data available.</p>
                        @endif
                        <br><span class="usernavi">{{ $usernavi['SEAL_FLG'] }}</span>
                        <br><span class="must">{{ $errors->first('CHR_SEAL_FLG') }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var lastDate = '';
        setInterval(function() {
            var date = document.querySelector('input.cal.date').value;
            if (lastDate !== date) {
                lastDate = date;
                var calcDate = new Date(date);
                if (calcDate.getFullYear() >= 2024 || (calcDate.getFullYear() >= 2023 && calcDate
                        .getMonth() >= 9)) {
                    document.getElementById('{{ $formType }}TAXFRACTIONTIMING1').setAttribute(
                        'disabled', true);
                    document.getElementById('{{ $formType }}TAXFRACTIONTIMING0').click();
                } else {
                    document.getElementById('{{ $formType }}TAXFRACTIONTIMING1').removeAttribute(
                        'disabled');
                }
            }
        }, 1000);
    });
</script>
