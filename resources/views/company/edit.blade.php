@extends('layout.default')

@section('scripts')
    <script type="text/javascript">
        function show_serial(val) {
            if (val == 0) {
                $('#serial_option').slideDown();
            } else if (val == 1) {
                $('#serial_option').slideUp();
            }
        }

        function update_serial(no) {
            $('#SERIAL' + no + 'CHANGED').val(1);
        }

        function change_sample(no) {
            var str = "";

            if ($('#SERIAL' + no + 'NUMBERINGFORMAT').val() == 0) {
                str += $('#SERIAL' + no + 'PREFIX').val();
                if ($('#SERIAL' + no + 'NEXT').val().length < 6) {
                    str += ('00000' + $('#SERIAL' + no + 'NEXT').val()).slice(-5);
                } else {
                    str += $('#SERIAL' + no + 'NEXT').val();
                }
                $('#sample' + no).html(str);
            } else {
                str += $('#SERIAL' + no + 'PREFIX').val();
                str += '{{ date('ymd') }}';
                if ($('#SERIAL' + no + 'NEXT').val().length < 2) {
                    str += ('00000' + $('#SERIAL' + no + 'NEXT').val()).slice(-2);
                } else {
                    str += $('#SERIAL' + no + 'NEXT').val();
                }
                $('#sample' + no).html(str);
            }
        }

        function format_change(val, no) {
            if (val == 1) {
                $('.NF' + no).fadeOut();
                $('#SERIAL' + no + 'NEXT').val(1);
            } else {
                $('.NF' + no).fadeIn();
            }
        }

        $(document).ready(function() {
            if ({{ $data['Company']['SERIAL_NUMBER'] ?? 0 }}) {
                $('#serial_option').hide();
            }
            @for ($i = 0; $i < 5; $i++)
                @if ($data['SERIAL'][$i]['NUMBERING_FORMAT'])
                    $(".NF{{ $i }}").fadeOut();
                @endif
            @endfor
        });
    </script>
