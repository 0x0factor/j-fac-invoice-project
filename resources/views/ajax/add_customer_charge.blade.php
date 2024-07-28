@extends('layout.app')

@section('link')
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
@endsection

@section('content')
    <!-- 帳票管理の顧客登録ポップアップ画面 -->
    <form id="popupForm">
        <div id="popup_contents">
            <img src="{{ asset('/img/popup/tl_customercharge.jpg') }}" style="padding-bottom:10px;" />
            <input type="hidden" name="type" value="customer_charge">
            <div class="popup_contents_box">
                <div class="popup_contents_area clearfix">
                    <table width="440" cellpadding="0" cellspacing="0" border="0">
                        <tr class="popup_cname">
                            <th style="width:130px;">担当者名<img src="{{ asset('img/i_must.jpg') }}" alt="必須"
                                    class="pl10" /></th>
                            <td style="width:310px;"><input type="text" name="CHARGE_NAME" class="w300" maxlength="60">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr class="popup_ckname">
                            <th>担当者名（カナ） </th>
                            <td><input type="text" name="CHARGE_NAME_KANA" class="w300" maxlength="100"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>

                        <tr class="popup_cunit">
                            <th style="width:130px;">担当者部署名 </th>
                            <td style="width:310px;"><input type="text" name="UNIT" class="w300" maxlength="60">
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr class="popup_postcode">
                            <th>郵便番号 </th>
                            <td>
                                <input type="text" name="POSTCODE1" class="w60 zip" maxlength="3">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="POSTCODE2" class="w80 zip" maxlength="4">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr class="popup_county">
                            <th>都道府県 </th>
                            <td>
                                <select name="CNT_ID" class="form-control">
                                    @foreach ($countys as $key => $county)
                                        <option value="{{ $key }}">{{ $county }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr class="popup_address">
                            <th>住所 </th>
                            <td><input type="text" name="ADDRESS" class="w300" maxlength="100"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                        <tr class="popup_phone">
                            <th>電話番号 </th>
                            <td>
                                <input type="text" name="PHONE_NO1" class="w60 phone" maxlength="4">
                                - <input type="text" name="PHONE_NO2" class="w60 phone" maxlength="4">
                                - <input type="text" name="PHONE_NO3" class="w60 phone" maxlength="4">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('/img/popup/i_line_solid.gif') }}"></td>
                        </tr>
                    </table>
                    <div class="save_btn">
                        <a href="#" onclick="return popupclass.popupinsert('customer_charge')">
                            <img src="{{ asset('img/bt_save2.jpg') }}" alt="Save" />
                        </a>
                        <a href="#" onclick="return popupclass.popup_close()">
                            <img src="{{ asset('img/bt_cancel_s.jpg') }}" alt="Cancel" />
                        </a>
                        {!! $customHtml->hiddenToken() !!}
                        <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                        <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
                        <input type="hidden" name="CST_ID" value="{{ $cst_id }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
