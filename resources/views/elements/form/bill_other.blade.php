{{-- Include necessary CSS and JS --}}
@section('link')
    <!-- Add your CSS here -->
@endsection
@section('scripts')
    <script>
        // JavaScript for toggling visibility and counting characters
        function edit3_toggle(state) {
            if (state === 'on') {
                document.querySelector('.contents_area4').style.display = 'none';
                document.querySelector('.show_bt3_on').style.display = 'none';
                document.querySelector('.show_bt3_off').style.display = 'inline';
            } else {
                document.querySelector('.contents_area4').style.display = 'block';
                document.querySelector('.show_bt3_on').style.display = 'inline';
                document.querySelector('.show_bt3_off').style.display = 'none';
            }
            return false;
        }

        function count_strw(id, value, maxLength) {
            document.getElementById(id).innerText = `${value.length}/${maxLength}`;
        }
    </script>
@endsection



{{-- Toggle Buttons --}}
<h3>
    <div class="edit_03" align="right">
        <span class="show_bt3_on" style="display:none">
            <img src="{{ asset('img/button/hide.png') }}" alt="on" class="imgover"
                onclick="return edit3_toggle('on');">
        </span>
        <span class="show_bt3_off" onclick="return edit3_toggle('off');">
            <img src="{{ asset('img/button/show.png') }}" alt="off" class="imgover">
        </span>
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background Top">
    <div class="contents_area4" style="display:none">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="txt_top">発行ステータス</th>
                <td>

                    @if (request()->route()->getActionMethod() == 'edit')

                        <select name="STATUS" class="form-control">
                            @foreach (config('constants.issued_stat_code') as $key => $value)
                                <option value="{{ $key }}"
                                    {{ old('STATUS', $status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @else
                        @php
                            // Retrieve the config value and ensure it's an array
                            $issuedStatCode = config('constants.issued_stat_code', []);
                        @endphp
                        <select name="STATUS" class="form-control">
                            @foreach ($issuedStatCode as $key => $value)
                                <option value="{{ $key }}" {{ old('STATUS', 1) == $key ? 'selected' : '' }}>
                                    {{ $value }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>
            </tr>

            <tr>
                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>

            <tr>
                <th class="{{ $errors->has('FEE') ? 'txt_top' : '' }}">振込手数料</th>
                <td>
                    <input type="text" name="FEE" value="{{ old('FEE') }}"
                        class="w320 mr10{{ $errors->has('FEE') ? ' error' : '' }}" maxlength="40"
                        onkeyup="count_strw('fee_rest', this.value, 20)" id="BillFEE">
                    <span id="fee_rest"></span>
                    <br><span class="usernavi">{{ $usernavi['FEE'] }}</span>
                    <br><span class="must">{{ $errors->first('FEE') }}</span>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>

            <tr>
                <th class="{{ $errors->has('DUE_DATE') ? 'txt_top' : '' }}">振込期限</th>
                <td>
                    <input type="text" name="DUE_DATE" value="{{ old('DUE_DATE') }}"
                        class="w320 mr10{{ $errors->has('DUE_DATE') ? ' error' : '' }}" maxlength="40"
                        onkeyup="count_strw('due_date_rest', this.value, 20)" id="BillDUEDATE">
                    <span id="due_date_rest"></span>
                    <br><span class="usernavi">{{ $usernavi['DUE_DATE'] }}</span>
                    <br><span class="must">{{ $errors->first('DUE_DATE') }}</span>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>

            <tr>
                <th class="txt_top">備考</th>
                <td>
                    <textarea name="NOTE" class="textarea{{ $errors->has('NOTE') ? ' error' : '' }}"
                        onkeyup="count_strw('note_rest', this.value, 300)" rows="4">
                        {{ old('NOTE') }}
                    </textarea>
                    <br><span id="note_rest"></span>
                    <span class="usernavi">{{ $usernavi['NOTE'] }}</span>
                    <br><span class="must">{{ $errors->first('NOTE') }}</span>
                </td>
            </tr>

            <tr>
                <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line"></td>
            </tr>

            <tr>
                <th class="{{ $errors->has('MEMO') ? 'txt_top' : '' }}">メモ</th>
                <td>
                    <input type="text" name="MEMO" value="{{ old('MEMO') }}"
                        class="w320{{ $errors->has('MEMO') ? ' error' : '' }}" maxlength="100" id="BillMEMO"
                        onkeyup="count_strw('memo_rest', this.value, 50)">
                    <span id="memo_rest"></span>
                    <br><span class="usernavi">{{ $usernavi['MEMO'] }}</span>
                    <br><span class="must">{{ $errors->first('MEMO') }}</span>
                </td>
            </tr>
        </table>
    </div>
    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Background Bottom" class="block">
</div>