@endsection

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp

    @if (session('flash_message'))
        <div class="flash-message">
            {{ session('flash_message') }}
        </div>
    @endif
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" />
            <p>こちらのページは自社情報設定の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると自社情報の変更を保存できます。</p>
        </div>
    </div>
    <br class="clear" />
    <!-- header_End -->

    <!-- contents_Start -->

    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>
        <h3><div class="company_01"><span class="edit_txt">&nbsp;</span></div></h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Content Top">
            <div class="contents_area">
                <form action="{{ url('companys') }}" method="POST" enctype="multipart/form-data" class="Company">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:130px;" class="{{ $errors->has('NAME') ? 'txt_top' : '' }}">
                                <span class="float_l">自社名</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="NAME" value="{{ old('NAME') }}" class="w300{{ $errors->has('NAME') ? ' error' : '' }}" maxlength="60">
                                <br><span class="usernavi">{{ $usernavi['CMP_NAME'] }}</span>
                                <br><span class="must">{{ $errors->first('NAME') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $errors->has('REPRESENTATIVE') ? 'txt_top' : '' }}">代表者名</th>
                            <td>
                                <input type="text" name="REPRESENTATIVE" value="{{ old('REPRESENTATIVE') }}" class="w300{{ $errors->has('REPRESENTATIVE') ? ' error' : '' }}" maxlength="60">
                                <br><span class="usernavi">{{ $usernavi['REPRESENTATIVE'] }}</span>
                                <br><span class="must">{{ $errors->first('REPRESENTATIVE') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th style="width:130px;" class="{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}">
                                <span class="float_l">郵便番号</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="POSTCODE1" value="{{ old('POSTCODE1') }}" class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? ' error' : '' }}" maxlength="3">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="POSTCODE2" value="{{ old('POSTCODE2') }}" class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? ' error' : '' }}" maxlength="4">
                                <div id="target"></div>
                                <br><span class="usernavi">{{ $usernavi['POSTCODE'] }}</span>
                                <br><span class="must">{{ $errors->first('POSTCODE1') ?? $errors->first('POSTCODE2') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th style="width:130px;">
                                <span class="float_l">都道府県</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <select name="CNT_ID" class="{{ $errors->has('CNT_ID') ? ' error' : '' }}">
                                    @foreach($countys as $key => $value)
                                        <option value="{{ $key }}"{{ old('CNT_ID') == $key ? ' selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                <br><span class="must">{{ $errors->first('CNT_ID') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th style="width:130px;" class="{{ $errors->has('ADDRESS') ? 'txt_top' : '' }}">
                                <span class="float_l">住所</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="ADDRESS" value="{{ old('ADDRESS') }}" class="w600{{ $errors->has('ADDRESS') ? ' error' : '' }}" maxlength="100">
                                <br><span class="usernavi">{{ $usernavi['ADDRESS'] }}</span>
                                <br><span class="must">{{ $errors->first('ADDRESS') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $errors->has('BUILDING') ? 'txt_top' : '' }}">建物名</th>
                            <td>
                                <input type="text" name="BUILDING" value="{{ old('BUILDING') }}" class="w600{{ $errors->has('BUILDING') ? ' error' : '' }}" maxlength="100">
                                <br><span class="usernavi">{{ $usernavi['BUILDING'] }}</span>
                                <br><span class="must">{{ $errors->first('BUILDING') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th style="width:130px;" class="{{ $perror == 1 ? 'txt_top' : '' }}">
                                <span class="float_l">電話番号</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="PHONE_NO1" value="{{ old('PHONE_NO1') }}" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="5">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO2" value="{{ old('PHONE_NO2') }}" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO3" value="{{ old('PHONE_NO3') }}" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4">
                                <br><span class="usernavi">{{ $usernavi['PHONE'] }}</span>
                                <br><span class="must">{{ $perror == 1 ? '正しい電話番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $ferror == 1 ? 'txt_top' : '' }}">FAX番号</th>
                            <td>
                                <input type="text" name="FAX_NO1" class="w60 error" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO2" class="w60 error" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO3" class="w60 error" maxlength="4">
                                <br><span class="usernavi">{{ $usernavi['FAX'] }}</span>
                                <br><span class="must">{{ $ferror == 1 ? '正しいFAX番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $errors->has('INVOICE_NUMBER') ? 'txt_top' : '' }}">登録番号</th>
                            <td>
                                <input type="text" name="INVOICE_NUMBER" class="w300 error" maxlength="14">
                                <br><span class="usernavi">{{ $usernavi['INVOICE_NUMBER'] }}</span>
                                <br><span class="must">{{ $errors->first('INVOICE_NUMBER') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $errors->has('HONOR_TITLE') ? 'txt_top' : '' }}">
                                <span class="float_l">敬称</span>
                            </th>
                            <td id="HONOR" colspan="3">
                                @foreach($honor as $key => $value)
                                    <label><input type="radio" name="HONOR_CODE" value="{{ $key }}" class="ml20 mr5 txt_mid" {{ old('HONOR_CODE') == $key ? ' selected' : '' }}> {{ $value }}</label>
                                @endforeach
                                <input type="text" name="HONOR_TITLE" class="w160 mr10 error" maxlength="8">
                                <br><span class="usernavi">{{ $usernavi['HONOR'] }}</span>
                                <br><span class="must">{{ $errors->first('HONOR') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th>社判登録<br /></th>
                            <td>
                                @if(isset($image))
                                    <img src="{{ url('companies/contents') }}" width="100" height="100" alt="Seal Image">
                                @endif
                                <input type="file" name="image">
                                <input type="checkbox" name="DEL_SEAL" style="width:30px;">削除
                                <div></div>
                                <br><span class="usernavi">{{ $usernavi['SEAL'] }}</span>
                                <br><span class="must">{{ $ierror == 1 ? '画像はjpeg,png,gifのみです' : '' }}</span>
                                <br><span class="must">{{ $ierror == 2 ? '1MB以上の画像は登録できません' : '' }}</span>
                                <br><span class="must">{{ $ierror == 3 ? '正しい画像を指定してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th class="{{ $errors->has('CMP_SEAL_FLG') ? 'txt_top' : '' }}">
                                <span class="float_l">押印設定</span>
                            </th>
                            <td id="SEAL_FLG" colspan="3">
                                @foreach($seal_flg as $key => $value)
                                    <label><input type="radio" name="HONOR_CODE" value="{{ $key }}" class="ml20 mr5 txt_mid" {{ old('CMP_SEAL_FLG') == $key ? ' selected' : '' }}> {{ $value }}</label>
                                @endforeach
                                <br><span class="usernavi">{{ $usernavi['CMP_SEAL_FLG'] }}</span>
                                <br><span class="must">{{ $errors->first('CMP_SEAL_FLG') }}</span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Content Bottom">
        </div>
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>
        <h3><div class="company_02"><span class="edit_txt">&nbsp;</span></div></h3>

        <div class="contents_box">
            <span class="usernavi">{{ $usernavi['CMP_PAYMENT'] }}</span>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('CUTOOFF_DATE') ? 'txt_top' : '' }}">締日</th>
                        <td style="width:750px;">
                            @foreach($cutooff_select['options'] as $key => $value)
                                <label><input type="radio" name="CUTOOFF_SELECT" value="{{ $key }}" class="ml20 mr5 txt_mid" {{ old('CUTOOFF_SELECT') == $key ? ' selected' : '' }}> {{ $value }}</label>
                            @endforeach

                            <input type="text" name="CUTOOFF_DATE" value="{{ old('CUTOOFF_DATE') }}" class="w60 mr5 ml5 {{ $errors->has('CUTOOFF_DATE') ? 'error' : '' }}" maxlength="2">日
                            <br><span class="usernavi">{{ $usernavi['CUTOOFF'] }}</span>
                            <br><span class="must">{{ $errors->first('CUTOOFF_DATE') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th class="{{ $errors->has('PAYMENT_DAY') ? 'txt_top' : '' }}">支払日</th>
                        <td>
                            @foreach($payment_month as $key => $value)
                                <label><input type="radio" name="CUTOOFF_SELECT" value="{{ $key }}" class="ml20 mr5 txt_mid" {{ old('CUTOOFF_SELECT') == $key ? ' selected' : '' }}> {{ $value }}</label>
                            @endforeach
                            <select name="PAYMENT_MONTH" class="w120 mr20" size="1">

                            </select>
                            <select name="PAYMENT_SELECT">
                            </select>
                            <input type="text" name="PAYMENT_DAY" value="{{ old('PAYMENT_DAY') }}" class="w60 mr5 ml5 {{ $errors->has('PAYMENT_DAY') ? 'error' : '' }}" maxlength="2">日
                            <br><span class="usernavi">{{ $usernavi['PAYMENT'] }}</span>
                            <br><span class="must">{{ $errors->first('PAYMENT_DAY') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>数量小数部表示</th>
                        <td>
                            <select name="DECIMAL_QUANTITY">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>単価小数部表示</th>
                        <td>
                            <select name="DECIMAL_UNITPRICE">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>消費税設定</th>
                        <td>
                            <select name="EXCISE">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>消費税端数処理</th>
                        <td>
                            <select name="TAX_FRACTION">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>消費税端数計算</th>
                        <td>
                            <select name="TAX_FRACTION_TIMING">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>基本端数処理</th>
                        <td>
                            <select name="FRACTION">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Background Bottom">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_03"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <span class="usernavi">{{ $usernavi['ACCOUNT_HOLDER'] }}</span>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" class="{{ $errors->has('ACCOUNT_HOLDER') ? 'txt_top' : '' }}">名義</th>
                        <td style="width:750px;">
                            <input type="text" name="ACCOUNT_HOLDER" value="{{ old('ACCOUNT_HOLDER') }}" class="w300 {{ $errors->first('ACCOUNT_HOLDER') ? 'error' : '' }}" maxlength="200">
                            <br><span class="must">{{ $errors->first('ACCOUNT_HOLDER') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th class="{{ $errors->has('BANK_NAME') ? 'txt_top' : '' }}">銀行名</th>
                        <td>
                            <input type="text" name="BANK_NAME" value="{{ old('BANK_NAME') }}" class="w300 {{ $errors->first('BANK_NAME') ? 'error' : '' }}" maxlength="200">
                            <br><span class="must">{{ $errors->first('BANK_NAME') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th class="{{ $errors->has('BANK_BRANCH') ? 'txt_top' : '' }}">支店名</th>
                        <td>
                            <input type="text" name="BANK_BRANCH" value="{{ old('BANK_BRANCH') }}" class="w300 {{ $errors->first('BANK_BRANCH') ? 'error' : '' }}" maxlength="200">
                            <br><span class="must">{{ $errors->first('BANK_BRANCH') }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>口座区分</th>
                        <td>
                            <select name="ACCOUNT_TYPE" class="w120 mr20" size="1">
                                <!-- Replace with PHP loop if needed -->
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th class="{{ $errors->has('ACCOUNT_NUMBER') ? 'txt_top' : '' }}">口座番号</th>
                        <td>
                            <input type="text" name="ACCOUNT_NUMBER" value="{{ old('ACCOUNT_NUMBER') }}" class="w300 {{ $errors->first('ACCOUNT_NUMBER') ? 'error' : '' }}" maxlength="7">
                            <br><span class="must">{{ $errors->first('ACCOUNT_NUMBER') }}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Background Bottom">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_04"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <span class="usernavi">{{ $usernavi['FORM_OPTION'] }}</span>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">
            <div class="contents_area">
                <div>
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:130px;">枠色</th>
                            <td style="width:330px;">
                                <select name="COLOR" class="mr200">
                                    <!-- Replace with options -->
                                </select>
                                <br><span class="usernavi">COLOR</span>
                            </td>
                            <td style="width:320px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th>方向</th>
                            <td>
                                <select name="DIRECTION">
                                    <!-- Replace with options -->
                                </select>
                                <br><span class="usernavi">DIRECTION</span>
                            </td>
                            <td style="width:320px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <tr>
                            <th>連番設定</th>
                            <td colspan="2">
                                <label><input type="radio" name="SERIAL_NUMBER" value="1" onclick="show_serial(value);"> Option 1</label>
                                <label><input type="radio" name="SERIAL_NUMBER" value="2" onclick="show_serial(value);"> Option 2</label>
                                <!-- Replace with options and values -->
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="serial_option">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td colspan="4" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                        <!-- Repeat for each serial option -->
                        <!-- Loop Start -->
                        <tr>
                            <th style="width:100px;" rowspan="3">見積書</th>
                            <td style="width:100px;">付番書式</td>
                            <td style="width:100px;">
                                <select name="SERIAL.0.NUMBERING_FORMAT" onchange="update_serial(0); format_change(value, 0); change_sample(0);">
                                    <!-- Replace with options -->
                                </select>
                                <br><span class="must">Error message</span>
                            </td>
                            <td style="width:580px;">
                                サンプル
                                <span id="sample0">
                                    <!-- Sample logic -->
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100px;">接頭文字</td>
                            <td colspan="2" style="width:680px;">
                                <input type="text" name="SERIAL.0.PREFIX" class="w300" maxlength="12" onchange="update_serial(0)" onkeyup="count_str('prefix0_rest', value, 12); change_sample(0)">
                                <span id="prefix0_rest"></span>
                                <br>&nbsp;
                                <br><span class="must">Error message</span>
                            </td>
                        </tr>
                        <tr class="NF0">
                            <td style="width:100px;">次回番号</td>
                            <td style="width:680px;" colspan="2">
                                <input type="text" name="SERIAL.0.NEXT" class="w300" maxlength="8" onchange="update_serial(0)" onkeyup="count_str('next0_rest', value, 8); change_sample(0)">
                                <span id="next0_rest"></span>
                                <br>
                                <span class="must">Error message</span>
                            </td>
                        </tr>
                        <tr><td colspan="3" class="serial_update0"></td></tr>
                        <tr><td colspan="3"><input type="hidden" name="SERIAL.0.CHANGED"></td></tr>
                        <!-- Loop End -->
                    </table>
                </div>
            </div>

            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Background Bottom">
        </div>

        <div class="edit_btn">
            <button type="submit" name="submit" alt="保存する" class="imgover" style="border: none;">
                <img src="{{ asset('img/bt_save.jpg') }}" alt="Save">
            </button>
            <button type="submit" name="cancel" alt="キャンセル" class="imgover" style="border: none;">
                <img src="{{ asset('img/bt_cancel.jpg') }}" alt="Cancel">
            </button>
        </div>
    </div>



    <input type="hidden" name="CMP_ID" value="1">
    <input type="hidden" name="csrf_token" value="YOUR_CSRF_TOKEN">


@endsection
