@extends('layout.default')

@section('content')

<script type="text/javascript">
<!--
	function customer_reset() {
		$('#SETCUSTOMER').children('input[type=text]').val('');
		$('#SETCUSTOMER').children('input[type=hidden]').val('');
		return false;
	}
// -->
</script>


{{-- Display flash message --}}
@if (session('flash_message'))
    <div class="alert alert-success">
        {{ session('flash_message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
        <p>1.　まず、対象とする作成済みの請求書を顧客名、発行日（日付from、to）の条件で絞込みます。<br /></p>
        <p>2.　条件で検索された請求書の中から対象となる請求書を選択し、作成ボタンを押します。<br />（合計請求書のフォーマットは「簡易」「詳細」から選択できます）<br /></p>
    </div>
</div>
<br class="clear" />
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
    </div>
    <div class="search_box">
        <div class="search_area">
            <form action="{{ route('totalbills.store') }}" method="post" class="Totalbill">
                @csrf
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:250px;" class="{{ $errors->has('NO') ? 'txt_top' : '' }}">顧客名</th>
                        <td width="630" colspan="3" id="SETCUSTOMER">
                            <input type="text" name="CUSTOMER_NAME" class="w140 p2" readonly>
                            <input type="hidden" name="CST_ID">
                            <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                <img src="{{ asset('img/bt_select2.jpg') }}" alt="">
                            </a>
                            <a href="#" onclick="return customer_reset();">
                                <img src="{{ asset('img/bt_delete2.jpg') }}" alt="">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>日付 FROM</th>
                        <td width="320">
                            <script>
                                var cal1 = new JKL.Calendar("calid", "TotalbillEditForm", "data[Totalbill][FROM]");
                            </script>
                            <input type="text" name="FROM" class="w100 p2 date cal" readonly onChange="cal1.getFormValue(); cal1.hide();">
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal1.write();">
                            <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="リセット" class="pl5 cleartime">
                        </td>
                    </tr>
                    <tr>
                        <th>日付 TO</th>
                        <td width="320">
                            <script>
                                var cal2 = new JKL.Calendar("calid", "TotalbillEditForm", "data[Totalbill][TO]");
                            </script>
                            <input type="text" name="TO" class="w100 p2 date cal" readonly onChange="cal2.getFormValue(); cal2.hide();">
                            <img src="{{ asset('img/bt_now.jpg') }}" alt="現在" class="pl5 nowtime">
                            <img src="{{ asset('img/bt_calender.jpg') }}" alt="カレンダー" class="pl5" onclick="return cal2.write();">
                            <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="リセット" class="pl5 cleartime">
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <input type="hidden" name="TBL_ID" value="{{ $tbl_id }}">
                    <button type="submit" name="search">
                        <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                    </button>
                </div>
            </form>
        </div>
        <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
    </div>
    <div id="calid"></div>

    <div class="listview hidebox">
        <h3>
            <div class="edit_02_bill"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box mb40">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="list_area">
                @if (isset($billlist) && is_array($billlist))
                    <form action="{{ url('totalbills/edit') }}" method="post" class="Totalbill">
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
                                        <input type="checkbox" name="MBL_ID[]" value="{{ $val['Bill']['MBL_ID'] }}" class="chk" id="check_{{ $i }}" {{ isset($val['Bill']['CHK']) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $val['Bill']['NO'] ?? '　' }}</td>
                                    <td>{{ $val['Bill']['SUBJECT'] }}</td>
                                    <td>{{ $val['Customer']['NAME'] }}</td>
                                    <td>{{ $val['Bill']['ISSUE_DATE'] }}</td>
                                    <td>{{ $val['Bill']['DUE_DATE'] ?? '　' }}</td>
                                    <td id="TOTAL{{ $i }}">{{ $val['Bill']['TOTAL'] }}</td>
                                    <div id="tax{{ $i }}" style="display:none;">{{ $val['Bill']['SALES_TAX'] }}</div>
                                </tr>
                            @endforeach
                        </table>
                    </form>
                @endif
            </div>
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td colspan="4" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <th>フォーマット</th>
                        <td width="320">
                            <select name="EDIT_STAT">
                                @foreach ($edit_stat as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <br /><span class="usernavi">{{ $usernavi['EDIT_STAT'] }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="line">
                            <img src="{{ asset('img/i_line_solid.gif') }}" alt="">
                        </td>
                    </tr>
                </table>
                <div id="billfrag" style="display:none;">{{ $billfrag }}</div>
                <input type="hidden" name="CUSTOMER_NAME" value="{{ $cst_name ?? '' }}">
                <input type="hidden" name="CST_ID" value="{{ $cst_id ?? '' }}">
                <input type="hidden" name="TBL_ID" value="{{ $tbl_id }}">
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
            <div class="search_btn">
                <button type="submit" name="select" class="btn btn-primary">作成する</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection
