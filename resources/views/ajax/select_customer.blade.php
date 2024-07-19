<link href="{{ asset('css/popup.css') }}" rel="stylesheet">

<script>
    function insert(no) {
        $('#SETCUSTOMER').find('input[type=text]').val($('#name'+no).html());
        $('#SETCUSTOMER').find('input[type=hidden]').val($('#id'+no).find('input#CST_ID').val());

        $('#SETCHARGE').find('input[type=text]').val($('#id'+no).find('input#CHARGE_NAME').val());
        $('#SETCHARGE').find('input[type=hidden]').val($('#id'+no).find('input#CHR_ID').val());

        if ($('#EXCISE').html()) {
            $('#EXCISE').find('input[type=radio]').val([$('#id'+no).find('input#EXCISE').val()]);
            $('#FRACTION').find('input[type=radio]').val([$('#id'+no).find('input#FRACTION').val()]);
            $('#TAX_FRACTION').find('input[type=radio]').val([$('#id'+no).find('input#TAX_FRACTION').val()]);
            $('#TAX_FRACTION_TIMING').find('input[type=radio]').val([$('#id'+no).find('input#TAX_FRACTION_TIMING').val()]);
            $('#HONOR').find('input[type=text]').val([$('#id'+no).find('input#HONOR_TITLE').val()]);
            $('#HONOR').find('input[type=radio]').val([$('#id'+no).find('input#HONOR_CODE').val()]);
            form.f_subtotal();
        }

        var address = $('#id'+no).find('input#C_POSTCODE').val() + " " + $('#id'+no).find('input#C_SEARCH_ADDRESS').val();
        $("#INSERT_ADDRESS").html('<a href="javascript:void(0)" onclick="insert_address('+ no +');" >'+ address +'</a>');
        popupclass.popup_close();
        return false;
    }

    function insert_address(no) {
        $("#CustomerChargeADDRESS").val($('#id'+no).find('input#C_ADDRESS').val());
        $("#CustomerChargePOSTCODE1").val($('#id'+no).find('input#C_POSTCODE1').val());
        $("#CustomerChargePOSTCODE2").val($('#id'+no).find('input#C_POSTCODE2').val());
        $("#CustomerChargeCNTID").val($('#id'+no).find('input#C_CNT_ID').val());
        $("#CustomerChargeBUILDING").val($('#id'+no).find('input#C_BUILDING').val());
        $("#INSERT_ADDRESS").html("");
    }

    var url = "{{ url('/ajax/popup') }}";

    function paging(page) {
        var param = {
            "type": "select_customer",
            "page": page
        };

        if ($("#CST_KEYWORD").val()) {
            param["keyword"] = $("#CST_KEYWORD").val();
        }

        if ($("#sort").val()) {
            param["sort"] = $("#sort").val();
            param["desc"] = $("#desc").val();
        }

        $.post(url, { params: param }, function(d) {
            $('#popup').html(d);
        });
    }

    var sortBy = {
        "NAME_KANA": 0,
        "LAST_UPDATE": 0
    };

    function sorting(sort) {
        var param = {
            "type": "select_customer",
            "sort": sort,
            "desc": sortBy[sort]
        };

        if ($("#CST_KEYWORD").val()) {
            param["keyword"] = $("#CST_KEYWORD").val();
        }

        $.post(url, { params: param }, function(d) {
            $('#popup').html(d);
            sortBy["NAME_KANA"] = 0;
            sortBy["LAST_UPDATE"] = 1;
            sortBy[sort] = 1 - param["desc"];
        });
    }
</script>

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
    }

    table.tbl td.left {
        text-align: left;
    }

    table.tbl td.center {
        text-align: center;
    }

    a.lmargin40 {
        margin-left: 40px;
    }

    a.lmargin20 {
        margin-left: 20px;
    }
</style>

