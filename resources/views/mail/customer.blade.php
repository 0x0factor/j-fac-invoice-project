<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" alt="">

        <form action="{{ route('mail.store') }}" method="POST">
            @csrf
            <div class="contents_area">
                <h3 class="mail_h3">データのダウンロード</h3>
                <p class="p_message">{{ $rcv_name }} 様宛てに {{ $snd_name }}
                    様より下記データが届いています。<br>ダウンロードボタンを押してデータをダウンロードしてください。</p>
                <p class="pb20">
                    ※データのお預かり期間は本日より7日間となっておりますので、期間内にダウンロードしていただきますようお願いします。<br>&emsp;お預かり期間が過ぎた場合は送信者様にご連絡ください。</p>

                <div class="mail_table2">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th class="th_l">件名</th>
                            <th class="th_r">ダウンロード</th>
                        </tr>
                        <tr>
                            <td width="420">{{ $subject }}</td>
                            <td width="130">
                                <a
                                    href="{{ route($type . '.pdf', ['frm_id' => $frm_id, 'download' => 'download', 'token' => $token]) }}">
                                    <img src="{{ asset('img/bt_download1.jpg') }}" class="imgover" alt="ダウンロード">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
        </form>
    </div>

    <div class="contents_box mt20">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" alt="">
        <div class="contents_area">
            <h3 class="mail_h3">ステータスの選択</h3>
            <p class="pb20">
                データをご確認いただき、修正が必要な場合はステータスの「修正願い」にチェックをつけてください。<br>修正点がなくデータに問題なければ、「確認済み」にチェックを付け、「送信」ボタンを押してください。</p>
            <div class="mail_table3 mb30">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th width="120">ステータス</th>
                        <td width="410">
                            @foreach (['1' => '確認済み', '2' => '修正願い'] as $value => $label)
                                <label class="ml20 mr5 txt_mid">
                                    <input type="radio" name="STATUS" value="{{ $value }}">
                                    {{ $label }}
                                </label>
                            @endforeach
                        </td>
                    </tr>
                </table>
            </div>
            <h3 class="mail_h3">コメントの記入</h3>
            <p class="pb20">ステータスで「修正願い」を選択した場合、修正内容を以下のコメント欄にご記入ください。<br>送信されたコメントについては、データ送信者に送信されます。</p>
            <div class="mail_table3">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th class="t_top" width="120">コメント<br>(200文字まで)</th>
                        <td width="410">
                            <textarea name="COMMENT" rows="8" class="textarea2{{ isset($error['COMMENT']) ? ' error' : '' }}"></textarea>
                            <br><span class="must">{{ $error['COMMENT'] ?? '' }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>
    <div class="edit_btn">
        <input type="hidden" name="TML_ID">
        <input type="hidden" name="TOKEN">
        <input type="hidden" name="tkn">
        <button type="submit" name="reaffirmation">
            <img src="{{ asset('img/bt_next.jpg') }}" alt="確認">
        </button>
    </div>
</div>
