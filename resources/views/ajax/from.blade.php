<link rel="stylesheet" href="{{ asset('css/popup.css') }}">
@section('scripts')
    <script type="text/javascript">
        function insert(no) {
            $('#FROMNAME').children('input').val($('#name' + no).html());
            $('#FROM').children('input').val($('#mail' + no).html());
            popupclass.popup_close();
            return false;
        }

        var url = "{{ url('/ajax/popup') }}";

        function paging(page) {
            var param = {
                "type": "from",
                "page": page
            };
            $.post(url, {
                params: param
            }, function(d) {
                $('#popup').html(d);
            });
        }
    </script>
@endsection
@section('link')
    <style type="text/css">
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
    </style>
@endsection

<form id="popupForm">
    <div id="popup_contents">
        <img src="{{ asset('img/popup/tl_charge.jpg') }}" alt="">
        <div class="popup_contents_box">
            <div class="popup_contents_area clearfix">
                <table width="500" cellpadding="0" cellspacing="0" border="0" class="tbl">
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
                    @php $i = 0; @endphp
                    @foreach ($charge as $key => $value)
                        <tr @if ($i % 2 == 1) class="bgcl" @endif>
                            <td class="w40">
                                <a href="#" onclick="return insert({{ $key }});">
                                    <img src="{{ asset('img/bt_insert.jpg') }}" alt="">
                                </a>
                            </td>
                            <td class="w80" id="name{{ $key }}">{{ $value['Charge']['CHARGE_NAME'] }}</td>
                            <td id="mail{{ $key }}">{{ $value['Charge']['MAIL'] }}</td>
                        </tr>
                        @php $i++; @endphp
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
                    <a href="#" onclick="return popupclass.popup_close();">
                        <img src="{{ asset('img/bt_cancel_s.jpg') }}" alt="">
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
