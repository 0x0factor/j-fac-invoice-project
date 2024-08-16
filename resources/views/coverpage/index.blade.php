@extends('layout.default')

@section('scripts')
    <script type="text/javascript">
        function customer_reset() {
            $('#SETCUSTOMER').find('input[type=text]').val('');
            $('#SETCUSTOMER').find('input[type=hidden]').val('');
            return false;
        }

        function cstchr_reset() {
            $('#SETCUSTOMERCHARGE').find('input[type=text]').val('');
            $('#SETCUSTOMERCHARGE').find('input[type=text]').removeAttr('readonly');
            $('#SETCUSTOMERCHARGE').find('input[type=hidden]').val('');
            $('#SETCCUNIT').find('input[type=text]').val('');
            $('#SETCCUNIT').find('input[type=text]').removeAttr('readonly');
            return false;
        }

        function chr_reset() {
            $('#SETCHARGE').find('input[type=text]').val('');
            return false;
        }

        function err_dt(_no) {
            $('tr.row_' + _no + ' input.documenttitle').css('background-color', '#DD0000');
        }

        function err_dn(_no) {
            $('tr.row_' + _no + ' input.documentnumber').css('background-color', '#DD0000');
        }
    </script>
@endsection

@section('content')

@php
        $formType = $formType ?? 'Coverpage';
        $controller = strtolower($formType);
        $action = request()->route()->getActionMethod();
    @endphp

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
            <p>こちらのページは送付状作成の画面です。<br>必要な情報を入力の上「保存する」ボタンを押すと送付状を作成できます。</p>
        </div>
    </div>
    <br class="clear">

    <div id="contents">
        @if (session('flash'))
            <div class="alert alert-success">{{ session('flash') }}</div>
        @endif

        <form action="{{ route('coverpage.store') }}" method="POST" class="Coverpages">
            @csrf
            <div class="arrow_under">
                <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
            </div>

            <h3>
                <div class="edit_02_coverpage"><span class="edit_txt">&nbsp;</span></div>
            </h3>

            <div class="contents_box">
                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">

                <div class="contents_area">
                    <input type="hidden" name="maxformline" value="{{ $maxline }}">
                    <input type="hidden" name="dataformline" value="{{ $dataline }}">

                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('SEND_METHOD') ? 'txt_top' : '' }}">
                                送付方法
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                            </th>
                            <td style="width:710px;" colspan="3">
                                @foreach ($SendMethod as $key => $value)
                                    <input type="radio" name="SEND_METHOD" id="CoverpagesSENDMETHOD{{ $key }}" value="{{ $key }}" class="ml20 mr5 txt_mid">
                                    <label for="">{{ $value }}</label>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('CUSTOMER_NAME') ? 'txt_top' : '' }}">
                                顧客名
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                            </th>
                            <td style="width:710px;" colspan="3" id="SETCUSTOMER">
                                <input type="text" name="CUSTOMER_NAME" value="{{ old('CUSTOMER_NAME') }}"
                                    class="w140 p2{{ $errors->has('CUSTOMER_NAME') ? ' error' : '' }}" maxlength="60"
                                    readonly>
                                <input type="hidden" name="CST_ID" value="{{ old('CST_ID') }}">
                                <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                    <img src="{{ asset('img/bt_select2.jpg') }}" alt="Select Customer">
                                </a>
                                <a href="#" onclick="return customer_reset();">
                                    <img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete Customer">
                                </a>
                                <br><span class="must">{{ $errors->first('CUSTOMER_NAME') }}</span>
                                <br><span class="usernavi">{{ $usernavi['CVR_CST'] }}</span>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;" class="{{ $errors->has('CUSTOMER_CHARGE_NAME') ? 'txt_top' : '' }}">
                                顧客担当者名
                            </th>
                            <td style="width:270px;" id="SETCUSTOMERCHARGE">
                                <input type="text" name="CUSTOMER_CHARGE_NAME" value="{{ old('CUSTOMER_CHARGE_NAME') }}"
                                    class="w120 p2{{ $errors->has('CUSTOMER_CHARGE_NAME') ? ' error' : '' }}"
                                    maxlength="60">
                                <input type="hidden" name="CHRC_ID" value="{{ old('CHRC_ID') }}">
                                <a href="#" onclick="return popupclass.popupajax('customer_charge');">
                                    <img src="{{ asset('img/bt_select2.jpg') }}" alt="Select Charge">
                                </a>
                                <a href="#" onclick="return cstchr_reset();">
                                    <img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete Charge">
                                </a>
                                <br><span class="must">{{ $errors->first('CUSTOMER_CHARGE_NAME') }}</span>
                            </td>
                            <th style="width:170px;">担当者部署名</th>
                            <td style="width:270px;" id="SETCCUNIT">
                                <input type="text" name="CUSTOMER_CHARGE_UNIT" value="{{ old('CUSTOMER_CHARGE_UNIT') }}"
                                    class="w180 p2{{ $errors->has('CUSTOMER_CHARGE_UNIT') ? ' error' : '' }}"
                                    maxlength="60">
                                <br><span class="must">{{ $errors->first('CUSTOMER_CHARGE_UNIT') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3"><br><span class="usernavi">{{ $usernavi['CVR_CST_CHR'] }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;">自社担当者名</th>
                            <td style="width:710px;" colspan="3" id="SETCHARGE">
                                <input type="text" name="CHARGE_NAME" value="{{ old('CHARGE_NAME') }}"
                                    class="w180 p2{{ $errors->has('CHARGE_NAME') ? ' error' : '' }}" maxlength="60">
                                <input type="hidden" name="CHR_ID" value="{{ old('CHR_ID') }}">
                                <a href="#" onclick="return popupclass.popupajax('charge');">
                                    <img src="{{ asset('img/bt_select2.jpg') }}" alt="Select Charge">
                                </a>
                                <a href="#" onclick="return chr_reset();">
                                    <img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete Charge">
                                </a>
                                <br><span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                                <br><span class="usernavi">{{ $usernavi['CHR_NAME'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;">発行日
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                            </th>
                            <td colspan="4">
                                <input type="text" name="data[{{$formType}}][DATE]" id="DATE" value="{{ old('DATE') }}"
                                    class="w100 p2 date cal{{ $errors->has('DATE') ? ' error' : '' }}" readonly>
                                <a href="#" class="nowtime">
                                    <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" onclick="document.getElementById('DATE').value = new Date().toISOString().split('T')[0];">
                                </a>
                                <a href="#" onclick="return cal1.write();">
                                    <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー">
                                </a>
                                <div id="calid"></div>
                                <br><span class="must">{{ $errors->first('DATE') }}</span>
                                <br><span class="usernavi">{{ $usernavi['DATE'] }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:170px;">件名</th>
                            <td colspan="3">
                                <input type="text" name="TITLE" value="{{ old('TITLE') }}"
                                    class="w180 p2{{ $errors->has('TITLE') ? ' error' : '' }}" maxlength="40">
                                <br><span class="must">{{ $errors->first('TITLE') }}</span>
                                <br><span class="usernavi">{{ $usernavi['CVR_TITLE'] }}</span>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                    </table>
                    <div id="SEND_DOCUMENT">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tbody>
                                <tr>
                                    <th style="width:170px;">
                                        送付書類
                                        <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10">
                                    </th>
                                    <td style="width: 42px;">&nbsp;</td>
                                    <th
                                        style="border-left:none;border-right:#FFF 1px solid;background:#5D3221;color:#FFFFFF;width: 250px;">
                                        書類名称</th>
                                    <th
                                        style="border-left:none;border-right:#FFF 1px solid;;background:#5D3221;color:#FFFFFF;width: 100px;">
                                        部数</th>
                                    <td style="width: 318px;">&nbsp;</td>
                                </tr>
                                @for ($i = 0; $i < $maxline; $i++)
                                    <tr class="row_{{ $i }}">
                                        <td style="width: 170px;">&nbsp;</td>
                                        <td style="width: 42px;">
                                            <img src="{{ asset('img/bt_delete.jpg') }}" alt="×" class="delbtn"
                                                onclick="return form.coverpage_delline({{ $i }});" />
                                        </td>
                                        <td style="width: 250px;">
                                            <input type="text" name="Reports[{{ $i }}][DOCUMENT_TITLE]"
                                                value="{{ old('Reports.' . $i . '.DOCUMENT_TITLE') }}"
                                                class="documenttitle{{ isset($document_error['DOCUMENT_TITLE']['NO'][$i]) ? ' error' : '' }}"
                                                style="width: 250px;" maxlength="30">
                                        </td>
                                        <td style="width: 100px;">
                                            <input type="text" name="Reports[{{ $i }}][DOCUMENT_NUMBER]"
                                                value="{{ old('Reports.' . $i . '.DOCUMENT_NUMBER') }}"
                                                class="documentnumber{{ isset($document_error['DOCUMENT_NUMBER']['NO'][$i]) ? ' error' : '' }}"
                                                style="width: 80px;" maxlength="7">
                                            &nbsp;部
                                        </td>
                                        <td style="width: 318px;">&nbsp;</td>
                                    </tr>
                                @endfor

                                <tr>
                                    <td colspan="5">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td colspan="2">
                                        <a href='#' onclick="return form.coverpage_addline(null);">
                                            <img src="{{ asset('img/bt_add.jpg') }}" alt="行を追加する">
                                        </a>
                                    </td>
                                    <td colspan="2">
                                        <a href='#' onclick="return form.f_reset('null');">
                                            <img src="{{ asset('img/bt_reset.jpg') }}" alt="リセット">
                                        </a>
                                    </td>
                                </tr>
                                <td colspan="4">
                                    <div id="file_upload">
                                        <table id="coverpage">

                                            <tr>
                                                <td colspan="5">&nbsp;</td>
                                            </tr>

                                            <tr>
                                                <td>&nbsp;</td>
                                                <td colspan="4">
                                                    <span class="must">
                                                        @if (isset($document_error) && $document_error['FLG'])
                                                            送付書類の中に入力エラーがあります<br>
                                                            @if (isset($document_error['EMP_FLG']) && $document_error['EMP_FLG'])
                                                                * 送付書類が一枚もありません<br>
                                                            @endif
                                                            @if (isset($document_error['DOCUMENT_TITLE']['OVER_FLAG']) && $document_error['DOCUMENT_TITLE']['OVER_FLAG'])
                                                                * 書類名は全角15文字以内<br>
                                                            @endif
                                                            @if (isset($document_error['DOCUMENT_TITLE']['EMP_FLAG']) && $document_error['DOCUMENT_TITLE']['EMP_FLAG'])
                                                                * 書類名が入力されていません<br>
                                                            @endif
                                                            @if ($document_error['DOCUMENT_NUMBER']['FLAG'])
                                                                * 枚数は半角数字1-7桁で入力してください<br>
                                                            @endif
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                </td>
                            </tbody>
                        </table>

                    </div>
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td colspan="5"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:250;">状況</th>
                            <td colspan="4">
                                <input type="checkbox" name="STATUS_ASAP">
                                至急&nbsp;&nbsp;
                                <input type="checkbox" name="STATUS_REFERENCE">
                                ご参考まで&nbsp;&nbsp;
                                <input type="checkbox" name="STATUS_COMFIRMATION">
                                ご確認ください&nbsp;&nbsp;
                                <input type="checkbox" name="STATUS_REPLY">
                                ご返信ください&nbsp;&nbsp;
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
                        </tr>
                        <tr>
                            <th style="width:250">連絡事項</th>
                            <td width="630" colspan="4">
                                <textarea name="CONTACT" class="textarea{{ $errors->has('CONTACT') ? ' error form-error' : '' }}" maxlength="600">{{ old('CONTACT') }}</textarea>
                                <br><span class="must">{{ $errors->first('CONTACT') }}</span>
                                <br><span class="usernavi">{{ $usernavi['NOTE'] }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Background Bottom" class="block">
            </div>
            <div class="edit_btn">
                <input type="image" src="{{ asset('img/bt_save.jpg') }}" alt="保存する" name="submit"
                    class="imgover">
                <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル" name="cancel"
                    class="imgover">
            </div>
        </form>
    </div>

@endsection
@section('script')
    <script language="JavaScript">
        var lastDate = '';
        var cal1 = new JKL.Calendar("calid", "{{$formType.$action}}Form", "data[{{$formType}}][DATE]");

        setInterval(function(){
            var date = $('input.cal.date').val();
            if(lastDate != date){
                lastDate = date;
                var calcDate = new Date(date);
                if(calcDate.getFullYear() >= 2024 || (calcDate.getFullYear() >= 2023 && calcDate.getMonth() >= 9)){
                    $('#TAXFRACTIONTIMING1').attr('disabled', true);
                    $('#TAXFRACTIONTIMING0').click();
                } else {
                    $('#TAXFRACTIONTIMING1').removeAttr('disabled', true);
                }
            }
        },1000);
    </script>
@endsection
