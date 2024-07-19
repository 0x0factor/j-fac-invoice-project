<script type="text/javascript">
<!--
	function customer_reset() {
		$('#SETCUSTOMER').children('input[type=text]').val('');
		$('#SETCUSTOMER').children('input[type=hidden]').val('');
		return false;
	}
	function cstchr_reset() {
		$('#SETCUSTOMERCHARGE').children('input[type=text]').val('');
		$('#SETCUSTOMERCHARGE').children('input[type=text]').removeAttr('readonly')
		$('#SETCUSTOMERCHARGE').children('input[type=hidden]').val('');
		$('#SETCCUNIT').children('input[type=text]').val('');
		$('#SETCCUNIT').children('input[type=text]').removeAttr('readonly')
		return false;
	}

	function chr_reset() {
		$('#SETCHARGE').children('input[type=text]').val('');
		return false;
	}

	function err_dt(_no){
		$('tr[class="row_'+_no+'"] input[class="documenttitle"]').css('background-color','#DD0000');
	}
	function err_dn(_no){
		$('tr[class="row_'+_no+'"] input[class="documentnumber"]').css('background-color','#DD0000');
	}

// -->
</script>
{{-- Flash Message --}}
{{ session()->flash() }}

{{-- Guide Section --}}
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/i_guide02.jpg') }}" alt="">
        <p>こちらのページは送付状作成の画面です。<br>必要な情報を入力の上「保存する」ボタンを押すと送付状を作成できます。</p>
    </div>
</div>
<br class="clear">

{{-- Contents Section --}}
<div id="contents">
    @error('CUSTOMER_NAME')
        {{ $message }}
    @enderror

    <form action="{{ url('coverpages') }}" method="post" class="Coverpages">
        @csrf
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>

        <h3>
            <div class="edit_02_coverpage"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <input type="hidden" name="maxformline" value="{{ $maxline }}">
                <input type="hidden" name="dataformline" value="{{ $dataline }}">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:170px;">送付方法</th>
                        <td style="width:710px;" colspan="3">
                            {{-- Adjust this to match your radio button setup --}}
                            {{-- Use Laravel Form Helpers or HTML to create radio buttons --}}
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                    <tr>
                        <th style="width:170px;">顧客名</th>
                        <td style="width:710px;" colspan="3" id="SETCUSTOMER">
                            <input type="text" name="CUSTOMER_NAME" class="w140 p2 @error('CUSTOMER_NAME') error @enderror" readonly maxlength="60">
                            <input type="hidden" name="CST_ID">
                            <a href="#" onclick="return popupclass.popupajax('select_customer');"><img src="{{ asset('img/bt_select2.jpg') }}" alt="" /></a>
                            <a href="#" onclick="return customer_reset();"><img src="{{ asset('img/bt_delete2.jpg') }}" alt="" /></a>
                            <br><span class="must">@error('CUSTOMER_NAME') {{ $message }} @enderror</span>
                            <br><span class="usernavi">{{ $usernavi['CVR_CST'] }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="4"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td></tr>
                    {{-- Continue translating the rest of the table --}}
                </table>
                {{-- Continue with the rest of the form fields --}}
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>

        <div class="edit_btn">
            <input type="image" src="{{ asset('img/bt_save.jpg') }}" alt="保存する" name="submit" class="imgover">
            <input type="image" src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル" name="cancel" class="imgover">
        </div>
    </form>
</div>

{{-- HTML Link and Image Links --}}
<a href="#" onclick="return popupclass.popupajax('select_customer');"><img src="{{ asset('img/bt_select2.jpg') }}" alt="" /></a>
<a href="#" onclick="return customer_reset();"><img src="{{ asset('img/bt_delete2.jpg') }}" alt="" /></a>
<a href="#"><img src="{{ asset('img/bt_add.jpg') }}" alt="行を追加する" onclick="return form.coverpage_addline(null);" /></a>
<a href="#"><img src="{{ asset('img/bt_reset.jpg') }}" alt="リセット" onclick="return form.f_reset('null');" /></a>
