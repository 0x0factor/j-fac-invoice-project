@extends('layout.default')

@section('scripts')
    <script type="text/javascript">
        <!--
        function charge_reset() {
            $('#SETCHARGE').children('input[type=text]').val('');
            $('#SETCHARGE').children('input[type=hidden]').val(0);
            return false;
        }
        // 
        -->
    </script>
@endsection

@section('content')
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
            <p>こちらのページは顧客情報設定の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると顧客情報の変更を保存できます。</p>
        </div>
    </div>
    <br class="clear" />

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <form action="{{ route('customers.store') }}" method="POST" class="Customer">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th style="width:130px;" @if ($errors->has('NAME')) class="txt_top" @endif>
                                <span class="float_l">社名</span>
                                <img src="{{ asset('img/i_must.jpg') }}" alt="必須" class="pl10 mr10 float_r">
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="NAME" value="{{ old('NAME') }}"
                                    class="w300{{ $errors->has('NAME') ? ' error' : '' }}" maxlength="60">
                                <br /><span class="must">
                                    @error('NAME')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line Solid"></td>
                        </tr>
                        <!-- Repeat for other fields -->
                        <!-- Example for phone number -->
                        <tr>
                            <th style="width:130px;" @if ($errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3')) class="txt_top" @endif>
                                <span class="float_l">電話番号</span>
                            </th>
                            <td style="width:750px;">
                                <input type="text" name="PHONE_NO1" value="{{ old('PHONE_NO1') }}"
                                    class="w60{{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? ' error' : '' }}"
                                    maxlength="5">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO2" value="{{ old('PHONE_NO2') }}"
                                    class="w60{{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? ' error' : '' }}"
                                    maxlength="4">
                                <span class="pl5 pr5">-</span>
                                <input type="text" name="PHONE_NO3" value="{{ old('PHONE_NO3') }}"
                                    class="w60{{ $errors->has('PHONE_NO1') || $errors->has('PHONE_NO2') || $errors->has('PHONE_NO3') ? ' error' : '' }}"
                                    maxlength="4">
                                <br /><span class="usernavi">{{ $usernavi['PHONE'] }}</span>
                                <br /><span class="must">
                                    @error('PHONE_NO1')
                                        {{ $message }}
                                    @enderror
                                </span>
                            </td>
                        </tr>
                        <!-- Other fields can be added similarly -->
                    </table>
                    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
                    <input type="hidden" name="CST_ID">
                    <button type="submit" name="submit" class="imgover" alt="保存する"><img
                            src="{{ asset('img/bt_save.jpg') }}" alt="保存する"></button>
                    <button type="submit" name="cancel" class="imgover" alt="キャンセル"><img
                            src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル"></button>
                </form>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
        </div>

        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Under">
        </div>

        <h3>
            <div class="company_02"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <span class="usernavi">{{ $usernavi['CUSTOMER_PAYMENT'] }}</span>
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:130px;" @if ($errors->has('CUTOOFF_DATE')) class="txt_top" @endif>締日</th>
                        <td style="width:750px;">
                            <select name="CUTOOFF_SELECT" class="txt_mid">
                                @foreach ($cutooff_select as $key => $value)
                                    <option value="{{ $key }}"
                                        {{ old('CUTOOFF_SELECT') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="CUTOOFF_DATE" value="{{ old('CUTOOFF_DATE') }}"
                                class="w60 mr5 ml5{{ $errors->has('CUTOOFF_DATE') ? ' error' : '' }}" maxlength="2">日
                            <br /><span class="usernavi">{{ $usernavi['CST_CUTOOFF'] }}</span>
                            <br /><span class="must">
                                @error('CUTOOFF_DATE')
                                    {{ $message }}
                                @enderror
                            </span>
                        </td>
                    </tr>
                    <!-- Repeat for other fields -->
                    <!-- Example for text area -->
                    <tr>
                        <th class="txt_top">備考</th>
                        <td>
                            <textarea name="NOTE" class="textarea{{ $errors->has('NOTE') ? ' error' : '' }}" maxlength="1000">{{ old('NOTE') }}</textarea>
                            <br /><span class="must">
                                @error('NOTE')
                                    {{ $message }}
                                @enderror
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom" class="block">
        </div>
    </div>
@endsection
