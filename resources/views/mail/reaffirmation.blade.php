<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" alt="">
        <div class="contents_area">
            <form action="{{ url('mail') }}" method="POST">
                @csrf

                <h3 class="mail_h3">こちらで、送信内容を確認してください。</h3>
                <p class="mb30">確認が終わりましたら、送信を押して送信できます。</p>
                <div class="mail_table">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th class="th_title">件名</th>
                            <td class="td_title" colspan="3">{{ $param['SUBJECT'] }}</td>
                        </tr>
                        <tr>
                            <th class="th_fromt" width="40" rowspan="2">From</th>
                            <td class="td_fromt" width="120">送信者</td>
                            <td class="td_fromt" width="740" id="FROMNAME">{{ $param['CHARGE'] }}</td>
                        </tr>
                        <tr>
                            <td class="td_fromb">返信用メールアドレス</td>
                            <td class="td_fromb" colspan="2" id="FROM">
                                {{ $param['FROM'] }}
                            </td>
                        </tr>
                        <tr>
                            <th class="th_tot" width="40" rowspan="2">To</th>
                            <td class="td_tot" width="100">氏名</td>
                            <td id="TONAME" class="td_tot" width="300">
                                {{ $param['CUSTOMER_CHARGE'] }}
                            </td>
                        </tr>
                        <tr>
                            <td class="td_tob">メールアドレス</td>
                            <td id="TO" class="td_tob" colspan="2">
                                {{ $param['TO'] }}
                            </td>
                        </tr>
                        <tr>
                            <th class="th_pw" colspan="2">
                                <img src="{{ asset('img/i_pw.jpg') }}" alt=""><br>
                                <p>（データ保護のため受信者がデータをダウンロードする前にパスワードを設定することができます）</p>
                            </th>
                            <td class="td_pw" colspan="2">
                                {{ $param['PASSWORD1'] }}
                            </td>
                        </tr>
                    </table>
                </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>

    <div class="contents_box mt20">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" alt="">
        <div class="contents_area">
            <table cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td>{!! $body !!}</td>
                </tr>
            </table>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
    </div>

    <input type="hidden" name="USR_ID" value="{{ $param['USR_ID'] }}">
    <input type="hidden" name="TYPE" value="{{ $param['TYPE'] }}">
    <input type="hidden" name="FRM_ID" value="{{ $param['FRM_ID'] }}">
    <input type="hidden" name="SUBJECT" value="{{ $param['SUBJECT'] }}">
    <input type="hidden" name="MAIL" value="{{ $to }}">
    <input type="hidden" name="BODY" value="{{ $body }}">
    <input type="hidden" name="CORD" value="{{ $param['CORD'] }}">
    <input type="hidden" name="TO" value="{{ $param['TO'] }}">
    <input type="hidden" name="FROM" value="{{ $param['FROM'] }}">
    <input type="hidden" name="COMPANY" value="{{ $param['COMPANY'] }}">
    <input type="hidden" name="CUSTOMER" value="{{ $param['CUSTOMER'] }}">
    <input type="hidden" name="CHARGE" value="{{ $param['CHARGE'] }}">
    <input type="hidden" name="CUSTOMER_CHARGE" value="{{ $param['CUSTOMER_CHARGE'] }}">
    <input type="hidden" name="PASSWORD1" value="{{ $param['PASSWORD1'] }}">
    <input type="hidden" name="tkn" value="{{ $param['tkn'] }}">
    <div class="edit_btn">
        <input type="submit" class="imgover" name="body" value="戻る">
        <input type="submit" class="imgover" name="send" value="送信" onclick="return sendmail();">
    </div>
    </form>
</div>