<form id="popupCharge">
    <div id="popup_contents">
        <img src="{{ asset('/img/popup/tl_customer.jpg') }}">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="500" cellpadding="0" cellspacing="0" border="0" class="tbl">
                    <tr>
                        <td colspan="{{ ($user['AUTHORITY'] != 1) ? '3' : '2' }}" class="w40 center">
                            @if ($user['AUTHORITY'] != 1)
                                <a href="#" onclick="return popupclass.popupajax('customer');" class="float_l lmargin20">
                                    <img src="{{ asset('bt_new2.jpg') }}" alt="">
                                </a>
                            @else
                                <a href="#" onclick="return popupclass.popupajax('customer');" class="float_l lmargin40">
                                    <img src="{{ asset('bt_new2.jpg') }}" alt="">
                                </a>
                            @endif
                            {{ $nowpage }}
                            {{ $paging }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="{{ ($user['AUTHORITY'] != 1) ? '3' : '2' }}">
                            <a href="javascript:void(0)" onclick="return sorting('NAME_KANA');">顧客名(カナ)</a>/<a href="javascript:void(0)" onclick="return sorting('LAST_UPDATE');">更新日順</a>
                            <input type="text" name="CST_KEYWORD" class="w200 p2">
                            <a href="#" onclick="return popupclass.popupajax('select_customer');">
                                <img src="{{ asset('bt_search.jpg') }}" alt="">
                            </a>
                        </td>
                    </tr>
                    <tr class="bgti">
                        <td class="w40"></td>
                        <td {{ ($user['AUTHORITY'] != 1) ? 'class="w80"' : 'class="w120"' }}>顧客名</td>
                        @if ($user['AUTHORITY'] != 1)
                            <td class="w100">作成者</td>
                        @endif
                    </tr>
                    @foreach ($customer as $key => $value)
                        <tr {{ ($loop->odd) ? 'class="bgcl"' : '' }}>
                            <td class="center" id="id{{ $key }}">
                                <a href="#" onclick="return insert({{ $key }});" class="float_l lmargin20">
                                    <img src="{{ asset('bt_insert.jpg') }}" alt="">
                                </a>
                                <input type="hidden" name="CST_ID" value="{{ $value['Customer']['CST_ID'] }}">
                                <input type="hidden" name="EXCISE" value="{{ $value['Customer']['EXCISE'] }}">
                                <input type="hidden" name="FRACTION" value="{{ $value['Customer']['FRACTION'] }}">
                                <input type="hidden" name="TAX_FRACTION" value="{{ $value['Customer']['TAX_FRACTION'] }}">
                                <input type="hidden" name="TAX_FRACTION_TIMING" value="{{ $value['Customer']['TAX_FRACTION_TIMING'] }}">
                                <input type="hidden" name="HONOR_CODE" value="{{ $value['Customer']['HONOR_CODE'] }}">
                                <input type="hidden" name="HONOR_TITLE" value="{{ $value['Customer']['HONOR_TITLE'] }}">
                                <input type="hidden" name="CHR_ID" value="{{ $value['Charge']['CHR_ID'] ?? '' }}">
                                <input type="hidden" name="CHARGE_NAME" value="{{ $value['Charge']['CHARGE_NAME'] ?? '' }}">
                                <input type="hidden" name="C_SEARCH_ADDRESS" value="{{ $value['Customer']['SEARCH_ADDRESS'] }}">
                                @php
                                    $post_code = '';
                                    if ($value['Customer']['POSTCODE1'] && $value['Customer']['POSTCODE2']) {
                                        $post_code = '〒'.$value['Customer']['POSTCODE1'].'-'.$value['Customer']['POSTCODE2'];
                                    }
                                @endphp
                                <input type="hidden" name="C_POSTCODE" value="{{ $post_code }}">
                                <input type="hidden" name="C_ADDRESS" value="{{ $value['Customer']['ADDRESS'] }}">
                                <input type="hidden" name="C_POSTCODE1" value="{{ $value['Customer']['POSTCODE1'] }}">
                                <input type="hidden" name="C_POSTCODE2" value="{{ $value['Customer']['POSTCODE2'] }}">
                                <input type="hidden" name="C_CNT_ID" value="{{ $value['Customer']['CNT_ID'] }}">
                                <input type="hidden" name="C_BUILDING" value="{{ $value['Customer']['BUILDING'] }}">
                            </td>
                            <td {{ ($user['AUTHORITY'] != 1) ? 'class="w80"' : '' }} id="name{{ $key }}">{{ $value['Customer']['NAME'] }}</td>
                            @if ($user['AUTHORITY'] != 1)
                                <td id="user{{ $key }}">{{ $value['User']['NAME'] }}</td>
                            @endif
                        </tr>
                    @endforeach
                    @if ($paging)
                        <tr>
                            <td colspan="{{ ($user['AUTHORITY'] != 1) ? '3' : '2' }}" class="w40 center">
                                {{ $paging }}
                            </td>
                        </tr>
                    @endif
                </table>
                <div class="save_btn">
                    <input type="hidden" name="sort">
                    <input type="hidden" name="desc">
                    <a href="#" onclick="return popupclass.popup_close();">
                        <img src="{{ asset('bt_cancel_s.jpg') }}" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
