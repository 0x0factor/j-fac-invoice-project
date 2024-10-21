{{-- resources/views/your-view.blade.php --}}
@php
    $formType = $controller_name; // assuming $name is passed from the controller
    $formID = '';
    $formController = '';
    $mailAction = '';

    switch ($formType) {
        case 'Quote':
            $formID = 'MQT_ID';
            $formController = 'quotes';
            $mailAction = 'quote';
            break;
        case 'Bill':
            $formID = 'MBL_ID';
            $formController = 'bills';
            $mailAction = 'bill';
            break;
        case 'Delivery':
            $formID = 'MDV_ID';
            $formController = 'deliveries';
            $mailAction = 'delivery';
            break;
    }
@endphp

<h3>
    <div class="edit_02">
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Top Background">

    <div class="check_area">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="w50">No.</th>
                <th class="w125">商品コード</th>
                <th class="w235">品目名</th>
                <th class="w85">数量</th>
                <th class="w75">単位</th>
                <th class="w125">単価</th>
                <th class="w185">金額</th>
            </tr>
            @php
                $taxClass = ['', '内 ', '', '非 '];
            @endphp

        </table>
    </div>

    <div class="contents_area3">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            @if (isset($param['REDUCED_RATE_TOTAL']) && $param['REDUCED_RATE_TOTAL'])
                <tr>
                    <td colspan="8">「※」は軽減税率対象であることを示します。</td>
                </tr>
            @endif
            <tr>
                <td colspan="8">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" class="pb5" alt="Line Dot">
                </td>
            </tr>
            <tr>
                <th>割引設定</th>
                <td colspan="3">
                    @if (isset($param['DISCOUNT_TYPE']))
                        @if ($param['DISCOUNT_TYPE'] == 1)
                            @if(isset($param['DISCOUNT']) && $param['DISCOUNT'])
                                {{ number_format($param['DISCOUNT']) . '円引き' }}
                            @else
                                　
                            @endif
                        @elseif ($param['DISCOUNT_TYPE'] == 0)
                            {{ $param['DISCOUNT'] ? number_format($param['DISCOUNT']) . '％引き' : '　' }}
                        @else
                            　
                        @endif
                    @else
                        　
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="4" class="line">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" alt="Line Dot">
                </td>
            </tr>
            <tr>
                <th class="w100">数量小数表示</th>
                <td class="w240">{{ $decimals[$param['DECIMAL_QUANTITY'] ?? 0] ?? 'N/A' }}</td>
                <th class="w100">単価小数表示</th>
                <td class="w440">{{ $decimals[$param['DECIMAL_UNITPRICE'] ?? 0] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="line">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" alt="Line Dot">
                </td>
            </tr>
            <tr>
                <th class="w100">消費税設定</th>
                <td class="w240">{{ $excises[$param['EXCISE'] ?? ''] ?? 'N/A' }}</td>
                <th class="w100">消費税端数処理</th>
                <td class="w440">{{ isset($param['TAX_FRACTION']) && isset($fractions[$param['TAX_FRACTION']]) ? $fractions[$param['TAX_FRACTION']] : 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="line">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" alt="Line Dot">
                </td>
            </tr>
            <tr>
                <th class="w100">消費税端数計算</th>
                <td class="w240">{{ isset($param['TAX_FRACTION_TIMING']) && isset($tax_fraction_timing[$param['TAX_FRACTION_TIMING']]) ? $tax_fraction_timing[$param['TAX_FRACTION_TIMING']] : 'N/A' }}</td>
                <th class="w100">基本端数処理</th>
                <td class="w440">{{ isset($param['FRACTION']) && isset($fractions[$param['FRACTION']]) ? $fractions[$param['FRACTION']] : 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="line">
                    <img src="{{ asset('img/i_line_dot2.gif') }}" alt="Line Dot">
                </td>
            </tr>
            <tr>
                <td class="pt10">
                    <img src="{{ asset('img/i_subtotal.jpg') }}" alt="小計">
                </td>
                <td class="pt10">
                    {{ isset($param['SUBTOTAL']) ? $param['SUBTOTAL'] : 'N/A' }}円
                </td>
                <td class="pt10">
                    <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税">
                </td>
                <td class="pt10">
                    {{ isset($param['SALES_TAX']) ? $param['SALES_TAX'] : 'N/A' }}円
                </td>
            </tr>
            <tr>
                <td class="pt10">
                    <img src="{{ asset('img/i_total.jpg') }}" alt="合計">
                </td>
                <td colspan="3" class="pt10">
                    {{ isset($param['TOTAL']) ? $param['TOTAL'] : 'N/A' }}円
                </td>
            </tr>
            @if (isset($param['tax_kind_count']) && $param['tax_kind_count'] >= 1)
                <tr>
                    <td colspan="8">
                        <img src="{{ asset('img/i_line_dot2.gif') }}" class="pb5" alt="Line Dot">
                    </td>
                </tr>

                @if (isset($param['TEN_RATE_TOTAL']) && $param['TEN_RATE_TOTAL'])
                    <tr>
                        <td class="pt10">
                            <img src="{{ asset('img/button/i_10_tax.jpg') }}" alt="10%対象">
                        </td>
                        <td class="pt10">
                            {{ isset($param['TEN_RATE_TOTAL']) ? $param['TEN_RATE_TOTAL'] : 'N/A' }}円
                        </td>
                        <td class="pt10">
                            <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税">
                        </td>
                        <td class="pt10">
                            {{ isset($param['TEN_RATE_TAX']) ? $param['TEN_RATE_TAX'] : 'N/A' }}円
                        </td>
                    </tr>
                @endif

                @if (isset($param['REDUCED_RATE_TOTAL']) && $param['REDUCED_RATE_TOTAL'])
                    <tr>
                        <td class="pt10">
                            <img src="{{ asset('img/button/i_reduced_tax.jpg') }}" alt="8%(軽減)対象">
                        </td>
                        <td class="pt10">
                            {{ isset($param['REDUCED_RATE_TOTAL']) ? $param['REDUCED_RATE_TOTAL'] : 'N/A' }}円
                        </td>
                        <td class="pt10">
                            <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税">
                        </td>
                        <td class="pt10">
                            {{ isset($param['REDUCED_RATE_TAX']) ? $param['REDUCED_RATE_TAX'] : 'N/A' }}円
                        </td>
                    </tr>
                @endif

                @if (isset($param['EIGHT_RATE_TOTAL']) && $param['EIGHT_RATE_TOTAL'])
                    <tr>
                        <td class="pt10">
                            <img src="{{ asset('img/button/i_8_tax.jpg') }}" alt="8%対象">
                        </td>
                        <td class="pt10">
                            {{ isset($param['EIGHT_RATE_TOTAL']) ? $param['EIGHT_RATE_TOTAL'] : 'N/A' }}円
                        </td>
                        <td class="pt10">
                            <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税">
                        </td>
                        <td class="pt10">
                            {{ isset($param['EIGHT_RATE_TAX']) ? $param['EIGHT_RATE_TAX'] : 'N/A' }}円
                        </td>
                    </tr>
                @endif

                @if (isset($param['FIVE_RATE_TOTAL']) && $param['FIVE_RATE_TOTAL'])
                    <tr>
                        <td class="pt10">
                            <img src="{{ asset('img/button/i_5_tax.jpg') }}" alt="5%対象">
                        </td>
                        <td class="pt10">
                            {{ isset($param['FIVE_RATE_TOTAL']) ? $param['FIVE_RATE_TOTAL'] : 'N/A' }}円
                        </td>
                        <td class="pt10">
                            <img src="{{ asset('img/i_tax.jpg') }}" alt="消費税">
                        </td>
                        <td class="pt10">
                            {{ isset($param['FIVE_RATE_TAX']) ? $param['FIVE_RATE_TAX'] : 'N/A' }}円
                        </td>
                    </tr>
                @endif
            @endif
        </table>
    </div>

    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Bottom Background">
</div>
