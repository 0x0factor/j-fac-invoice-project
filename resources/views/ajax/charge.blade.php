<!-- CSS -->
<link rel="stylesheet" href="{{ asset('css/popup.css') }}">

<!-- JavaScript -->
@push('scripts')

<script>
    function insert(no) {
        $('#SETCHARGE input[type=text]').val($('#name'+no).html());
        $('#SETCHARGE input[type=hidden]').val($('#user'+no+' input#CHR_ID').val());
        $('#SET_CHR_SEAL_FLG input[type=radio]').prop('checked', false);

        if ($('#user'+no+' input#TMP_CHR_SEAL_FLG').val() == 1) {
            $('#SET_CHR_SEAL_FLG input[type=radio]:first').prop('checked', true);
        } else {
            $('#SET_CHR_SEAL_FLG input[type=radio]:last').prop('checked', true);
        }

        popupclass.popup_close();
        return false;
    }

    var url = "{{ url('/ajax/popup') }}";

    function paging(page) {
        var param = {
            type: "charge",
            page: page
        };

        // Add keyword if available
        if ($("#CHR_KEYWORD").val()) {
            param.keyword = $("#CHR_KEYWORD").val();
        }

        // Add sorting parameters if available
        if ($("#sort").val()) {
            param.sort = $("#sort").val();
            param.desc = $("#desc").val();
        }

        $.post(url, { params: param }, function(d) {
            $('#popup').html(d);
        });
    }

    var sortBy = {
        CHARGE_NAME_KANA: 0,
        LAST_UPDATE: 0
    };

    function sorting(sort) {
        var param = {
            type: "charge",
            sort: sort,
            desc: sortBy[sort]
        };

        // Add keyword if available
        if ($("#CHR_KEYWORD").val()) {
            param.keyword = $("#CHR_KEYWORD").val();
        }

        $.post(url, { params: param }, function(d) {
            $('#popup').html(d);
            sortBy["CHARGE_NAME_KANA"] = 0;
            sortBy["LAST_UPDATE"] = 1;
            sortBy[sort] = 1 - param["desc"];
        });
    }
</script>
@endpush
<!-- Inline Styles -->
<style>
    table.tbl {
        border: 1px #E3E3E3 solid;
        border-collapse: collapse;
        border-spacing: 0;
        margin: 10px auto;
    }

    table.tbl tr.bgti {
        background: #EEEEEE;
    }

    table.tbl tr.bgcl {
        background: #F5F5F5;
    }

    table.tbl td {
        padding: 10px;
        border: 1px #E3E3E3 solid;
        border-width: 0 0 1px 1px;
    }

    table.tbl td.left {
        text-align: left;
    }

    table.tbl td.center {
        text-align: center;
    }

    td.w260 {
        width: 360px;
    }
</style>

<!-- HTML Form and Table -->
<form id="popupCharge">
    <div id="popup_contents">
        <img src="{{ asset('/img/popup/tl_charge.jpg') }}" alt="Charge Popup Title">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="503" cellpadding="0" cellspacing="0" border="0" class="tbl">
                    <tr>
                        <td colspan="4" class="w40 center">
                            {{ $nowpage }}
                            {{ $paging }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            <a href="javascript:void(0)" onclick="sorting('CHARGE_NAME_KANA')">担当者名(カナ)</a> /
                            <a href="javascript:void(0)" onclick="sorting('LAST_UPDATE')">更新日順</a>
                            {{ Form::text('CHR_KEYWORD', null, ['class' => 'w120 p2']) }}
                            <a href="#" onclick="return popupclass.popupajax('charge')">
                                <img src="{{ asset('img/bt_search.jpg') }}" alt="Search">
                            </a>
                        </td>
                    </tr>
                    <tr class="bgti">
                        <td class="w40"></td>
                        <td class="W140">名前</td>
                        <td class="W140">部署名</td>
                        <td>作成者</td>
                    </tr>
                    @foreach ($charge as $key => $value)
                        <tr class="{{ ($loop->iteration % 2 == 1) ? 'bgcl' : '' }}">
                            <td class="w40 center" id="user{{ $key }}">
                                <a href="#" onclick="return insert({{ $key }})">
                                    <img src="{{ asset('img/bt_insert.jpg') }}" alt="Insert">
                                </a>
                                {{ Form::hidden('CHR_ID', $value['Charge']['CHR_ID']) }}
                                {{ Form::hidden('TMP_CHR_SEAL_FLG', $value['Charge']['CHR_SEAL_FLG']) }}
                            </td>
                            <td class="w140" id="name{{ $key }}">{{ $value['Charge']['CHARGE_NAME'] }}</td>
                            <td class="w140">{{ $value['Charge']['UNIT'] }}</td>
                            <td>{{ $value['User']['NAME'] }}</td>
                        </tr>
                    @endforeach
                    @if ($paging)
                        <tr>
                            <td colspan="3" class="w40 center">
                                {{ $paging }}
                            </td>
                        </tr>
                    @endif
                </table>
                <div class="save_btn">
                    {{ Form::hidden('sort') }}
                    {{ Form::hidden('desc') }}
                    <a href="#" onclick="return popupclass.popup_close()">
                        <img src="{{ asset('img/bt_cancel_s.jpg') }}" alt="Cancel">
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
