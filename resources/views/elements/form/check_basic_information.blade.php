{{-- resources/views/forms/show.blade.php --}}

@php
    $formType = $controller_name;
    $formID = '';
    $formController = '';
    $mailAction = '';

    switch ($controller_name) {
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
    <div class="edit_01">
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>
<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">
    <div class="contents_area">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="w100">管理番号</th>
                <td class="w320">{{ $param[$formType][$formID] ?? '' }}</td>
                <th class="w100">発行日</th>
                <td class="w320">{{ $param[$formType]['ISSUE_DATE'] ?? '' }}</td>
            </tr>
            <tr>
                <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="txt_top w100">件名</th>
                <td colspan="3">{{ $param[$formType]['SUBJECT'], 'Quote', 'SUBJECT' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="w100">顧客名</th>
                <td colspan="3">{{ isset($param['Customer']['NAME']) ? $param['Customer']['NAME'] : '' }}</td>
            </tr>
            <tr>
                <td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="w100">顧客担当者名</th>
                <td colspan="3">
                    @isset($param['CustomerCharge']['CHARGE_NAME'])
                        {{ $param['CustomerCharge']['CHARGE_NAME'], 'CustomerCharge', 'CHARGE_NAME' }}
                    @endisset
                </td>
            </tr>
            <tr>
                <td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="w100">自社担当者名</th>
                <td colspan="3">
                    @isset($param['Charge']['NAME'])
                        {{ $param['Charge']['NAME'], 'Charge', 'NAME' }}
                    @endisset
                </td>
            </tr>
            <tr>
                <td colspan="4" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="txt_top w100">敬称</th>
                <td colspan="3">
                    @if ($param[$formType]['HONOR_CODE'] == 2)
                        {{ $param[$formType]['HONOR_TITLE'] }}
                    @else
                        {{ $honor[$param[$formType]['HONOR_CODE']] }}
                    @endif
                </td>
            </tr>
            <tr>
                <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="txt_top w100">自社印押印設定</th>
                @dd($param[$formType]['CMP_SWAL_FLG'])
                <td colspan="3">{{ $seal_flg[$param[$formType]['CMP_SEAL_FLG']] }}</td>
            </tr>
            <tr>
                <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
            <tr>
                <th class="txt_top w100">担当者印押印設定</th>
                <td colspan="3">{{ $seal_flg[$param['CHR_SEAL_FLG']] }}</td>
            </tr>
            <tr>
                <td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>
        </table>
    </div>
    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Background Bottom" class="block">
</div>
