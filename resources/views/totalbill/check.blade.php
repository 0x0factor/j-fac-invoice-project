@extends('layout.default')

@section('content')



<!-- Flash message -->
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="Guide">
        <p>こちらのページは合計請求書確認の画面です。<br />「編集する」ボタンを押すと合計請求書を編集できます。</p>
    </div>
</div>
<br class="clear" />

<!-- contents_Start -->
<div id="contents">

    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>
    <div class="edit_btn2">
        @if($editauth)
            <a href="{{ route('totalbills.edit', ['id' => $param['Totalbill']['TBL_ID']]) }}">
                <img src="{{ asset('img/bt_edit.jpg') }}" class="imgover" alt="編集する">
            </a>
        @endif
        <a href="{{ route('totalbills.download', ['id' => $param['Totalbill']['TBL_ID']]) }}">
            <img src="{{ asset('img/bt_download.jpg') }}" class="imgover" alt="ダウンロード">
        </a>
        <a href="{{ route('totalbills.preview', ['id' => $param['Totalbill']['TBL_ID']]) }}" target="_blank">
            <img src="{{ asset('img/bt_preview.jpg') }}" class="imgover" alt="プレビュー">
        </a>
        <form action="{{ route('totalbills.moveback') }}" method="post" style="display:inline;">
            @csrf
            <a href="javascript:move_to_index();">
                <img src="{{ asset('img/bt_index.jpg') }}" class="imgover" alt="一覧">
            </a>
        </form>
    </div>

    <h3>
        <div class="edit_01">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th class="w100">管理番号</th>
                    <td class="w320">{{ $customHtml->ht2br($param['Totalbill']['NO'], 'Totalbill', 'NO') }}</td>
                    <th class="w100">発行日</th>
                    <td class="w320">{{ $customHtml->df($param['Totalbill']['ISSUE_DATE']) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="w100">顧客名</th>
                    <td colspan="3">{{ $customHtml->ht2br($param['Customer']['NAME'], 'Customer', 'NAME') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="w100">顧客担当者名</th>
                    <td colspan="3">{{ isset($param['CustomerCharge']['CHARGE_NAME']) ? $customHtml->ht2br($param['CustomerCharge']['CHARGE_NAME'], 'CustomerCharge', 'CHARGE_NAME') : '' }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="line">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="w100">担当者部署名</th>
                    <td colspan="3">{{ isset($param['CustomerCharge']['UNIT']) ? $customHtml->ht2br($param['CustomerCharge']['UNIT'], 'CustomerCharge', 'UNIT') : '' }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="txt_top w100">敬称</th>
                    <td colspan="3">
                        @if(isset($param['Totalbill']['HONOR_CODE']))
                            @if($param['Totalbill']['HONOR_CODE'] == 2)
                                {{ $param['Totalbill']['HONOR_TITLE'] }}
                            @else
                                {{ $honor[$param['Totalbill']['HONOR_CODE']] }}
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="txt_top w100">件名</th>
                    <td colspan="3">{{ $customHtml->ht2br($param['Totalbill']['SUBJECT'], 'Bill', 'SUBJECT') }}</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                    </td>
                </tr>
                <tr>
                    <th class="txt_top w100">振込期限</th>
                    <td colspan="3">{{ $customHtml->ht2br($param['Totalbill']['DUE_DATE'], 'Bill', 'DUE_DATE') }}</td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
    </div>
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>
    <div class="listview">
        <h3>
            <div class="edit_02_bill">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="list_area">
                @if(isset($param['Bill']))
                    <table width="900" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th class="w150">請求書番号</th>
                            <th class="w200">件名</th>
                            <th class="w250">顧客名</th>
                            <th class="w100">発行日</th>
                            <th class="w100">振込期限</th>
                            <th class="w100">合計金額</th>
                        </tr>
                        @foreach($param['Bill'] as $key => $val)
                            <tr>
                                <td>{{ $customHtml->ht2br($val['Bill']['NO'] ?? '　', 'Totalbill', 'NO') }}</td>
                                <td>{{ $customHtml->ht2br($val['Bill']['SUBJECT'], 'Totalbill', 'NO') }}</td>
                                <td>{{ $customHtml->ht2br($val['Customer']['NAME'], 'Totalbill', 'NAME') }}</td>
                                <td>{{ $customHtml->ht2br($val['Bill']['ISSUE_DATE'], 'Totalbill', 'ISSUE_DATE') }}</td>
                                <td>{{ $customHtml->ht2br($val['Bill']['DUE_DATE'] ?? '　', 'Totalbill', 'DUE_DATE') }}</td>
                                <td id="TOTAL{{ $loop->index }}">{{ $customHtml->ht2br($val['Bill']['TOTAL'] ?? '　', 'Totalbill', 'TOTAL') }}</td>
                                <div id="tax{{ $loop->index }}" style="display:none;">{{ $customHtml->ht2br($val['Bill']['SALES_TAX']) }}</div>
                                <div id="subtotal{{ $loop->index }}" style="display:none;">{{ $customHtml->ht2br($val['Bill']['SUBTOTAL']) }}</div>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
        </div>
    </div>

    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>
    <div id="edit_stat" style="display:none;">{{ $customHtml->ht2br($param['Totalbill']['EDIT_STAT']) }}</div>

    <div class="listview hidebox_d">
        <h3>
            <div class="edit_02_abstract">
                <span class="edit_txt">&nbsp;</span>
            </div>
        </h3>

        <div class="contents_box mb40">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">

		</div>
	</div>
	<div class="edit_btn">
	</div>
</div>
<!-- contents_End -->
@endsection
