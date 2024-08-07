@extends('layout.default')

@section('content')

    <script type="text/javascript">
        function customer_reset() {
            $('#SETCUSTOMER').children('input[type=text]').val('');
            $('#SETCUSTOMER').children('input[type=hidden]').val('');
            return false;
        }

        function cstchr_reset() {
            $('#SETCUSTOMERCHARGE').children('input[type=text]').val('');
            $('#SETCUSTOMERCHARGE').children('input[type=text]').removeAttr('readonly')
            $('#SETCUSTOMERCHARGE').children('input[type=hidden]').val('');
            $('#SETCCUNIT').children('input[type=text]').val('');
            $('#SETCCUNIT').children('input[type=text]').removeAttr('readonly')
            return false;
        }

    </script>


    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
            <p>こちらのページは合計請求書作成の画面です。<br>必要な情報を入力の上「保存する」ボタンを押すと合計請求書を作成できます。</p>
        </div>
    </div>
    <br class="clear">

    <!-- contents_Start -->
    <div id="contents">
        <form action="{{ route('totalbill.add') }}" method="POST" class="Totalbill">
            @csrf
            <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
            <h3>
                <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
            </h3>
            <div class="contents_box">
                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
                <div class="contents_area">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>{{ $errors->has('NO') ? '管理番号' : '管理番号' }}</th>
                            <td width="320">
                                <input type="text" name="NO" value="{{ old('NO') }}"
                                    class="w180 p2{{ $errors->has('NO') ? ' error' : '' }}" maxlength="20">
                                <br><span class="usernavi">{{ $usernavi['NO'] }}</span>
                                <br><span class="must">{{ $errors->first('NO') }}</span>
                            </td>
                            <th>発行日</th>
                            <td width="320">
                                <input type="text" name="DATE" id="DATE" value="{{ old('DATE') }}"
                                    class="w100 p2 date cal{{ $errors->has('DATE') ? ' error' : '' }}" readonly
                                    onchange="cal1.getFormValue(); cal1.hide();">
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('DATE').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                    onclick="return cal1.write();">
                                <div id="calid"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td><span class="usernavi">{{ $usernavi['DATE'] }}</span><br><span
                                    class="must">{{ $errors->first('ISSUE_DATE') }}</span></td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th>件名</th>
                            <td colspan="3">
                                <input type="text" name="SUBJECT" value="{{ old('SUBJECT') }}"
                                    class="w320 mr10{{ $errors->has('SUBJECT') ? ' error' : '' }}" maxlength="80"
                                    onkeyup="count_str('subject_rest', value, 40)">
                                <span id="subject_rest"></span>
                                <br><span class="usernavi">{{ $usernavi['SUBJECT'] }}</span>
                                <br><span class="must">{{ $errors->first('SUBJECT') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th>顧客名</th>
                            <td id="SETCUSTOMER" colspan="3">
                                <input type="text" name="CUSTOMER_NAME" value="{{ old('CUSTOMER_NAME') }}"
                                    class="w130{{ $errors->has('CST_ID') ? ' error' : '' }}" readonly>
                                <input type="hidden" name="CST_ID">
                                <a href="#" onclick="return popupclass.popupajax('select_customer');"><img
                                        src="{{ asset('img/bt_select2.jpg') }}" alt="選択"></a>
                                <a href="#" onclick="return customer_reset();"><img
                                        src="{{ asset('img/bt_delete2.jpg') }}" alt="削除"></a>
                                <br><span class="usernavi">{{ $usernavi['CST_ID'] }}</span>
                                <br><span class="must">{{ $errors->first('CST_ID') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th>顧客担当者名</th>
                            <td id="SETCUSTOMERCHARGE" colspan="3">
                                <input type="text" name="CUSTOMER_CHARGE_NAME" value="{{ old('CUSTOMER_CHARGE_NAME') }}"
                                    class="w120 p2{{ isset($error['CUSTOMER_CHARGE_NAME']) ? ' error' : '' }}" readonly>
                                <input type="hidden" name="CHRC_ID">
                                <a href="#" onclick="return popupclass.popupajax('customer_charge');"><img
                                        src="{{ asset('img/bt_select2.jpg') }}" alt="選択"></a>
                                <a href="#" onclick="return cstchr_reset();"><img
                                        src="{{ asset('img/bt_delete2.jpg') }}" alt="削除"></a>
                                <br><span
                                    class="must">{{ isset($error['CUSTOMER_CHARGE_NAME']) ? $error['CUSTOMER_CHARGE_NAME'] : '' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th>敬称</th>
                            <td id="HONOR" colspan="3">
                                @foreach ($honor as $key => $value)
                                    <input type="radio" name="HONOR_CODE" value="{{ $key }}"
                                        class="ml20 mr5 txt_mid" {{ old('HONOR_CODE') == $key ? 'checked' : '' }}>
                                    {{ $value }}
                                @endforeach
                                <input type="text" name="HONOR_TITLE" value="{{ old('HONOR_TITLE') }}"
                                    class="w160 mr10{{ $errors->has('HONOR_TITLE') ? ' error' : '' }}" maxlength="8">
                                <br><span class="usernavi">{{ $usernavi['HONOR'] }}</span>
                                <br><span class="must">{{ $errors->first('HONOR_TITLE') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th>振込期限</th>
                            <td colspan="3">
                                <input type="text" name="DUE_DATE" value="{{ old('DUE_DATE') }}"
                                    class="w320 mr10{{ $errors->has('DUE_DATE') ? ' error' : '' }}" maxlength="20"
                                    onkeyup="count_str('duedate_rest', value, 20)">
                                <span id="duedate_rest"></span>
                                <br><span class="usernavi">{{ $usernavi['DUE_DATE'] }}</span>
                                <br><span class="must">{{ $errors->first('DUE_DATE') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
            </div>
            <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
            <div class="listview">
                <h3>
                    <div class="edit_02_bill"><span class="edit_txt">&nbsp;</span></div>
                </h3>
                <div class="contents_box mb40">
                    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
                    <div class="list_area">
                        @if (isset($billlist) && is_array($billlist))
                            <table width="900" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <th class="w150">請求書番号</th>
                                    <th class="w200">件名</th>
                                    <th class="w250">顧客名</th>
                                    <th class="w100">発行日</th>
                                    <th class="w100">振込期限</th>
                                    <th class="w100">合計金額</th>
                                </tr>
                                @foreach ($billlist as $val)
                                    <tr>
                                        <td>{{ $val['Bill']['NO'] ?? '　' }}</td>
                                        <td>{{ $val['Bill']['SUBJECT'] }}</td>
                                        <td>{{ $val['Customer']['NAME'] }}</td>
                                        <td>{{ $val['Bill']['ISSUE_DATE'] }}</td>
                                        <td>{{ $val['Bill']['DUE_DATE'] ?? '　' }}</td>
                                        <td>{{ $val['Bill']['SUM_PRICE'] }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @else
                            <p class="tac">請求書が登録されていません。</p>
                        @endif
                    </div>
                    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="">
                </div>
            </div>
            <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
            <div class="contents_foot">
                <button type="submit" name="SAVE" class="ml5">
                    <img src="{{ asset('img/bt_save.gif') }}" alt="保存する" class="ml5" alt="保存する">
                </button>
                <button type="reset" name="CLEAR" class="ml10">
                    <img src="{{ asset('img/bt_clear.gif') }}" alt="クリア" class="ml10" alt="クリア">
                </button>
                <button type="button" onclick="return location.href='{{ action('TotalbillController@index') }}'"
                    class="ml10">
                    <img src="{{ asset('img/bt_cancel.gif') }}" alt="キャンセル" class="ml10" alt="キャンセル">
                </button>
            </div>
        </form>
    </div>
    <!-- contents_End -->

@endsection
