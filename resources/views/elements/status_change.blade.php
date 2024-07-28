<div class="status_change">
    <div class="status_text">発行ステータス一括変更</div>

    {{-- Dropdown for STATUS_CHANGE --}}
    <select name="STATUS_CHANGE" id="status_change" class="form-control">
        @if (is_array($status) || is_object($status))
            @foreach ($status as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        @else
            <option value="" disabled>No status available</option>
        @endif
    </select>

    {{-- Submit Button --}}

    <input type="image" src="{{ asset('img/bt_set.jpg') }}" name="status_change" alt="ステータス変更" class="mr5"
        onclick="return status_change();">

</div>
