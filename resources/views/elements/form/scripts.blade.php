<script>
    function customer_reset() {
        $('#SETCUSTOMER').children('input[type=text]').val('');
        $('#SETCUSTOMER').children('input[type=hidden]').val('');

        return false;
    }

    function cstchr_reset() {
        $('#SETCUSTOMERCHARGE').children('input[type=text]').val('');
        $('#SETCUSTOMERCHARGE').children('input[type=text]').removeAttr('readonly');
        $('#SETCUSTOMERCHARGE').children('input[type=hidden]').val('');
        $('#SETCCUNIT').children('input[type=text]').val('');
        $('#SETCCUNIT').children('input[type=text]').removeAttr('readonly');
        return false;
    }

    function chr_reset() {
        $('#SETCHARGE').children('input[type=text]').val('');
        $('#SETCHARGE').children('input[type=hidden]').val('');

        return false;
    }

    function edit1_toggle(str) {
        $('div.contents_area').slideToggle();
        if (str == 'on') {
            $('span.show_bt1_on').hide();
            $('span.show_bt1_off').show();
        } else {
            $('span.show_bt1_on').show();
            $('span.show_bt1_off').hide();
        }
    }

    function edit2_toggle(str) {
        if (str == 'on') {
            $('div.contents_area2').slideUp();
            $('div.contents_area3').slideUp();
            $('span.show_bt2_on').hide();
            $('span.show_bt2_off').show();
        } else {
            $('div.contents_area2').slideDown();
            $('div.contents_area3').slideDown();
            $('span.show_bt2_on').show();
            $('span.show_bt2_off').hide();
        }
    }

    function detail_toggle(str) {
        if (str == 'on') {
            $('#detail').slideUp();
            $('span.show_btdetail_on').hide();
            $('span.show_btdetail_off').show();
        } else {
            $('#detail').slideDown();
            $('span.show_btdetail_on').show();
            $('span.show_btdetail_off').hide();
        }
    }

    function edit3_toggle(str) {
        $('div.contents_area4').slideToggle();

        if (str == 'on') {
            $('span.show_bt3_on').hide();
            $('span.show_bt3_off').show();
        } else {
            $('span.show_bt3_on').show();
            $('span.show_bt3_off').hide();
        }
    }

    function edit4_toggle(str) {
        $('div.contents_area5').slideToggle();

        if (str == 'on') {
            $('span.show_bt4_on').hide();
            $('span.show_bt4_off').show();
        } else {
            $('span.show_bt4_on').show();
            $('span.show_bt4_off').hide();
        }
    }

    $(document).ready(function($) {
        @if (!$collapse_other)
            $('div.contents_area4').slideToggle();
            $('span.show_bt3_on').show();
            $('span.show_bt3_off').hide();
        @endif

        @if (!$collapse_management)
            $('div.contents_area5').slideToggle();
            $('span.show_bt4_on').show();
            $('span.show_bt4_off').hide();
        @endif

        @for ($i = 0; $i < $dataline; $i++)
            $(".row_{{ $i }}").hover(
                function() {
                    $(':text[name*="data[{{ $i }}]"]').addClass('hoverLine');
                },
                function() {
                    $(':text[name*="data[{{ $i }}]"]').removeClass('hoverLine');
                }
            );
        @endfor

        setReadOnly(form.maintype);
        recalculation(form.maintype);
    });
</script>
