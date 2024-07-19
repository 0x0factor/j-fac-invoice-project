<link rel="stylesheet" type="text/css" href="{{ asset('css/popup.css') }}">
<style>
    .tbl {
        border: 1px #E3E3E3 solid;
        border-collapse: collapse;
        border-spacing: 0;
        margin: 10px auto;
    }

    .tbl tr.bgti {
        background: #EEEEEE;
    }

    .tbl tr.bgcl {
        background: #F5F5F5;
    }

    .tbl td {
        padding: 10px;
        border: 1px #E3E3E3 solid;
        border-width: 0 0 1px 1px;
    }

    .tbl td.left {
        text-align: left;
    }

    .tbl td.center {
        text-align: center;
    }
</style>
<script>
    $(document).ready(function($) {
        var url = "{{ url('/ajax/popup') }}";
        var no = "{{ $no }}";

        // Radio button click handler
        $('input[type="radio"]').click(function() {
            var param = {
                type: "to",
                no: no,
                ctype: $(this).val()
            };

            $.post(url, { params: param }, function(d) {
                $('#popup').html(d);
            });
        });
    });

    function insert(no) {
        $('#TONAME input').val($('#name'+no).text());
        $('#TO input').val($('#mail'+no).text());
        popupclass.popup_close();
        return false;
    }
</script>
<form id="popupForm">
    <div id="popup_contents">
        <img src="{{ asset('/img/popup/tl_allcharge.jpg') }}" alt="">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="500" cellpadding="0" cellspacing="0" border="0" class="tbl">
                    <tr>
                        <td colspan="3" class="w40">
                            <label>
                                <input type="radio" name="type" value="0" class="ml20 mr5 txt_mid" {{ old('type', 0) == 0 ? 'checked' : '' }}>
                                顧客担当者
                            </label>
                            <label>
                                <input type="radio" name="type" value="1" class="ml20 mr5 txt_mid" {{ old('type', 0) == 1 ? 'checked' : '' }}>
                                自社担当者
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="w40 center">
                            {{ $nowpage }}
                            {{ $paging }}
                        </td>
                    </tr>
                    <tr class="bgti">
                        <td class="w40"></td>
                        <td class="w80">名前</td>
                        <td>メールアドレス</td>
                    </tr>
                    @foreach($charge as $key => $value)
                    <tr class="{{ ($loop->index % 2 == 1) ? 'bgcl' : '' }}">
                        <td class="w40">
                            <a href="#" onclick="return insert({{ $key }});">
                                <img src="{{ asset('bt_insert.jpg') }}" alt="">
                            </a>
                        </td>
                        <td class="w80" id="name{{ $key }}">{{ $value['CHARGE_NAME'] }}</td>
                        <td id="mail{{ $key }}">{{ $value['MAIL'] }}</td>
                    </tr>
                    @endforeach
                </table>
                <div class="save_btn">
                    <a href="#" onclick="return popupclass.popup_close();">
                        <img src="{{ asset('bt_cancel_s.jpg') }}" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
