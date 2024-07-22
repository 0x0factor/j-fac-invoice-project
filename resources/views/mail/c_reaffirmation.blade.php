<script type="text/javascript">
<!--
	//送信の確認
	function rec() {
		if (confirm("１度送信してしまうとPDFのダウンロードが出来なくなりますが、送信をしてもよろしいですか？")){
			//送信
			return true;
		} else {
			//キャンセル
			return false;
		}
	}
// -->
</script>
<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" />
        <div class="contents_area">
            <form method="POST" action="{{ route('mail.store') }}">
                @csrf

                <h3 class="mail_h3">こちらで、入力データの確認をしてください。</h3>
                <p class="mb30">確認が終わったら、送信ボタンを押して送信します。</p>

                <div class="mail_table">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>入力確認</th>
                        </tr>
                        <tr>
                            <td></td>
                        </tr>
                        <tr>
                            <th class="th_fromt" colspan="2">ステータス</th>
                            <td class="td_pwf" colspan="2" style="line-height:1.5em;">
                                {{ $status }}
                                <input type="hidden" name="STATUS" value="{{ $status }}">
                            </td>
                        </tr>
                        <tr>
                            <th class="th_pw" colspan="2">コメント<br>（200文字まで）</th>
                            <td class="td_pw" colspan="2">
                                {{ $comment }}
                                <input type="hidden" name="COMMENT" value="{{ $comment }}">
                            </td>
                        </tr>
                    </table>
                </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
    </div>

    <div class="edit_btn">
        <input type="hidden" name="TML_ID" value="">
        <input type="hidden" name="TOKEN" value="">
        <input type="hidden" name="tkn" value="">

        <button type="submit" name="logind" class="imgover" style="background: url('/img/bt_back.jpg') no-repeat; width: 120px; height: 40px; border: none;" onclick="return rec();" alt="戻る">戻る</button>

        <button type="submit" name="send" class="imgover" style="background: url('/img/bt_submit.jpg') no-repeat; width: 120px; height: 40px; border: none;" onclick="return rec();" alt="送信">送信</button>

            </form>
    </div>
</div>
