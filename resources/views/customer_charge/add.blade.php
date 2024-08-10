@extends('layout.default')

@section('scripts')
    <script type="text/javascript">
        function customer_reset() {
            $('#SETCUSTOMER').children('input[type=text]').val('');
            $('#SETCUSTOMER').children('input[type=hidden]').val(0);
            $("#INSERT_ADDRESS").html("");
            return false;
        }
    </script>
@endsection

@section('content')
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
            <p>こちらのページは取引先担当者登録の画面です。<br>必要な情報を入力の上「保存する」ボタンを押下すると取引先担当者を作成できます。</p>
        </div>
    </div>
    <br class="clear">
    <!-- header_End -->

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under"><img src="{{ asset('i_arrow_under.jpg') }}" alt=""></div>

        <h3>
            <div class="edit_01_c_charge"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <form action="{{ route('customer_charge.add') }}" method="POST" class="CustomerCharge">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <select name="STATUS" class="{{ $errors->hass->has('STATUS') ? 'error' : '' }}">
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}" {{ old('STATUS') == $key ? 'selected' : '' }}>
                                            {{ $value }}</option>
                                    @endforeach
                                </select>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}" alt="">
                            </td>
                        </tr>

                        <tr>
                            <th style="width:150px;">顧客名</th>
                            <td id="SETCUSTOMER" style="width:730px;">
                                <input type="text" name="CUSTOMER_NAME" value="{{ old('CUSTOMER_NAME') }}" readonly
                                    class="w130">
                                <input type="hidden" name="CST_ID" value="{{ old('CST_ID') }}">
                                <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                    <img src="{{ asset('bt_registered.jpg') }}" alt="" class="imgover">
                                </a>
                                <a href="#" onclick="return customer_reset();">
                                    <img src="{{ asset('bt_reset.jpg') }}" alt="" class="imgover">
                                </a>
                                <br>
                                <span id="INSERT_ADDRESS"></span>
                                <br>
                                <span class="usernavi">{{ $usernavi['CUSTOMER_ADDRESS'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}" alt="">
                            </td>
                        </tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('CHARGE_NAME') ? 'txt_top' : '' }}">
                                <span class="float_l">担当者名</span>
                                @if ($errors->first('CHARGE_NAME'))
                                    <img src="{{ asset('i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                                @endif
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="CHARGE_NAME" value="{{ old('CHARGE_NAME') }}"
                                    class="w300{{ $errors->has('CHARGE_NAME') ? ' error' : '' }}" maxlength="60">
                                <br>
                                <span class="usernavi">{{ $usernavi['CHARGE_NAME'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}" alt="">
                            </td>
                        </tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('CHARGE_NAME_KANA') ? 'txt_top' : '' }}">
                                <span class="float_l">担当者名カナ</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="CHARGE_NAME_KANA" value="{{ old('CHARGE_NAME_KANA') }}"
                                    class="w300{{ $errors->has('CHARGE_NAME_KANA') ? ' error' : '' }}" maxlength="60">
                                <br>
                                <span class="usernavi">{{ $usernavi['CHARGE_NAME_KANA'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('CHARGE_NAME_KANA') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}" alt="">
                            </td>
                        </tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('UNIT') ? 'txt_top' : '' }}">
                                <span class="float_l">部署名</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="UNIT" value="{{ old('UNIT') }}"
                                    class="w300{{ $errors->has('UNIT') ? ' error' : '' }}" maxlength="60">
                                <br>
                                <span class="usernavi">{{ $usernavi['UNIT'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('UNIT') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th class="{{ $errors->has('POST') ? 'txt_top' : '' }}">役職名</th>
                            <td>
                                <input type="text" name="POST" value="{{ old('POST') }}"
                                    class="w300{{ $errors->has('POST') ? ' error' : '' }}" maxlength="60">
                                <br>
                                <span class="usernavi">{{ $usernavi['POST'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('POST') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th class="{{ $errors->has('MAIL') ? 'txt_top' : '' }}">メールアドレス</th>
                            <td>
                                <input type="text" name="MAIL" value="{{ old('MAIL') }}"
                                    class="w300{{ $errors->has('MAIL') ? ' error' : '' }}" maxlength="256">
                                <br>
                                <span class="must">{{ $errors->first('MAIL') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th style="width:150px;"
                                class="{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}">
                                <span class="float_l">郵便番号</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="POSTCODE1" value="{{ old('POSTCODE1') }}"
                                    class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}"
                                    maxlength="3">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="POSTCODE2" value="{{ old('POSTCODE2') }}"
                                    class="w60{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}"
                                    maxlength="4">
                                <div id="target"></div>
                                <br>
                                <span class="usernavi">{{ $usernavi['POSTCODE'] }}</span>
                                <br>
                                <span class="must">
                                    @if ($errors->first('POSTCODE1'))
                                        {{ $errors('POSTCODE1') }}
                                    @elseif ($errors->first('POSTCODE2'))
                                        {{ $errors('POSTCODE2') }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th style="width:150px;"><span class="float_l">都道府県</span></th>
                            <td style="width:730px;">
                                <select name="CNT_ID" class="{{ $errors->has('CNT_ID') ? 'error' : '' }}">
                                    @foreach ($countys as $key => $value)
                                        <option value="{{ $key }}"
                                            {{ old('CNT_ID') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('ADDRESS') ? 'txt_top' : '' }}"><span
                                    class="float_l">住所</span></th>
                            <td style="width:730px;">
                                <input type="text" name="ADDRESS" value="{{ old('ADDRESS') }}"
                                    class="w600{{ $errors->has('ADDRESS') ? ' error' : '' }}" maxlength="100">
                                <br>
                                <span class="usernavi">{{ $usernavi['ADDRESS'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('ADDRESS') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th class="{{ $errors->has('BUILDING') ? 'txt_top' : '' }}">建物名</th>
                            <td>
                                <input type="text" name="BUILDING" value="{{ old('BUILDING') }}"
                                    class="w600{{ $errors->has('BUILDING') ? ' error' : '' }}" maxlength="100">
                                <br>
                                <span class="usernavi">{{ $usernavi['BUILDING'] }}</span>
                                <br>
                                <span class="must">{{ $errors->first('BUILDING') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th class="{{ $perror == 1 ? 'txt_top' : '' }}">
                                <span class="float_l">電話番号</span>
                            </th>
                            <td>
                                <input type="text" name="PHONE_NO1" value="{{ old('PHONE_NO1') }}"
                                    class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="5">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO2" value="{{ old('PHONE_NO2') }}"
                                    class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO3" value="{{ old('PHONE_NO3') }}"
                                    class="w60{{ $perror == 1 ? ' error' : '' }}" maxlength="4">
                                <br>
                                <span class="usernavi">{{ $usernavi['PHONE'] }}</span>
                                <br>
                                <span class="must">{{ $perror == 1 ? '正しい電話番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('i_line_solid.gif') }}"
                                    alt=""></td>
                        </tr>

                        <tr>
                            <th class="{{ $ferror == 1 ? 'txt_top' : '' }}">FAX番号</th>
                            <td>
                                <input type="text" name="FAX_NO1" value="{{ old('FAX_NO1') }}"
                                    class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO2" value="{{ old('FAX_NO2') }}"
                                    class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO3" value="{{ old('FAX_NO3') }}"
                                    class="w60{{ $ferror == 1 ? ' error' : '' }}" maxlength="4">
                                <br>
                                <span class="usernavi">{{ $usernavi['FAX'] }}</span>
                                <br>
                                <span class="must">{{ $ferror == 1 ? '正しいFAX番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <img src="{{ asset('bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>
        <div class="edit_btn">
            <input type="image" src="{{ asset('img/bt_save.jpg') }}" alt="保存する" class="imgover"
                form="CustomerCharge" name="submit">
            <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル" class="imgover"
                form="CustomerCharge" name="cancel">
        </div>
    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['UPDATE_USR_ID'] }}">
@endsection
