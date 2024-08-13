@extends('layout.default')

@section('content')

    @php
        $formType = $formType ?? 'Totalbill';
        $controller = strtolower($formType);
        $action = request()->route()->getActionMethod();
    @endphp

    <script type="text/javascript">
        <!--
        function customer_reset() {
            $('#SETCUSTOMER').children('input[type=text]').val('');
            $('#SETCUSTOMER').children('input[type=hidden]').val('');
            return false;
        }
        //
        -->
    </script>

    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session()->get('message') }}
        </div>
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/i_guide02.jpg') }}" alt="Guide">
            <p>1.　まず、対象とする作成済みの請求書を顧客名、発行日（日付from、to）の条件で絞込みます。<br /></p>
            <p>2.　条件で検索された請求書の中から対象となる請求書を選択し、作成ボタンを押します。<br />（合計請求書のフォーマットは「簡易」「詳細」から選択できます）<br /></p>
        </div>
    </div>
    <br class="clear" />

    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow">
        </div>
        <div class="search_box">
            <div class="search_area">
                <form method="POST" action="{{ route('totalbill.search') }}" class="Totalbill">
                    @csrf
                    <table width="600" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:250;">顧客名</th>
                            <td width="630" colspan="3" id="SETCUSTOMER">
                                <input type="text" name="CUSTOMER_NAME" class="w140 p2" readonly>
                                <input type="hidden" name="CST_ID">
                                <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                    <img src="{{ asset('img/bt_select2.jpg') }}" alt="Select">
                                </a>
                                <a href="#" onclick="return customer_reset();">
                                    <img src="{{ asset('img/bt_delete2.jpg') }}" alt="Delete">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>日付 FROM</th>
                            <td width="320">
                                <input type="text" name="FROM" id="data[{{$formType}}][FROM]" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('FROM').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                    onclick="return cal1.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('FROM').value = '';">
                            </td>
                        </tr>
                        <tr>
                            <th>日付 TO</th>
                            <td width="320">
                                <input type="text" name="data[{{$formType}}][TO]" id="TO" class="w100 p2 date cal" readonly>
                                <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime" onclick="document.getElementById('TO').value = new Date().toISOString().split('T')[0];">
                                <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5"
                                    onclick="return cal2.write();">
                                <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="現在" class="pl5 cleartime" onclick="document.getElementById('TO').value = '';">
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <input type="image" src="{{ asset('img/bt_search.jpg') }}" name="search" alt="検索する">
                    </div>
                </form>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block">
        </div>
        <div id="calid"></div>

        @if (isset($billlist) && is_array($billlist))
            <div class="listview hidebox">
                <h3>
                    <div class="edit_02_bill">
                        <span class="edit_txt">&nbsp;</span>
                    </div>
                </h3>
                <div class="contents_box mb40">
                    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background">
                    <div class="list_area">
                        <form method="POST" action="{{ route('totalbill.add') }}" class="Totalbill">
                            @csrf
                            <table width="900" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <th class="w50">選択</th>
                                    <th class="w150">請求書番号</th>
                                    <th class="w200">件名</th>
                                    <th class="w250">顧客名</th>
                                    <th class="w100">発行日</th>
                                    <th class="w100">振込期限</th>
                                    <th class="w100">合計金額</th>
                                </tr>
                                @foreach ($billlist as $i => $val)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="MBL_ID[{{ $val['Bill']['MBL_ID'] }}]"
                                                class="chk" id="check_{{ $i }}"
                                                value="{{ $val['Bill']['CHK'] ? '1' : '0' }}">
                                        </td>
                                        <td>{{ $val['Bill']['NO'] ?? '　' }}</td>
                                        <td>{{ $val['Bill']['SUBJECT'] }}</td>
                                        <td>{{ $val['Customer']['NAME'] }}</td>
                                        <td>{{ $val['Bill']['ISSUE_DATE'] ?? '　' }}</td>
                                        <td>{{ $val['Bill']['DUE_DATE'] }}</td>
                                        <td id="TOTAL{{ $i }}">{{ $val['Bill']['TOTAL'] }}</td>
                                        <div id="tax{{ $i }}" style="display:none;">
                                            {{ $val['Bill']['SALES_TAX'] }}</div>
                                        <div id="subtotal{{ $i }}" style="display:none;">
                                            {{ $val['Bill']['SUBTOTAL'] }}</div>
                                    </tr>
                                @endforeach
                            </table>
                    </div>
                    <div class="contents_area">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td colspan="4" class="line">
                                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                                </td>
                            </tr>
                            <tr>
                                <th>フォーマット</th>
                                <td width="320">
                                    <select name="EDIT_STAT" id="EDIT_STAT">
                                        @foreach ($edit_stat as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ old('EDIT_STAT', $totalbill->EDIT_STAT) == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <br /><span class="usernavi">{{ $usernavi['EDIT_STAT'] }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" class="line">
                                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                                </td>
                            </tr>
                        </table>
                        <div id="billfrag" style="display:none;">{{ $billfrag }}</div>
                        @if (isset($cst_name))
                            <input type="hidden" name="CUSTOMER_NAME" value="{{ $cst_name }}">
                        @endif
                        @if (isset($cst_id))
                            <input type="hidden" name="CST_ID" value="{{ $cst_id }}">
                        @endif
                    </div>
                    <div class="search_btn">
                        <input type="submit" name="select" value="選択する" alt="選択する">
                    </div>
                    </form>
                </div>
        @endif
    </div>

    <script>
        $(document).ready(function() {
            $('#FROM').datepicker({
                dateFormat: 'yy-mm-dd'
            });
            $('#TO').datepicker({
                dateFormat: 'yy-mm-dd'
            });
        });
    </script>
@endsection
@section('script')
    <script language="JavaScript">
        var lastDate = '';
        var cal1 = new JKL.Calendar("calid", "{{$formType.$action}}Form", "data[{{$formType}}][FROM]");
        var cal2 = new JKL.Calendar("calid", "{{$formType.$action}}Form", "data[{{$formType}}][TO]");

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
