@extends('layout.default')

@section('content')
<!-- Laravel Blade View -->

{{-- 完了メッセージ --}}
{{ session()->flash() }}

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
        <p>こちらのページは顧客情報確認の画面です。<br>「編集する」ボタンを押すと顧客情報を編集することができます。</p>
    </div>
</div>

<br class="clear">

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>
    <div class="edit_btn2">
        @if($editauth)
            <a href="{{ url('customers/edit/'.$cstID) }}" class="imgover">
                <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
            </a>
        @endif
        <form action="{{ url('moveback') }}" method="post" style="display:inline;">
            @csrf
            <a href="javascript:move_to_index();" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
            </a>
        </form>
    </div>

    <h3><div class="edit_01"><span class="edit_txt">&nbsp;</span></div></h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th style="width:130px;">社名</th>
                    <td style="width:750px;">{{ nl2br(e($customer['NAME'])) }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">社名カナ</th>
                    <td style="width:750px;">{{ nl2br(e($customer['NAME_KANA'])) }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">敬称</th>
                    <td style="width:750px;">
                        @if($customer['HONOR_CODE'] == 2)
                            {{ $customer['HONOR_TITLE'] }}
                        @else
                            {{ $honor[$customer['HONOR_CODE']] }}
                        @endif
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">郵便番号</th>
                    <td style="width:750px;">
                        @if(!empty($customer['POSTCODE1']) || !empty($customer['POSTCODE2']))
                            {{ e($customer['POSTCODE1']." - ".$customer['POSTCODE2']) }}
                        @endif
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">都道府県</th>
                    <td style="width:750px;">
                        {{ $customer['CNT_ID'] ? nl2br(e($countys[$customer['CNT_ID']])) : '' }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">住所</th>
                    <td style="width:750px;">{{ nl2br(e($customer['ADDRESS'])) }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">建物名</th>
                    <td style="width:750px;">{{ nl2br(e($customer['BUILDING'])) }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">電話番号</th>
                    <td style="width:750px;">
                        @if(!empty($customer['PHONE_NO1']) || !empty($customer['PHONE_NO2']) || !empty($customer['PHONE_NO3']))
                            {{ e($customer['PHONE_NO1']." - ".$customer['PHONE_NO2']." - ".$customer['PHONE_NO3']) }}
                        @endif
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">FAX番号</th>
                    <td style="width:750px;">
                        {{ e(($customer['FAX_NO1'] && $customer['FAX_NO2'] && $customer['FAX_NO3']) ? $customer['FAX_NO1']." - ".$customer['FAX_NO2']." - ".$customer['FAX_NO3'] : '') }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">ホームページ</th>
                    <td style="width:750px;">
                        @if($customer['WEBSITE'])
                            <a href="{{ e($customer['WEBSITE']) }}">{{ nl2br(e($customer['WEBSITE'])) }}</a>
                        @endif
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">自社担当者</th>
                    <td style="width:750px;">{{ nl2br(e($charge)) }}</td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>

    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>

    <h3><div class="company_02"><span class="edit_txt">&nbsp;</span></div></h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
        <div class="contents_area">
            <table width="880" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <th style="width:130px;">締日</th>
                    <td style="width:750px;">
                        {{ $cutooff_select[$customer['CUTOOFF_SELECT']] }}
                        {{ nl2br(e($customer['CUTOOFF_DATE'] ? $customer['CUTOOFF_DATE']."日" : '')) }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">支払日</th>
                    <td style="width:750px;">
                        {{ ($customer['PAYMENT_MONTH'] || $customer['PAYMENT_MONTH'] != NULL) ? $payment[$customer['PAYMENT_MONTH']] : '' }}
                        {{ $payment_select[$customer['PAYMENT_SELECT']] }}
                        {{ nl2br(e($customer['PAYMENT_DAY'] ? $customer['PAYMENT_DAY']."日" : '')) }}
                    </td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">消費税設定</th>
                    <td style="width:750px;">{{ $excises[$customer['EXCISE']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                <tr>
                    <th>消費税端数処理</th>
                    <td>{{ $fractions[$customer['TAX_FRACTION']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                <tr>
                    <th>消費税端数計算</th>
                    <td>{{ $tax_fraction_timing[$customer['TAX_FRACTION_TIMING']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                <tr>
                    <th style="width:130px;">基本端数処理</th>
                    <td style="width:750px;">{{ $fractions[$customer['FRACTION']] }}</td>
                </tr>
                <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>

                <tr>
                    <th style="width:130px;">備考</th>
                    <td style="width:750px;">{{ nl2br(e($customer['NOTE'])) }}</td>
                </tr>

            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
    </div>

    <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt=""></div>

    <div class="edit_btn2">
        @if($editauth)
            <a href="{{ url('customers/edit/'.$cstID) }}" class="imgover">
                <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
            </a>
        @endif
        <a href="{{ url('customers/index') }}" class="imgover">
            <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
        </a>
    </div>

</div>
@endsection
