{{-- resources/views/your-view.blade.php --}}
@php
    $formType = $name; // assuming $name is passed from the controller
@endphp

<h3>
    <div class="edit_04">
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box mb20">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Top Background">

    <div class="contents_area">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="w100">ステータス</th>
                <td class="w770">{{ $status[$param[$formType]['STATUS']] }}</td>
            </tr>
            <tr>
                <td colspan="2" class="line">
                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line">
                </td>
            </tr>
            <tr>
                <th class="w100">メモ</th>
                <td class="w770">
                    {!! $customHtml->ht2br($param[$formType]['MEMO'], $formType, 'MEMO') !!}
                </td>
            </tr>
        </table>
    </div>

    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Bottom Background">
</div>
