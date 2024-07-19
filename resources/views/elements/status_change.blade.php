<div class="status_change">
    <div class="status_text">発行ステータス一括変更</div>

    {{-- Dropdown for STATUS_CHANGE --}}
    <select name="STATUS_CHANGE" id="status_change" class="form-control">
        @foreach($status as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>

    {{-- Submit Button --}}
    <button
        type="submit"
        name="status_change"
        onclick="return status_change();"
        class="mr5"
    >
        <img src="{{ asset('img/bt_set.jpg') }}" alt="ステータス変更">
    </button>
</div>
