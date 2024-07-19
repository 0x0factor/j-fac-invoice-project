<div id="contents">
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" class="block" />
        <div class="contents_area">
            <form method="POST" action="{{ url('mail') }}">
                @csrf <!-- CSRF Protection -->

                <h3 class="mail_h3">本文</h3>

                <textarea name="BODY" rows="10" class="pt5 pr5 pb5 pl5 mailtextarea">{{ $body }}</textarea>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" />
    </div>

    <input type="hidden" name="PASSWORD1" value="{{ $PASSWORD1 }}">
    <input type="hidden" name="USR_ID" value="{{ $USR_ID }}">
    <input type="hidden" name="TYPE" value="{{ $TYPE }}">
    <input type="hidden" name="FRM_ID" value="{{ $FRM_ID }}">
    <input type="hidden" name="TO" value="{{ $TO }}">
    <input type="hidden" name="FROM" value="{{ $FROM }}">
    <input type="hidden" name="CUSTOMER_CHARGE" value="{{ $CUSTOMER_CHARGE }}">
    <input type="hidden" name="CHARGE" value="{{ $CHARGE }}">
    <input type="hidden" name="SUBJECT" value="{{ $SUBJECT }}">
    <input type="hidden" name="COMPANY" value="{{ $COMPANY }}">
    <input type="hidden" name="CUSTOMER" value="{{ $CUSTOMER }}">
    <input type="hidden" name="tkn" value="{{ $tkn }}">
    <input type="hidden" name="CORD" value="{{ $hash }}">

    <div class="edit_btn">
        <button type="submit" name="mail" class="imgover">
            <img src="{{ asset('img/bt_back.jpg') }}" alt="戻る">
        </button>
        <button type="submit" name="reaffirmation" class="imgover">
            <img src="{{ asset('img/bt_check.jpg') }}" alt="確認">
        </button>
    </div>
    </form>
</div>
