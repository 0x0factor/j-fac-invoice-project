@extends('layout.default')

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
    <!-- Flash message -->
    @if (session('flash_message'))
        <div class="flash-message">
            {{ session('flash_message') }}
        </div>
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
            <p>こちらのページは自社情報設定確認の画面です。
                @if ($user->AUTHORITY == 0)
                    <br>必要な情報を入力の上「編集する」ボタンを押下すると自社情報を変更できます。
                @endif
            </p>
        </div>
    </div>
    <br class="clear" />
    <!-- header_End -->

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_01"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;"><span class="float_l">自社名</span></th>
                        <td style="width:750px;">
                            {!! nl2br(e($company->NAME)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>代表者名</th>
                        <td style="width:750px;">
                            {!! nl2br(e($company->REPRESENTATIVE)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">郵便番号</th>
                        <td style="width:750px;">
                            @if (!empty($company->POSTCODE1) || !empty($company->POSTCODE2))
                                {!! nl2br(e($company->POSTCODE1 . '-' . $company->POSTCODE2)) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">都道府県</th>
                        <td style="width:750px;">
                            @if ($company->CNT_ID)
                                {{ $countys[$company->CNT_ID] ?? '' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">住所</th>
                        <td style="width:750px;">
                            {!! nl2br(e($company->ADDRESS)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">建物名</th>
                        <td style="width:750px;">
                            {!! nl2br(e($company->BUILDING)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">電話番号</th>
                        <td style="width:750px;">
                            @if (!empty($company->PHONE_NO1) || !empty($company->PHONE_NO2) || !empty($company->PHONE_NO3))
                                {!! nl2br(e($company->PHONE_NO1 . ' - ' . $company->PHONE_NO2 . ' - ' . $company->PHONE_NO3)) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">FAX番号</th>
                        <td style="width:750px;">
                            @if (!empty($company->FAX_NO1) && !empty($company->FAX_NO2) && !empty($company->FAX_NO3))
                                {!! nl2br(e($company->FAX_NO1 . ' - ' . $company->FAX_NO2 . ' - ' . $company->FAX_NO3)) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">登録番号</th>
                        <td style="width:750px;">
                            @if (!empty($company->INVOICE_NUMBER))
                                {!! nl2br(e($company->INVOICE_NUMBER)) !!}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">敬称</th>
                        <td style="width:750px;">
                            @if ($company->HONOR_CODE == 2)
                                {{ $company->HONOR_TITLE }}
                            @else
                                {{ $honor[$company->HONOR_CODE] ?? '' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th>社判登録<br /></th>
                        <td>
                            @if (isset($image))
                                <img src="{{ asset('img/companies/contents.jpg') }}" width="100" height="100"
                                    alt="Company Seal">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                        </td>
                    </tr>
                    <tr>
                        <th style="width:130px;">押印設定</th>
                        <td style="width:750px;">
                            {{ $seal_flg[$company->CMP_SEAL_FLG] ?? '' }}
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_02"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;">締日</th>
                        <td style="width:750px;">
                            {{ $company->CUTOOFF_DATE ? $company->CUTOOFF_DATE . '日' : '末日' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th @error('PAYMENT_DAY') class="txt_top" @enderror>支払日</th>
                        <td>
                            {{ $company->PAYMENT_MONTH != null ? $payment[$company->PAYMENT_MONTH] : '' }}
                            {{ $payment_select[$company->PAYMENT_SELECT] ?? '' }}
                            {{ $company->PAYMENT_DAY ? $company->PAYMENT_DAY . '日' : '' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>数量小数部表示</th>
                        <td>{{ $decimals[$company->DECIMAL_QUANTITY] ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>単価小数部表示</th>
                        <td>{{ $decimals[$company->DECIMAL_UNITPRICE] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>消費税設定</th>
                        <td>{{ $excises[$company->EXCISE] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>消費税端数処理</th>
                        <td>{{ $fractions[$company->TAX_FRACTION] ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>消費税端数計算</th>
                        <td>{{ $tax_fraction_timing[$company->TAX_FRACTION_TIMING] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>基本端数処理</th>
                        <td>{{ $fractions[$company->FRACTION] ?? '' }}</td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_03"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;">名義</th>
                        <td style="width:750px;">
                            {!! nl2br(e($company->ACCOUNT_HOLDER)) !!}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>銀行名</th>
                        <td>{!! nl2br(e($company->BANK_NAME)) !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>支店名</th>
                        <td>{!! nl2br(e($company->BANK_BRANCH)) !!}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>口座区分</th>
                        <td>{{ $company->ACCOUNT_TYPE ? $account_type[$company->ACCOUNT_TYPE] : '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th>口座番号</th>
                        <td>{!! nl2br(e($company->ACCOUNT_NUMBER)) !!}</td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_04"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;">枠色</th>
                        <td style="width:750px;">
                            {{ $colors[$company->COLOR] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th style="width:130px;">方向</th>
                        <td style="width:750px;">
                            {{ $direction[$company->DIRECTION] ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                alt="Line"></td>
                    </tr>
                    <tr>
                        <th style="width:130px;">連番設定</th>
                        <td style="width:750px;">
                            {{ $serial_option[$company->SERIAL_NUMBER] ?? '' }}
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Contents Bottom">
        </div>

        <div class="edit_btn">
            @if ($user['AUTHORITY'] == 0)
                <a href="{{ route('companies.edit', ['id' => $company->id]) }}">
                    <img src="{{ asset('img/bt_edit.jpg') }}" class="imgover" alt="編集する">
                </a>
            @endif
        </div>
    </div>

    <!-- Hidden field -->
    <input type="hidden" name="CMP_ID" value="{{ $company->id }}">
@endsection
