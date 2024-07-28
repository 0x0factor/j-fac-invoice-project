<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" />
        <div class="contents_area">
            <form action="{{ url('mail') }}" method="POST" class="Mail">
                @csrf

                <h3 class="mail_h3">こちらで、データ送信者と宛先の指定をしてください。</h3>
                <p class="mb30">
                    「氏名」「メールアドレス」については、それぞれ「自社担当者」「取引先担当者」であらかじめ情報が登録されている場合、<br />「登録情報から選択」ボタンで情報を呼び出すことができます。</p>
                <div class="mail_table">
                    <table cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th class="th_title">件名</th>
                            <td class="td_title" colspan="3">{{ $subject }} ( {{ $customer }} )</td>
                        </tr>
                        <tr>
                            <th class="th_fromt" width="40" rowspan="2">From</th>
                            <td class="td_fromt" width="150">送信者</td>
                            <td class="td_fromt" width="300" id="FROMNAME">
                                <input type="text" name="CHARGE" value="{{ old('CHARGE') }}"
                                    class="w300{{ $errors->has('CHARGE') ? ' error' : '' }}" maxlength="60">
                                <br /><span class="must">{{ $errors->first('CHARGE') }}</span>
                            </td>
                            <td class="td_fromt" width="420">
                                <a href="#" onclick="return popupclass.popupajax('from');">
                                    <img src="{{ asset('img/bt_registered.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_fromb">返信用メールアドレス</td>
                            <td class="td_fromb" colspan="2" id="FROM">
                                <input type="text" name="FROM" value="{{ old('FROM') }}"
                                    class="w300{{ $errors->has('FROM') ? ' error' : '' }}">
                                <br /><span class="must">{{ $errors->first('FROM') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="th_tot" width="40" rowspan="2">To</th>
                            <td class="td_tot">氏名</td>
                            <td id="TONAME" class="td_tot" width="300">
                                <input type="text" name="CUSTOMER_CHARGE" value="{{ old('CUSTOMER_CHARGE') }}"
                                    class="w300{{ $errors->has('CUSTOMER_CHARGE') ? ' error' : '' }}" maxlength="60">
                                <br /><span class="must">{{ $errors->first('CUSTOMER_CHARGE') }}</span>
                            </td>
                            <td class="td_tot" width="440">
                                <a href="#" onclick="return popupclass.popupajax('to', '{{ $cstid }}');">
                                    <img src="{{ asset('img/bt_registered.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="td_tob">メールアドレス</td>
                            <td id="TO" class="td_tob" colspan="2">
                                <input type="text" name="TO" value="{{ old('TO') }}"
                                    class="w300{{ $errors->has('TO') ? ' error' : '' }}">
                                <br /><span class="must">{{ $errors->first('TO') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th class="th_pw" colspan="2">
                                <img src="{{ asset('img/i_pw.jpg') }}" /><br />
                                <p>（データ保護のため受信者がデータをダウンロードする前にパスワードを設定することができます）</p>
                            </th>
                            <td class="td_pw" colspan="2">
                                <input type="text" name="PASSWORD1" value="{{ old('PASSWORD1') }}"
                                    class="w200{{ $errors->has('PASSWORD1') ? ' error' : '' }}">
                                <br /><span class="must">{{ $errors->first('PASSWORD1') }}</span>
                                <br />※パスワードは、自動送信されませんので、別途送信してください。
                            </td>
                        </tr>
                    </table>
                </div>

                <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />

                <input type="hidden" name="USR_ID">
                <input type="hidden" name="SUBJECT" value="{{ $subject }}">
                <input type="hidden" name="TYPE">
                <input type="hidden" name="FRM_ID">
                <input type="hidden" name="tkn">
                <input type="hidden" name="COMPANY" value="{{ $company }}">
                <input type="hidden" name="CUSTOMER" value="{{ $customer }}">

                <div class="edit_btn">
                    <input type="image" src="{{ asset('img/bt_next.jpg') }}" alt="次へ" class="imgover"
                        name="body">
                </div>
            </form>
        </div>
    </div>
</div>
