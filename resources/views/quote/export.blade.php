<?php	//完了メッセージ
	echo $session->flash();
?>

<script type="text/javascript">
<!--
/*
var url="<?php echo $html->url('/ajax/excel'); ?>";

$(document).ready(function($){

	$('select.date').change(function(){

		var param = eval({
			"year1"  : $('#year1').val(),
			"month1" : $('#month1').val(),
			"day1"   : $('#day1').val(),
			"year2"  : $('#year2').val(),
			"month2" : $('#month2').val(),
			"day2"   : $('#day2').val()
		});

		$.post(url, {params:param}, function(d){
			$('#test').html(d);
		});
	});

	var param = eval({
		"year1"  : $('#year1').val(),
		"month1" : $('#month1').val(),
		"day1"   : $('#day1').val(),
		"year2"  : $('#year2').val(),
		"month2" : $('#month2').val(),
		"day2"   : $('#day2').val()
	});

	$.post(url, {params:param}, function(d){
		$('#test').html(d);
	});
});
*/

// -->
</script>

<div id="contents">
    <div class="search_box">
        <div class="search_area">
            <form action="{{ route('quotes.store') }}" method="POST">
                @csrf
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td>
                            以下のプルダウンより抽出する期間を設定してください。期間は見積書の発行日となります。
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="datetime-local" name="DATE1" id="DATE1">
                            ～
                            <input type="datetime-local" name="DATE2" id="DATE2">
                        </td>
                    </tr>
                </table>
                <div id="test"></div>

                <div class="search_btn">
                    <button type="submit" name="download">
                        <img src="{{ asset('img/bt_search.jpg') }}" alt="検索する">
                    </button>
                </div>
            </form>
        </div>
        <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
    </div>
</div>
