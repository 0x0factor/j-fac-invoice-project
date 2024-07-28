@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
@section('link')
    <link rel="stylesheet" href="{{ asset('css/popup.css') }}">
@endsection
@section('scripts')
    <script>
        function insert(no, id) {
            $('#SETCUSTOMERCHARGE').children('input[type=text]').val($('#name' + no).html());
            $('#SETCUSTOMERCHARGE').children('input[type=hidden]').val(id);
            $('#SETCCUNIT').children('input[type=text]').val($('#unit' + no).html());
            popupclass.popup_close();
            return false;
        }

        var url = "{{ url('/ajax/popup') }}";

        function paging(page) {
            var id = $('#SETCUSTOMER').children('input[type=hidden]').val();

            var param = {
                "type": "customer_charge",
                "page": page,
                "id": id
            };

            // Add keyword if exists
            if ($("#CHRC_KEYWORD").val()) {
                param["keyword"] = $("#CHRC_KEYWORD").val();
            }

            // Add sorting parameters if exists
            if ($("#sort").val()) {
                param["sort"] = $("#sort").val();
                param["desc"] = $("#desc").val();
            }

            $.post(url, {
                params: param
            }, function(d) {
                $('#popup').html(d);
            });
        }

        var sortBy = {
            "CUSTOMER_NAME": 0,
            "LAST_UPDATE": 0,
            "CHARGE_NAME_KANA": 0
        };

        function sorting(sort) {
            var id = $('#SETCUSTOMER').children('input[type=hidden]').val();

            var param = {
                "type": "customer_charge",
                "sort": sort,
                "desc": sortBy[sort],
                "id": id
            };

            // Add keyword if exists
            if ($("#CHRC_KEYWORD").val()) {
                param["keyword"] = $("#CHRC_KEYWORD").val();
            }

            $.post(url, {
                params: param
            }, function(d) {
                $('#popup').html(d);

                // Toggle sort direction
                sortBy["CUSTOMER_NAME"] = 0;
                sortBy["LAST_UPDATE"] = 1;
                sortBy["CHARGE_NAME_KANA"] = 0;

                sortBy[sort] = 1 - param["desc"];
            });
        }
    </script>
@endsection

<style>
    /* CSS styles here */
    table.tbl {
        border: 1px #E3E3E3 solid;
        border-collapse: collapse;
        border-spacing: 0;
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
</style>

<form id="popupForm">
    <div id="popup_contents">
        <img src="{{ asset('/img/popup/tl_customercharge.jpg') }}" style="padding-bottom:10px;">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="500" cellpadding="0" cellspacing="0" border="0" class="tbl">
                    <tr>
                        <td colspan="5" class="w20 center">
                            @if ($user['AUTHORITY'] != 1)
                                <a href="#"
                                    onclick="return popupclass.popupajax('add_customer_charge', {{ $cst_id }});"
                                    class="float_l lmargin20">
                                    <img src="{{ asset('bt_new2.jpg') }}" alt="">
                                </a>
                            @else
                                <a href="#" onclick="return popupclass.popupajax('add_customer_charge');"
                                    class="float_l lmargin40">
                                    <img src="{{ asset('bt_new2.jpg') }}" alt="">
                                </a>
                            @endif
                            {{ $nowpage }}
                            {{ $paging }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <a href="javascript:void(0)" onclick="return sorting('CHARGE_NAME_KANA');">担当者名(カナ)</a> /
                            <a href="javascript:void(0)" onclick="return sorting('CUSTOMER_NAME');">顧客名(カナ)</a> /
                            <a href="javascript:void(0)" onclick="return sorting('LAST_UPDATE');">更新日順</a>
                            <input type="text" name="CHRC_KEYWORD" class="w120 p2" placeholder="キーワード">
                            <a href="#" onclick="return popupclass.popupajax('customer_charge');">
                                <img src="{{ asset('bt_search.jpg') }}" alt="">
                            </a>
                        </td>
                    </tr>
                    <tr class="bgti">
                        <td class="w20"></td>
                        <td class="w100">名前</td>
                        <td class="w100">顧客名</td>
                        <td class="w60">部署</td>
                        <td>作成者</td>
                    </tr>
                    @foreach ($customer_charge as $key => $value)
                        <tr class="{{ $loop->index % 2 == 1 ? 'bgcl' : '' }}">
                            <td class="w20">
                                <a href="#"
                                    onclick="return insert({{ $key }}, {{ $value['CustomerCharge']['CHRC_ID'] }});">
                                    <img src="{{ asset('bt_insert.jpg') }}" alt="">
                                </a>
                            </td>
                            <td class="w100" id="name{{ $key }}">
                                {{ $value['CustomerCharge']['CHARGE_NAME'] }}</td>
                            <td class="w100">{{ $value['Customer']['NAME'] }}</td>
                            <td class="w60" id="unit{{ $key }}">{{ $value['CustomerCharge']['UNIT'] }}
                            </td>
                            <input type="hidden" name="CHRC_ID" id="CHRC_ID"
                                value="{{ $value['CustomerCharge']['CHRC_ID'] }}">
                            <td>{{ $value['User']['NAME'] }}</td>
                        </tr>
                    @endforeach
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
