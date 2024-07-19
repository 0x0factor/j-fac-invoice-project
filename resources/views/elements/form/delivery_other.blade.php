{{-- resources/views/your-view.blade.php --}}
@php
    $formType = $name; // assuming $name is passed from the controller
@endphp

<h3>
    <div class="edit_03" align="right">
        <span class="show_bt3_on" style="display:none">
            <img src="{{ asset('img/button/hide.png') }}" class="imgover" alt="on" onclick="return edit3_toggle('on');">
        </span>
        <span class="show_bt3_off" onclick="return edit3_toggle('off');">
            <img src="{{ asset('img/button/show.png') }}" class="imgover" alt="off">
        </span>
        <span class="edit_txt">&nbsp;</span>
    </div>
</h3>

<div class="contents_box">
    <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Top Background">

    <div class="contents_area4" style="display:none">
        <table width="880" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <th class="txt_top">発行ステータス</th>
                <td>
                    @if(request()->route()->getActionMethod() == 'edit')
                        <select name="STATUS" class="form-control">
                            @foreach(config('constants.issued_stat_code') as $key => $value)
                                <option value="{{ $key }}" {{ old('STATUS', $status) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @else
                        <select name="STATUS" class="form-control">
                            @foreach(config('constants.issued_stat_code') as $key => $value)
                                <option value="{{ $key }}" {{ old('STATUS', 1) == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>
            </tr>

            <tr>
                <td colspan="2" class="line">
                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line">
                </td>
            </tr>
            <tr>
                <th class="{{ $errors->has('DELIVERY') ? 'txt_top' : '' }}">納入場所</th>
                <td>
                    <input type="text" name="DELIVERY" value="{{ old('DELIVERY') }}" class="w320 mr10{{ $errors->has('DELIVERY') ? ' error' : '' }}" maxlength="40" onkeyup="count_strw('delivery_rest', this.value, 20)">
                    <span id="delivery_rest"></span>
                    <br /><span class="usernavi">{{ $usernavi['DELIVERY'] }}</span>
                    <br /><span class="must">{{ $errors->first('DELIVERY') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="line">
                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line">
                </td>
            </tr>
            <tr>
                <th class="txt_top">備考</th>
                <td>
                    <textarea name="NOTE" class="textarea{{ $errors->has('NOTE') ? ' error' : '' }}" onkeyup="count_strw('note_rest', this.value, 300)">{{ old('NOTE') }}</textarea>
                    <br /><span id="note_rest"></span>
                    <br /><span class="usernavi">{{ $usernavi['NOTE'] }}</span>
                    <br /><span class="must">{{ $errors->first('NOTE') }}</span>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="line">
                    <img src="{{ asset('img/i_line_solid.gif') }}" alt="Solid Line">
                </td>
            </tr>
            <tr>
                <th class="{{ $errors->has('MEMO') ? 'txt_top' : '' }}">メモ</th>
                <td>
                    <input type="text" name="MEMO" value="{{ old('MEMO') }}" class="w320{{ $errors->has('MEMO') ? ' error' : '' }}" maxlength="100" onkeyup="count_strw('memo_rest', this.value, 50)">
                    <span id="memo_rest"></span>
                    <br /><span class="usernavi">{{ $usernavi['MEMO'] }}</span>
                    <br /><span class="must">{{ $errors->first('MEMO') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Bottom Background">
</div>
