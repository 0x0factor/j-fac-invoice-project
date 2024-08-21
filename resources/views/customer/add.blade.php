@extends('layout.default')

@section('script')
    <script type="text/javascript">
        <!--
        function charge_reset() {
            $('#SETCHARGE').children('input[type=text]').val('');
            $('#SETCHARGE').children('input[type=hidden]').val(0);
            return false;
        }
        //
        -->
    </script>
@endsection

@section('content')
    <!-- guide section -->
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('/img/company/i_guide.jpg') }}" alt="">
            <p>こちらのページは顧客情報設定の画面です。<br>必要な情報を入力の上「保存する」ボタンを押下すると顧客情報を作成できます。</p>
        </div>
    </div>
    <br class="clear">

    <!-- contents section -->
    <div id="contents">
        <form action="{{ route('customer.add') }}" method="POST" class="Customer">
            @csrf
            <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>

            <h3>
                <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
            </h3>

            <div class="contents_box">
                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
                <div class="contents_area">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <!-- Company Name -->
                            <tr>
                                <th style="width:130px;" class="{{ $errors->has('NAME') ? 'txt_top' : '' }}">
                                    <span class="float_l">社名</span>
                                    <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                                </th>
                                <td style="width:750px;">
                                    <input type="text" name="NAME" class="w300{{ $errors->has('NAME') ? ' error' : '' }}" maxlength="60" value="{{ old('NAME') }}">
                                    <br><span class="usernavi">{{ $usernavi['CMP_NAME'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('NAME') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Company Name Kana -->
                            <tr>
                                <th style="width:130px;" class="{{ $errors->has('NAME_KANA') ? 'txt_top' : '' }}">
                                    <span class="float_l">社名カナ</span>
                                </th>
                                <td style="width:750px;">
                                    <input type="text" name="NAME_KANA" class="w300{{ $errors->has('NAME_KANA') ? ' error' : '' }}" maxlength="100" value="{{ old('NAME_KANA') }}">
                                    <br><span class="must">{{ $errors->first('NAME_KANA') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Honorific Title -->
                            <tr>
                                <th class="{{ $errors->has('HONOR_TITLE') ? 'txt_top' : '' }}">
                                    <span class="float_l">敬称</span>
                                </th>
                                <td id="HONOR" colspan="3">
                                    @foreach($honor as $key => $value)
                                        <label class="ml20 mr5 txt_mid">
                                            <input type="radio" name="HONOR_CODE" value="{{ $key }}" {{ old('HONOR_CODE') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                    @endforeach
                                    <input type="text" name="HONOR_TITLE" class="w160 mr10{{ $errors->has('HONOR_TITLE') ? ' error' : '' }}" maxlength="8" value="{{ old('HONOR_TITLE') }}">
                                    <br><span class="usernavi">{{ $usernavi['HONOR'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('HONOR_TITLE') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Postal Code -->
                            <tr>
                                <th style="width:130px;" class="{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}">
                                    <span class="float_l">郵便番号</span>
                                </th>
                                <td style="width:750px;">
                                    <input type="text" name="POSTCODE1" class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? ' error' : '' }}" maxlength="3" value="{{ old('POSTCODE1') }}">
                                    <span class="pl5 pr5">-</span>
                                    <input type="text" name="POSTCODE2" class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? ' error' : '' }}" maxlength="4" value="{{ old('POSTCODE2') }}">
                                    <br><span class="usernavi">{{ $usernavi['POSTCODE'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('POSTCODE1') ?: $errors->first('POSTCODE2') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Prefecture -->
                            <tr>
                                <th style="width:130px;">
                                    <span class="float_l">都道府県</span>
                                </th>
                                <td style="width:750px;">
                                    <select name="CNT_ID" class="form-control">
                                        @foreach($countys as $key => $value)
                                            <option value="{{ $key }}" {{ old('CNT_ID') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Address -->
                            <tr>
                                <th style="width:130px;" class="{{ $errors->has('ADDRESS') ? 'txt_top' : '' }}">
                                    <span class="float_l">住所</span>
                                </th>
                                <td style="width:750px;">
                                    <input type="text" name="ADDRESS" class="w600{{ $errors->has('ADDRESS') ? ' error' : '' }}" maxlength="100" value="{{ old('ADDRESS') }}">
                                    <br><span class="usernavi">{{ $usernavi['ADDRESS'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('ADDRESS') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Building Name -->
                            <tr>
                                <th class="{{ $errors->has('BUILDING') ? 'txt_top' : '' }}">建物名</th>
                                <td>
                                    <input type="text" name="BUILDING" class="w600{{ $errors->has('BUILDING') ? ' error' : '' }}" maxlength="100" value="{{ old('BUILDING') }}">
                                    <br><span class="usernavi">{{ $usernavi['BUILDING'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('BUILDING') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Phone Number -->
                            <tr>
                                <th style="width:130px;" class="{{ $perror == 1 ? 'txt_top' : '' }}">
                                    <span class="float_l">電話番号</span>
                                </th>
                                <td style="width:750px;">
                                    <input type="text" name="PHONE_NO1" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="5" value="{{ old('PHONE_NO1') }}">
                                    <span class="pl5 pr5">-</span>
                                    <input type="text" name="PHONE_NO2" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4" value="{{ old('PHONE_NO2') }}">
                                    <span class="pl5 pr5">-</span>
                                    <input type="text" name="PHONE_NO3" class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4" value="{{ old('PHONE_NO3') }}">
                                    <br><span class="usernavi">{{ $usernavi['PHONE'] ?? '' }}</span>
                                    <br><span class="must">{{ $perror == 1 ? '正しい電話番号を入力してください' : '' }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Fax Number -->
                            <tr>
                                <th class="{{ $ferror == 1 ? 'txt_top' : '' }}">FAX番号</th>
                                <td>
                                    <input type="text" name="FAX_NO1" class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4" value="{{ old('FAX_NO1') }}">
                                    <span class="pl5 pr5">-</span>
                                    <input type="text" name="FAX_NO2" class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4" value="{{ old('FAX_NO2') }}">
                                    <span class="pl5 pr5">-</span>
                                    <input type="text" name="FAX_NO3" class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4" value="{{ old('FAX_NO3') }}">
                                    <br><span class="usernavi">{{ $usernavi['FAX'] ?? '' }}</span>
                                    <br><span class="must">{{ $ferror == 1 ? '正しいFAX番号を入力してください' : '' }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Website -->
                            <tr>
                                <th class="{{ $errors->has('WEBSITE') ? 'txt_top' : '' }}">ホームページ</th>
                                <td>
                                    <input type="text" name="WEBSITE" class="w600{{ $errors->has('WEBSITE') ? ' error' : '' }}" maxlength="100" value="{{ old('WEBSITE') }}">
                                    <br><span class="usernavi">{{ $usernavi['WEBSITE'] ?? '' }}</span>
                                    <br><span class="must">{{ $errors->first('WEBSITE') }}</span>
                                </td>
                            </tr>

                            <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                            <!-- Representative -->
                            <tr>
                                <th>自社担当者</th>
                                <td id="SETCHARGE">
                                    <input type="text" name="CHR_NAME" class="w100" value="{{ old('CHR_NAME') }}" readonly>
                                    <input type="hidden" name="CHR_ID" value="{{ old('CHR_ID') }}">
                                    <a href="#" onclick="return popupclass.popupajax('charge');">
                                        <img src="{{ asset('img/bt_registered.jpg') }}" alt="Registered">
                                    </a>
                                    <a href="#" onclick="return charge_reset();">
                                        <img src="{{ asset('img/bt_reset.jpg') }}" alt="Reset">
                                    </a>
                                    <br><span class="usernavi">{{ $usernavi['CHR_ID'] ?? '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
                </div>


                <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under"></div>

                <h3>
                    <div class="company_02"><span class="edit_txt">&nbsp;</span></div>
                </h3>

                <div class="contents_box">
                    <span class="usernavi">{{ $usernavi['CUSTOMER_PAYMENT'] ?? '' }}</span>
                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
                <div class="contents_area">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <!-- Cutoff Date -->
                        <tr>
                            <th style="width:130px;" class="{{ $errors->has('CUTOOFF_DATE') ? 'txt_top' : '' }}">締日</th>
                            <td style="width:750px;">
                                    @foreach($cutooffSelect['options'] as $key => $value)
                                        <label class="ml20 mr5 txt_mid">
                                            <input type="radio" name="CUTOOFF_SELECT" value="{{ $key }}" {{ old('CUTOOFF_SELECT') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                    @endforeach

                                <input type="text" name="CUTOOFF_DATE" class="w60 mr5 ml5{{ $errors->has('CUTOOFF_DATE') ? ' error' : '' }}" maxlength="2" value="{{ old('CUTOOFF_DATE') }}">日
                                <br><span class="usernavi">{{ $usernavi['CST_CUTOOFF'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('CUTOOFF_DATE') }}</span>
                            </td>
                        </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Payment Day -->
                        <tr>
                            <th class="{{ $errors->has('PAYMENT_DAY') ? 'txt_top' : '' }}">支払日</th>
                            <td>
                                <select name="PAYMENT_MONTH" class="w120 mr20">
                                    @foreach($payment as $key => $value)
                                        <option value="{{ $key }}" {{ old('PAYMENT_MONTH') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                                    @foreach($paymentSelect['options'] as $key => $value)
                                        <label class="ml20 mr5 txt_mid">
                                            <input type="radio" name="PAYMENT_SELECT" value="{{ $key }}" {{ old('PAYMENT_SELECT') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                    @endforeach
                                    <input type="text" name="PAYMENT_DAY" class="w60 mr5 ml5{{ $errors->has('PAYMENT_DAY') ? ' error' : '' }}" maxlength="2" value="{{ old('PAYMENT_DAY') }}">日
                                    <br><span class="usernavi">{{ $usernavi['CST_PAYMENT'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('PAYMENT_DAY') }}</span>
                            </td>
                        </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Excise Setting -->
                        <tr>
                            <th>消費税設定</th>
                            <td>
                                    @foreach($excises['options'] as $key => $value)
                                    <label class="ml20 mr5 txt_mid">
                                            <input type="radio" name="EXCISE" value="{{ $key }}" {{ old('EXCISE') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                        @endforeach
                                    </td>
                                </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Tax Fraction Processing -->
                        <tr>
                            <th>消費税端数処理</th>
                            <td>
                                    @foreach($fractions['options'] as $key => $value)
                                        <label class="ml20 mr5 txt_mid">
                                            <input type="radio" name="TAX_FRACTION" value="{{ $key }}" {{ old('TAX_FRACTION') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                    @endforeach
                            </td>
                        </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Tax Fraction Timing -->
                        <tr>
                            <th>消費税端数計算</th>
                            <td>
                                    @foreach($tax_fraction_timing['options'] as $key => $value)
                                    <label class="ml20 mr5 txt_mid">
                                        <input type="radio" name="TAX_FRACTION_TIMING" value="{{ $key }}" {{ old('TAX_FRACTION_TIMING') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                        @endforeach
                                    </td>
                                </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Basic Fraction Processing -->
                        <tr>
                            <th>基本端数処理</th>
                            <td>
                                @foreach($fractions['options'] as $key => $value)
                                <label class="ml20 mr5 txt_mid">
                                    <input type="radio" name="FRACTION" value="{{ $key }}" {{ old('FRACTION') == $key ? 'checked' : '' }}>
                                            {{ $value }}
                                        </label>
                                    @endforeach
                            </td>
                        </tr>

                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line"></td></tr>

                        <!-- Notes -->
                        <tr>
                            <th class="txt_top">備考</th>
                            <td>
                                <textarea name="NOTE" class="textarea{{ $errors->has('NOTE') ? ' error' : '' }}" maxlength="1000">{{ old('NOTE') }}</textarea>
                                <br><span class="must">{{ $errors->first('NOTE') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
            </div>


            <div class="edit_btn">
                <button type="submit" name="submit" class="imgover"><img src="{{ asset('img/bt_save.jpg') }}"
                        alt="保存する" style="border:none;"></button>
                <button type="submit" name="cancel" class="imgover"><img src="{{ asset('img/bt_cancel.jpg') }}"
                alt="キャンセル" style="border:none;"></button>
            </div>
        </form>
    </div>

    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
@endsection
