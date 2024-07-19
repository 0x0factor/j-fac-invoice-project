<link rel="stylesheet" type="text/css" href="{{ asset('css/popup.css') }}">

<script type="text/javascript">
    function insert(no) {
        if (form.focusline === undefined) {
            form.focusline = 0;
        }
        var data = JSON.parse(document.getElementById('popup_itemlist').textContent);
        var dec = document.querySelector('input[name="data[' + form.maintype + '][DECIMAL_UNITPRICE]"]:checked').value;
        document.querySelector('select[name="data[' + form.focusline + '][' + form.type + '][LINE_ATTRIBUTE]"]').value = 0;
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][ITEM]"]').value = data[no].Item.ITEM;
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][ITEM_CODE]"]').style.color = '#000';
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][ITEM_CODE]"]').value = data[no].Item.ITEM_CODE;
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][UNIT]"]').style.color = '#000';
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][UNIT]"]').value = data[no].Item.UNIT;
        var excise = document.querySelector('input[name="data[' + form.maintype + '][EXCISE]"]:checked').value;

        if (data[no].Item.TAX_CLASS == 3) {
            document.querySelector('select[name="data[' + form.focusline + '][' + form.type + '][TAX_CLASS]"]').value = data[no].Item.TAX_CLASS;
        } else {
            var taxOperationDate = @json($taxOperationDate);
            var issueDate = document.querySelector('input.cal.date').value.replace(/-/g, '/');
            var prefix = "";
            Object.keys(taxOperationDate).forEach(function(per) {
                var dates = taxOperationDate[per];
                dates.start = dates.start.replace(/-/, '/').replace(/-/, '/');
                if (Date.parse(dates.start) <= Date.parse(issueDate)) {
                    if (per > 5) prefix = per;
                }
            });
            document.querySelector('select[name="data[' + form.focusline + '][' + form.type + '][TAX_CLASS]"]').value = prefix + "" + data[no].Item.TAX_CLASS;
        }

        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][UNIT_PRICE]"]').style.color = '#000';
        document.querySelector('input[name="data[' + form.focusline + '][' + form.type + '][UNIT_PRICE]"]').value = number_format(data[no].Item.UNIT_PRICE);

        setReadOnly(form.maintype);
        focusLine(form.maintype);
        recalculation(form.maintype);
        popupclass.popup_close();
        return false;
    }

    var url = "{{ url('/ajax/popup') }}";

    function paging(page) {
        var param = {
            "type": "select_item",
            "page": page
        };

        //キーワードがある場合
        if ($("#ITEM_KEYWORD").val()) {
            param.keyword = $("#ITEM_KEYWORD").val();
        }

        //ソートされている場合
        if ($("#sort").val()) {
            param.sort = $("#sort").val();
            param.desc = $("#desc").val();
        }

        $.post(url, {
            params: param
        }, function(d) {
            $('#popup').html(d);
        });
    }

    //商品一覧並び替え
    var sortBy = {
        "ITEM_CODE": 0,
        "UNIT_PRICE": 0,
        "ITEM_KANA": 1,
        "LAST_UPDATE": 0,
        "TAX_CLASS": 0
    };

    function sorting(sort) {
        var param = {
            "type": "select_item",
            "sort": sort,
            "desc": sortBy[sort]
        };

        //キーワードがある場合
        if ($("#ITEM_KEYWORD").val()) {
            param.keyword = $("#ITEM_KEYWORD").val();
        }

        $.post(url, {
            params: param
        }, function(d) {
            $('#popup').html(d);
            //降順昇順いれかえ
            sortBy["ITEM_CODE"] = 0;
            sortBy["UNIT_PRICE"] = 0;
            sortBy["ITEM_KANA"] = 1;
            sortBy["LAST_UPDATE"] = 1;
            sortBy["TAX_CLASS"] = 1;
            sortBy[sort] = 1 - param["desc"];
        });
    }
</script>

<style type="text/css">
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

<div id="popup_contents">
    <img src="{{ asset('/img/popup/tl_item.jpg') }}" style="padding-bottom:10px;">
    <div class="popup_contents_box">
        <div class="popup_contents_area clearfix">
            <table width="550" cellpadding="0" cellspacing="0" border="0" class="tbl">
                <tr>
                    <td colspan="6" class="w20 center">
                        @if ($user['AUTHORITY'] != 1)
                            <a href="#" class="float_l lmargin20" onclick="return popupclass.popupajax('item');">
                                <img src="{{ asset('img/bt_new2.jpg') }}" alt="" style="border: none;">
                            </a>
                        @else
                            <a href="#" class="float_l lmargin40" onclick="return popupclass.popupajax('item');">
                                <img src="{{ asset('img/bt_new2.jpg') }}" alt="" style="border: none;">
                            </a>
                        @endif
                        {{ $nowpage }}
                        {{ $paging }}
                    </td>
                </tr>
                <tr>
                    <td colspan="6">
                        <a href="javascript:void(0)" onclick="return sorting('ITEM_KANA');">商品名(カナ)</a> /
                        <a href="javascript:void(0)" onclick="return sorting('ITEM_CODE');">商品コード</a> /
                        <a href="javascript:void(0)" onclick="return sorting('UNIT_PRICE');">単価順</a> /
                        <a href="javascript:void(0)" onclick="return sorting('LAST_UPDATE');">更新日順</a> /
                        <a href="javascript:void(0)" onclick="return sorting('TAX_CLASS');">税区分</a>
                        <input type="text" name="ITEM_KEYWORD" class="w100 p2">
                        <a href="#" onclick="return popupclass.popupajax('select_item');">
                            <img src="{{ asset('img/bt_search.jpg') }}" alt="" style="border: none;">
                        </a>
                    </td>
                </tr>
                <tr class="bgti">
                    <td class="w40"></td>
                    <td class="w120">商品名</td>
                    <td class="w80">商品コード</td>
                    <td class="w80">単価</td>
                    <td class="w40">税区分</td>
                    <td>作成者</td>
                </tr>
                @foreach ($item as $key => $value)
                    <tr class="{{ $i % 2 == 1 ? 'bgcl' : '' }}">
                        <td class="w40">
                            <a href="#" onclick="return insert('{{ $key }}');">
                                <img src="{{ asset('img/bt_insert.jpg') }}" alt="" style="border: none;">
                            </a>
                        </td>
                        <td class="w120" id="ITEM{{ $key }}">{{ $value['Item']['ITEM'] }}</td>
                        <td class="w80" id="ITEM_CODE{{ $key }}">{{ $value['Item']['ITEM_CODE'] }}</td>
                        <td id="UNIT_PRICE{{ $key }}">{{ number_format($value['Item']['UNIT_PRICE']) }}円</td>
                        <td class="w40" id="TAX_CLASS{{ $key }}">{{ $excises[$value['Item']['TAX_CLASS']] }}</td>
                        <td>{{ $value['User']['NAME'] }}</td>
                        <input type="hidden" name="ITM_ID" value="{{ $value['Item']['ITM_ID'] }}" id="ITM_ID">
                    </tr>
                    <?php $i++; ?>
                @endforeach
            </table>
            <div class="save_btn">
                <a href="#" onclick="return popupclass.popup_close();">
                    <img src="{{ asset('img/bt_cancel_s.jpg') }}" alt="" style="border: none;">
                </a>
            </div>
            <div id="popup_itemlist" style="display: none;">
                <input type="hidden" name="sort">
                <input type="hidden" name="desc">
                {{ isset($item) ? json_encode($item) : '' }}
            </div>
        </div>
    </div>
</div>
