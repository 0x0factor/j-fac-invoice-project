<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" />
        <div class="contents_area">
            <div class="finish">データの送信が完了しました。</div>

            <div class="finish_txt">
                ただいまご指定のアドレスへデータを送信しました。<br />
                送信先よりコメントが届いた場合については
                <a href="{{ route('mails.index') }}">顧客コメント確認ページ</a>でご確認ください。<br />
                指定したアドレスへ届いていない場合については、送信先をご確認のうえ、再送ください。
                パスワードは別途送信してください。<br />
                <h4 class='h4_mailpass'>パスワード：{{ $pass }}</h4>
            </div>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
    </div>

    <div class="edit_btn">
        <a href="{{ route('mails.index') }}">
            <img src="{{ asset('img/bt_mailindex.jpg') }}" class="imgover" alt="メール送信一覧へ" />
        </a>
    </div>
</div>
