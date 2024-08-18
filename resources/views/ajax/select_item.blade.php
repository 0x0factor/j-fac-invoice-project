@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<link href="{{ asset('css/popup.css') }}" rel="stylesheet">
<script type="text/javascript">
    function insert(no) {

        if (form.focusline === undefined) {
            form.focusline = 0;
        }

        var data = JSON.parse($('#popup_itemlist').text());

        var dec = $('input[name="['+form.maintype+'][DECIMAL_UNITPRICE]"]:checked').val();
        $('select[name="['+form.focusline+']['+form.type+'][LINE_ATTRIBUTE]"]').val(0);
        $('input[name="['+form.focusline+']['+form.type+'][ITEM]"]').val(data[no].ITEM);
        $('input[name="['+form.focusline+']['+form.type+'][ITEM_CODE]"]').css('color', '#000').val(data[no].ITEM_CODE);
        $('input[name="['+form.focusline+']['+form.type+'][UNIT]"]').css('color', '#000').val(data[no].UNIT);
        var excise = $('input[name="['+form.maintype+'][EXCISE]"]:radio:checked').val();

        if (data[no].TAX_CLASS == 3) {
            $('select[name="['+form.focusline+']['+form.type+'][TAX_CLASS]"]').val(data[no].TAX_CLASS);
        } else {
            var taxOperationDate = @json($taxOperationDate);
            var issue_date = $("input.cal.date").val().replace(/-/g, '/');
            var prefix = "";
            $.each(taxOperationDate, function (per, dates) {
                dates["start"] = dates["start"].replace(/-/, '/').replace(/-/, '/');
                if (Date.parse(dates["start"]) <= Date.parse(issue_date)) {
                    if (per > 5) prefix = per;
                }
            });
            $('select[name="['+form.focusline+']['+form.type+'][TAX_CLASS]"]').val(prefix + "" + data[no].TAX_CLASS);
        }

        $('input[name="['+form.focusline+']['+form.type+'][UNIT_PRICE]"]').css('color', '#000').val(number_format(data[no].UNIT_PRICE));

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

        if ($("#ITEM_KEYWORD").val()) {
            param["keyword"] = $("#ITEM_KEYWORD").val();
        }

        if ($("#sort").val()) {
            param["sort"] = $("#sort").val();
            param["desc"] = $("#desc").val();
        }

        $.post(url, { params: param }, function (d) {
            $('#popup').html(d);
        });
    }

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

        if ($("#ITEM_KEYWORD").val()) {
            param["keyword"] = $("#ITEM_KEYWORD").val();
        }

        $.post(url, { params: param }, function (d) {
            $('#popup').html(d);
            sortBy = {
                "ITEM_CODE": 0,
                "UNIT_PRICE": 0,
                "ITEM_KANA": 1,
                "LAST_UPDATE": 1,
                "TAX_CLASS": 1
            };
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

<form id="popupForm">
<div id="popup_contents">
    <img src="{{ asset('/img/popup/tl_item.jpg') }}" style="padding-bottom:10px;" />
    <div class="popup_contents_box">
        <div class="popup_contents_area clearfix">
            <table width="550" cellpadding="0" cellspacing="0" border="0" class="tbl">
                <tr>
                    <td colspan="6" class="w20 center">
                        @if($user['AUTHORITY'] != 1)
                            <button onclick="return popupclass.popupajax('item');" class="float_l lmargin20" style="border:none;">
                                <img src="{{ asset('img/bt_new2.jpg') }}" />
                            </button>
                        @else
                            <button onclick="return popupclass.popupajax('item');" class="float_l lmargin40" style="border:none;">
                                <img src="{{ asset('img/bt_new2.jpg') }}" />
                            </button>
                        @endif
                        <!-- Display pagination information -->
                        <div id='pagination'>
                            {{ $paginator->total() }} 件中 {{ ($paginator->count() * ($paginator->currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator->currentPage()) }} 件表示中
                        </div>

                        <div id='pagination'>
                            @if ($paginator->onFirstPage())
                                <span class="disabled"><< {{ __('前へ') }}</span> |
                            @else
                                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><< {{ __('前へ') }}</a> |
                            @endif

                            <!-- Pagination Elements -->
                            @foreach ($paginator->links()->elements as $element)
                                <!-- "Three Dots" Separator -->
                                @if (is_string($element))
                                    <span class="disabled">{{ $element }}</span> |
                                @endif

                                <!-- Array Of Links -->
                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $paginator->currentPage())
                                            <span class="active">{{ $page }}</span> |
                                        @else
                                            <a href="{{ $url }}">{{ $page }}</a> |
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($paginator->hasMorePages())
                                <a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
                            @else
                                <span class="disabled">{{ __('次へ') }} >></span>
                            @endif
                        </div>
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
                        <button onclick="return popupclass.popupajax('select_item');" style="border:none;">
                            <img src="{{ asset('img/bt_search.jpg') }}" />
                        </button>
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
                @foreach($item as $key => $value)
                <tr class="{{ ($loop->index % 2) == 1 ? 'bgcl' : '' }}">
                    <td class="w40">
                        <a href="#" onclick="return insert({{ $key }});" style="border:none;">
                            <img src="{{ asset('img/bt_insert.jpg') }}" />
                        </a>
                    </td>
                    <td class="w120" id="ITEM{{ $key }}">{{ $value['ITEM'] }}</td>
                    <td class="w80" id="ITEM_CODE{{ $key }}">{{ $value['ITEM_CODE'] }}</td>
                    <td id="UNIT_PRICE{{ $key }}">{{ $value['UNIT_PRICE'] }}円</td>
                    <td class="w40" id="TAX_CLASS{{ $key }}">{{ $excises[$value['TAX_CLASS']] }}</td>
                    <td>{{ $value['User']['NAME'] }}</td>
                <input type="hidden" name="ITM_ID" value="{{$value['ITM_ID']}}" id="ITM_ID">
                </tr>
                @endforeach
            </table>
            <div class="save_btn">
                <button onclick="return popupclass.popup_close();" style="border:none;">
                    <img src="{{ asset('img/bt_cancel_s.jpg') }}" />
                </button>
            </div>
            <div id="popup_itemlist" style="display: none">
                <input type="hidden" name="sort">
                <input type="hidden" name="desc">
                {!! isset($item) ? json_encode($item) : '' !!}
            </div>
        </div>
    </div>
</div>
</form>


