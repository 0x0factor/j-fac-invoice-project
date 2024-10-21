<div class="edit_btn">
    <!-- Save Button -->
    <input type="image" src="{{ asset('img/bt_save3.jpg') }}" name="submit" alt="保存する" class="btn btn-save">

    <!-- Delete Button (Only in Edit Mode) -->
    @if (request()->routeIs('edit'))
        <button type="submit" name="del" alt="削除する" class="imgover imgcheck"
            onmouseover="this.src='{{ asset('img/bt_delete4_on.jpg') }}'"
            onmouseout="this.src='{{ asset('img/bt_delete4.jpg') }}'" onclick="return confirm('削除してもよろしいですか？')">
            <img src="{{ asset('img/bt_delete4.jpg') }}" alt="削除する">
        </button>
        <input type="image" src="{{ asset('img/bt_delete4.jpg') }}" alt="キャンセル" class="imgover imgcheck"
            name="del" onmouseover="this.src='{{ asset('img/bt_delete4_on.jpg') }}'"
            onmouseout="this.src='{{ asset('img/bt_delete4.jpg') }}'" onclick="return confirm('削除してもよろしいですか？')">
    @endif

    <!-- Cancel Button -->
    <input type="image" src="{{ asset('img/bt_index.jpg') }}" alt="キャンセル" class="imgover imgcheck" name="cancel">

</div>

<!-- Hidden Inputs -->
@if (request()->routeIs('edit'))
    @switch($name)
        @case('Quote')
            <input type="hidden" name="MQT_ID">
        @break

        @case('Bill')
            <input type="hidden" name="MBL_ID">
        @break

        @case('Delivery')
            <input type="hidden" name="MDV_ID">
        @break
    @endswitch
@endif

<input type="hidden" name="UPDATE_USR_ID" value="{{ $user['USR_ID'] }}">
<input type="hidden" name="dataformline" value="{{ $dataline }}">
</form>
