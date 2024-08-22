<!-- CSS -->
<link rel="stylesheet" href="{{ asset('css/popup.css') }}">

<!-- JavaScript -->
    <script>
        function insert(no) {
            $('#SETCHARGE input[type=text]').val($('#name' + no).html());
            $('#SETCHARGE input[type=hidden]').val($('#user' + no + ' input#CHR_ID').val());
            $('#SET_CHR_SEAL_FLG input[type=radio]').prop('checked', false);

            if ($('#user' + no + ' input#TMP_CHR_SEAL_FLG').val() == 1) {
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

            $.post(url, {
                params: param
            }, function(d) {
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

            $.post(url, {
                params: param
            }, function(d) {
                $('#popup').html(d);
                sortBy["CHARGE_NAME_KANA"] = 0;
                sortBy["LAST_UPDATE"] = 1;
                sortBy[sort] = 1 - param["desc"];
            });
        }
    </script>
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
                               <!-- Display current page and total records -->
                               {{ $paginator->total() }} 件中 {{ ($paginator->count() * ($paginator->currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator->currentPage()) }} 件表示中

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
                        <td colspan="4">
                            <a href="javascript:void(0)" onclick="sorting('CHARGE_NAME_KANA')">担当者名(カナ)</a> /
                            <a href="javascript:void(0)" onclick="sorting('LAST_UPDATE')">更新日順</a>
                            <input type="text" name="CHR_KEYWORD" class="w120 p2" id="CHR_KEYWORD">

                            <button onclick="return popupclass.popupajax('charge');" style="border:none;">
                                <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                            </button>
                        </td>
                    </tr>
                    <tr class="bgti">
                        <td class="w40"></td>
                        <td class="W140">名前</td>
                        <td class="W140">部署名</td>
                        <td>作成者</td>
                    </tr>
                    @foreach ($charge as $key => $value)
                        <tr class="{{ $loop->iteration % 2 == 1 ? 'bgcl' : '' }}">
                            <td class="w40 center" id="user{{ $key }}">
                                <button onclick="return insert({{ $key }})">
                                    <img src="{{ asset('img/bt_insert.jpg') }}" alt="Insert">
                                </button>
                                <input type="hidden" name="CHR_ID" value="{{ $value['CHR_ID'] }}">
                                <input type="hidden" name="TMP_CHR_SEAL_FLG" value="{{ $value['CHR_SEAL_FLG'] }}">

                            </td>
                            <td class="w140" id="name{{ $key }}">{{ $value['CHARGE_NAME'] }}</td>
                            <td class="w140">{{ $value['UNIT'] }}</td>
                            <td>{{ $value['User']['NAME'] }}</td>
                        </tr>
                    @endforeach


                </table>
                <div class="save_btn">
                    <input type="hidden" name="sort">
                    <input type="hidden" name="desc">
                    <button type="button" onclick="return popupclass.popup_close();" style="border: none;">
                        <img src="{{ asset('img/bt_cancel_s.jpg') }}" alt="">
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
