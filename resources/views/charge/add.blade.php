@extends('layout.default')

@section('scripts')
    <script>
        $(document).ready(function() {
            if ($('input[name="data[Charge][SEAL_METHOD]"]:checked').val() == 1) {
                $('div.SEAL_METHOD').slideToggle();
            }

            $('input[name="data[Charge][SEAL_METHOD]"]').change(function() {
                $('div.SEAL_METHOD').slideToggle();
            });
        });
    </script>
@endsection

@section('content')
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
            <p>こちらのページは自社担当者登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると自社担当者を作成できます。</p>
        </div>
    </div>
    <br class="clear" />
    <!-- header_End -->

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>

        <h3>
            <div class="edit_01">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <form action="{{ route('charge.add') }}" method="POST" enctype="multipart/form-data" class="Charge">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <select name="STATUS" class="form-control">
                                    @foreach($status as $key => $value)
                                        <option value="{{ $key }}" {{ old('STATUS') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('CHARGE_NAME') ? 'txt_top' : '' }}">
                                <span class="float_l">担当者名</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r" />
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="CHARGE_NAME" class="w300 {{ $errors->has('CHARGE_NAME') ? 'error' : '' }}" maxlength="60" value="{{ old('CHARGE_NAME') }}">
                                <br><span class="usernavi">{{ $usernavi['CHARGE_NAME'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('CHARGE_NAME_KANA') ? 'txt_top' : '' }}">
                                <span class="float_l">担当者名カナ</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="CHARGE_NAME_KANA" class="w300 {{ $errors->has('CHARGE_NAME_KANA') ? 'error' : '' }}" maxlength="60" value="{{ old('CHARGE_NAME_KANA') }}">
                                <br><span class="usernavi">{{ $usernavi['CHARGE_NAME_KANA'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('CHARGE_NAME_KANA') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('UNIT') ? 'txt_top' : '' }}">
                                <span class="float_l">部署名</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="UNIT" class="w300 {{ $errors->has('UNIT') ? 'error' : '' }}" maxlength="60" value="{{ old('UNIT') }}">
                                <br><span class="usernavi">{{ $usernavi['UNIT'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('UNIT') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th class="{{ $errors->has('POST') ? 'txt_top' : '' }}">役職名</th>
                            <td>
                                <input type="text" name="POST" class="w300 {{ $errors->has('POST') ? 'error' : '' }}" maxlength="60" value="{{ old('POST') }}">
                                <br><span class="usernavi">{{ $usernavi['POST'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('POST') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th class="{{ $errors->has('MAIL') ? 'txt_top' : '' }}">メールアドレス</th>
                            <td>
                                <input type="email" name="MAIL" class="w300 {{ $errors->has('MAIL') ? 'error' : '' }}" maxlength="256" value="{{ old('MAIL') }}">
                                <br><span class="must">{{ $errors->first('MAIL') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th class="{{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'txt_top' : '' }}" style="width:150px;">
                                <span class="float_l">郵便番号</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="POSTCODE1" class="w60 {{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'error' : '' }}" maxlength="3" value="{{ old('POSTCODE1') }}">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="POSTCODE2" class="w60 {{ $errors->has('POSTCODE1') || $errors->has('POSTCODE2') ? 'error' : '' }}" maxlength="4" value="{{ old('POSTCODE2') }}">
                                <br><span class="usernavi">{{ $usernavi['POSTCODE'] ?? '' }}</span>

                                <div id="target"></div>

                                <br><span class="must">{{ $errors->first('POSTCODE1') ?: $errors->first('POSTCODE2') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;"><span class="float_l">都道府県</span></th>
                            <td style="width:730px;">
                                <select name="CNT_ID" class="form-control">
                                    @foreach($countys as $key => $value)
                                        <option value="{{ $key }}" {{ old('CNT_ID') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('ADDRESS') ? 'txt_top' : '' }}">
                                <span class="float_l">住所</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="ADDRESS" class="w600 {{ $errors->has('ADDRESS') ? 'error' : '' }}" maxlength="100" value="{{ old('ADDRESS') }}">
                                <br><span class="usernavi">{{ $usernavi['ADDRESS'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('ADDRESS') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th class="{{ $errors->has('BUILDING') ? 'txt_top' : '' }}">建物名</th>
                            <td>
                                <input type="text" name="BUILDING" class="w600 {{ $errors->has('BUILDING') ? 'error' : '' }}" maxlength="100" value="{{ old('BUILDING') }}">
                                <br><span class="usernavi">{{ $usernavi['BUILDING'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('BUILDING') }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th style="width:150px;" class="{{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? 'txt_top' : '' }}">
                                <span class="float_l">電話番号</span>
                            </th>
                            <td style="width:730px;">
                                <input type="text" name="PHONE_NO1" class="w60 {{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? 'error' : '' }}" maxlength="5" value="{{ old('PHONE_NO1') }}">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO2" class="w60 {{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? 'error' : '' }}" maxlength="4" value="{{ old('PHONE_NO2') }}">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO3" class="w60 {{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? 'error' : '' }}" maxlength="4" value="{{ old('PHONE_NO3') }}">
                                <br><span class="usernavi">{{ $usernavi['PHONE'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? '正しい電話番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th class="{{ $errors->has('FAX_NO1') || $errors->has('FAX_NO2') || $errors->has('FAX_NO3') ? 'txt_top' : '' }}">FAX番号</th>
                            <td>
                                <input type="text" name="FAX_NO1" class="w60 {{ $errors->has('FAX_NO1') || $errors->has('FAX_NO2') || $errors->has('FAX_NO3') ? 'error' : '' }}" maxlength="4" value="{{ old('FAX_NO1') }}">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO2" class="w60 {{ $errors->has('FAX_NO1') || $errors->has('FAX_NO2') || $errors->has('FAX_NO3') ? 'error' : '' }}" maxlength="4" value="{{ old('FAX_NO2') }}">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="FAX_NO3" class="w60 {{ $errors->has('FAX_NO1') || $errors->has('FAX_NO2') || $errors->has('FAX_NO3') ? 'error' : '' }}" maxlength="4" value="{{ old('FAX_NO3') }}">
                                <br><span class="usernavi">{{ $usernavi['FAX'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->has('FAX_NO1') || $errors->has('FAX_NO2') || $errors->has('FAX_NO3') ? '正しいFAX番号を入力してください' : '' }}</span>
                            </td>
                        </tr>
                        <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="line" /></td></tr>

                        <tr>
                            <th>担当者印</th>
                            <td>
                                @foreach($seal_method as $key => $label)
                                    <label class="ml20 mr5 txt_mid">
                                        <input type="radio" name="SEAL_METHOD" value="{{ $key }}" {{ old('SEAL_METHOD') == $key ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>&nbsp;</th>
                        </tr>
                    </table>



                    <div class="SEAL_METHOD">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <th style="width:150px;">&nbsp;</th>
                                <td>
                                    @if(isset($image))
                                        <img src="{{ route('charge.contents', $id) }}" width="100" height="100" alt="Seal Image">
                                    @endif

                                    <input type="file" name="image" class="form-control">

                                    <input type="checkbox" name="DEL_SEAL" style="width:30px;">削除

                                    <br><span class="usernavi">{{ $usernavi['SEAL'] ?? '' }}</span>

                                    <br><span class="must">
                                        @if($ierror == 1)
                                            画像はjpeg, png, gifのみです
                                        @elseif($ierror == 2)
                                            1MB以上の画像は登録できません
                                        @elseif($ierror == 3)
                                            正しい画像を指定してください
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="SEAL_METHOD" style="display:none">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <th style="width:150px;">&nbsp;</th>
                                <td>
                                    <input type="text" name="SEAL_STR" class="w300" maxlength="4" value="{{ old('SEAL_STR') }}">

                                    <br><span class="usernavi">{{ $usernavi['SEAL_METHOD'] ?? '' }}</span>

                                    <br><span class="must">{{ $errors->first('SEAL_STR') }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="line">
                            </td>
                        </tr>
                        <tr>
                            <th>押印設定</th>
                            <td>
                                @foreach($seal_flg as $key => $label)
                                    <label class="ml20 mr5 txt_mid">
                                        <input type="radio" name="CHR_SEAL_FLG" value="{{ $key }}" {{ old('CHR_SEAL_FLG') == $key ? 'checked' : '' }}>
                                        {{ $label }}
                                    </label>
                                @endforeach

                                <br><span class="usernavi">{{ $usernavi['CHR_SEAL_FLG'] ?? '' }}</span>
                                <br><span class="must">{{ $errors->first('CHR_SEAL_FLG') }}</span>
                            </td>
                        </tr>
                    </table>


                </form>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom Background" class="block">
        </div>
        <!-- Submit and Cancel Buttons -->
        <div class="edit_btn">
            <button type="submit" name="submit" class="imgover" style="border: none;">
                <img src="{{ asset('img/bt_save.jpg') }}" alt="保存する">
            </button>
            <button type="submit" name="cancel" class="imgover" style="border: none;">
                <img src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル">
            </button>
        </div>

        <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
        <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
    </div>
@endsection
