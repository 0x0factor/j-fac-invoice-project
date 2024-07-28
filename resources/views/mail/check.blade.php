<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block">
        <div class="contents_area">

            <h3 class="comment_h3">下記データに対して、{{ $snd_name }} 様宛てに以下コメントが届いています。<br>（コメント詳細については、{{ $rcv_name }}
                様にご確認ください。）</h3>

            <div class="mail_table">
                <table cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th class="th_title">件名</th>
                        <td class="td_title" width="200" colspan="3">{{ $subject }}</td>
                    </tr>
                    <tr>
                        <td class="td_title">ステータス</td>
                        <td class="td_title" colspan="3">{{ $mail_status }}</td>
                    </tr>
                    <tr>
                        <th class="th_fromt" rowspan="2">返信者</th>
                        <td class="td_fromt">氏名</td>
                        <td class="td_fromt" colspan="2">{{ $rcv_name }}</td>
                    </tr>
                    <tr>
                        <td class="td_fromb">メールアドレス</td>
                        <td class="td_fromb" colspan="2">
                            {{ $receiver }}
                        </td>
                    </tr>
                    <tr>
                        <th width="40" rowspan="2">受信者</th>
                        <td class="td_tot">氏名</td>
                        <td class="td_tot" colspan="2">
                            {{ $snd_name }}
                        </td>
                    </tr>
                    <tr>
                        <td>メールアドレス</td>
                        <td colspan="2">
                            {{ $sender }}
                        </td>
                    </tr>
                    <tr>
                        <th class="th_pw" rowspan="2">受信日時</th>
                        <td class="th_pw" colspan="3">
                            {{ $reptime }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
    </div>

    <div class="contents_box mt20">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block">
        <div class="contents_area">
            <div class="comment_txt">
                <dl>
                    <dt>コメント</dt>
                    <dd>{{ $comment }}</dd>
                </dl>
            </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block">
    </div>
    <div class="edit_btn">
        <a href="{{ route('mail.index') }}" class="imgover">
            <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
        </a>
    </div>
</div>
