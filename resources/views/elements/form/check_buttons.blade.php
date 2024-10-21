<!-- resources/views/your-view.blade.php -->
@php
    $formType = $controller_name; // assuming $name is passed from the controller
    $formID = '';
    $formController = '';
    $mailAction = '';

    switch ($formType) {
        case 'Quote':
            $formID = 'MQT_ID';
            $formController = 'quote';
            $mailAction = 'quote';
            break;
        case 'Bill':
            $formID = 'MBL_ID';
            $formController = 'bill';
            $mailAction = 'bill';
            break;
        case 'Delivery':
            $formID = 'MDV_ID';
            $formController = 'delivery';
            $mailAction = 'delivery';
            break;
    }
@endphp

<div class="edit_btn2">
    @if ($editauth)
    
        <a href="{{ route($formController . '.edit', [$param[$formType][$formID] ?? '0']) }}">
            <img src="{{ asset('img/bt_edit.jpg') }}" class="imgover" alt="編集する">
        </a>

        @if (isset($param[$formType]['STATUS']) && $param[$formType]['STATUS'] == 1)
            <a href="{{ route('mail.sendmail', ['action' => $mailAction, 'quoteId' => $param[$formType][$formID] ?? null]) }}">
                <img src="{{ asset('img/bt_send_mail.jpg') }}" class="imgover" alt="メール送付">
            </a>
        @endif
    @endif

    <a href="{{ route($formController . '.download', ['quoteId' => $param[$formType][$formID] ?? null]) }}">
        <img src="{{ asset('img/bt_download.jpg') }}" class="imgover" alt="ダウンロード">
    </a>

    <a href="{{ route($formController . '.preview', ['quoteId' => $param[$formType][$formID] ?? null]) }}" target="_blank">
        <img src="{{ asset('img/bt_preview.jpg') }}" class="imgover" alt="プレビュー">
    </a>

    @if (isset($param[$formType]['STATUS']) && $param[$formType]['STATUS'] == 1)
        <a href="{{ route($formController . '.download_with_coverpage', ['quoteId' => $param[$formType][$formID] ?? null]) }}">
            <img src="{{ asset('img/bt_invoice.jpg') }}" class="imgover" alt="ダウンロード">
        </a>
    @endif

    <!-- HTML Form for Action -->
    <form action="{{ url('action') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="Action.type" value="{{ strtolower($formType) }}">
        <input type="hidden" name="{{ $formType }}[{{ $formID }}]" value="{{ $param[$formType][$formID] ?? '' }}">
        <button type="submit" style="vertical-align:bottom;border:none;" 
            onmouseover="this.src='{{ asset('img/bt_copy_on.jpg') }}'"
            onmouseout="this.src='{{ asset('img/bt_copy.jpg') }}'">
            <img src="{{ asset('img/bt_copy.jpg') }}" alt="転記">
        </button>
    </form>

    @if (isset($rb_flag) && $rb_flag && $formType == 'Bill')
        <a href="{{ route('regularbill.add', ['quoteId' => $param[$formID]]) }}">
            <img src="{{ asset('img/bt_rb_copy.jpg') }}" class="imgover" alt="定期請求書へコピー">
        </a>
    @endif

    <!-- HTML Form for Move Back -->
    <form action="{{ url('moveback') }}" method="POST" style="display:inline;">
        @csrf
        <a href="{{ route('home') }}">
            <img src="{{ asset('img/bt_index.jpg') }}" class="imgover" alt="ホーム">
        </a>
    </form>
</div>
