@extends('layout.default')

@section('content')
<script>
    $(document).ready(function() {
        if ($('input[name="data[Charge][SEAL_METHOD]"]:checked').val() == 1) {
            $('div.SEAL_METHOD').slideToggle();
        }

        $('input[name="data[Charge][SEAL_METHOD]"]').change(function() {
            $('div.SEAL_METHOD').slideToggle();
        });
    });
</script>
<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
        <p>こちらのページは自社担当者登録の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると自社担当者を作成できます。</p>
    </div>
</div>
<br class="clear" />
<!-- header_End -->

<!-- contents_Start -->
<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
    </div>

    <h3>
        <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
    </h3>

    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top Background">
        <div class="contents_area">
            <form action="{{ route('charge.store') }}" method="post" enctype="multipart/form-data" class="Charge">
                @csrf
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS" class="form-control">
                                @foreach($status as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <tr>
                        <th style="width:150px;" class="{{ $errors->has('CHARGE_NAME') ? 'txt_top' : '' }}"><span class="float_l">担当者名</span><img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r"></th>
                        <td style="width:730px;">
                            <input type="text" name="CHARGE_NAME" value="{{ old('CHARGE_NAME') }}" class="w300{{ $errors->has('CHARGE_NAME') ? ' error' : '' }}" maxlength="60">
                            <br /><span class="usernavi">{{ $usernavi['CHARGE_NAME'] }}</span>
                            <br /><span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                        </td>
                    </tr>
                    <tr><td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line"></td></tr>
                    <!-- Continue translating each table row -->
                    <!-- ... -->
                </table>

                <div class="SEAL_METHOD">
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:150px;">&nbsp;</th>
                            <td>
                                <!-- Add your content here -->
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Add the remaining HTML code -->
                <!-- ... -->

                <div class="edit_btn">
                    <button type="submit" name="submit" class="imgover">
                        <img src="{{ asset('img/bt_save.jpg') }}" alt="保存する">
                    </button>
                    <button type="submit" name="cancel" class="imgover">
                        <img src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル">
                    </button>
                </div>

                <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom Background" class="block">
    </div>
</div>
@endsection
