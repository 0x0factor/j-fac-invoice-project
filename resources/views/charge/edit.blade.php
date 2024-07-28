@extends('layout.default')

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('input[name="data[Charge][SEAL_METHOD]"]:checked').val() == 1) {
                $('div.SEAL_METHOD').slideToggle();
            }
            $('input[name="data[Charge][SEAL_METHOD]"]').change(function() {
                $('div.SEAL_METHOD').slideToggle();
            });
        });
    </script>
@endsection

@section('content')
    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
            <p>こちらのページは自社担当者編集の画面です。<br />必要な情報を入力の上「保存する」ボタンを押下すると自社担当者の変更を保存できます。</p>
        </div>
    </div>
    <br class="clear" />

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Image">
        </div>

        <h3>
            <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Background Top">
            <div class="contents_area">
                <form action="{{ route('charge.store') }}" method="POST" enctype="multipart/form-data" class="Charge">
                    @csrf
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <select name="STATUS" class="form-control">
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line Image"></td>
                        </tr>
                        <tr>
                            <th style="width:150px;">担当者名</th>
                            <td style="width:730px;">
                                <input type="text" name="CHARGE_NAME" value="{{ old('CHARGE_NAME') }}"
                                    class="form-control w300{{ $errors->has('CHARGE_NAME') ? ' error' : '' }}"
                                    maxlength="60">
                                <br /><span class="usernavi">{{ $usernavi['CHARGE_NAME'] }}</span>
                                <br /><span class="must">{{ $errors->first('CHARGE_NAME') }}</span>
                            </td>
                        </tr>
                        <!-- Add other form fields similarly -->
                        <!-- Remaining form fields -->
                    </table>

                    <div class="SEAL_METHOD">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <th style="width:150px;">&nbsp;</th>
                                <td>
                                    @if (isset($image))
                                        <img src="{{ asset('img/' . $image->path) }}" alt="Uploaded Image" width="100"
                                            height="100">
                                    @endif
                                    <input type="file" name="image" class="form-control">
                                    <input type="checkbox" name="DEL_SEAL" style="width:30px;">削除
                                    <br /><span class="usernavi">{{ $usernavi['SEAL'] }}</span>
                                    <br /><span class="must">{{ $ierror == 1 ? '画像はjpeg,png,gifのみです' : '' }}</span>
                                    <br /><span class="must">{{ $ierror == 2 ? '1MB以上の画像は登録できません' : '' }}</span>
                                    <br /><span class="must">{{ $ierror == 3 ? '正しい画像を指定してください' : '' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="SEAL_METHOD" style="display:none">
                        <table width="880" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <th style="width:150px;">&nbsp;</th>
                                <td>
                                    <input type="text" name="SEAL_STR" value="{{ old('SEAL_STR') }}"
                                        class="form-control w300" maxlength="4">
                                    <br /><span class="usernavi">{{ $usernavi['SEAL_METHOD'] }}</span>
                                    <br /><span class="must">{{ $errors->first('SEAL_STR') }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}"
                                    alt="Line Image"></td>
                        </tr>
                        <tr>
                            <th>押印設定</th>
                            <td>
                                <!-- Example for radio buttons -->
                                <!-- {!! Form::radio('CHR_SEAL_FLG', $value, $seal_flg) !!} -->
                                <!-- {!! Form::radio('CHR_SEAL_FLG', $value, $seal_flg) !!} -->
                                <input type="radio" name="CHR_SEAL_FLG" value="{{ $seal_flg }}"
                                    class="ml20 mr5 txt_mid">
                                <br /><span class="usernavi">{{ $usernavi['CHR_SEAL_FLG'] }}</span>
                                <br /><span class="must">{{ $errors->first('CHR_SEAL_FLG') }}</span>
                            </td>
                        </tr>
                    </table>

                    <button type="submit" name="submit" class="imgover">保存する</button>
                    <button type="submit" name="cancel" class="imgover">キャンセル</button>

                    <input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
                    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="CHR_ID">
                </form>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Background Bottom" class="block">
        </div>
    </div>
@endsection
