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
        <a href="{{ route($formController . '.edit', ['quoteId' => $param[$formID]]) }}">
            <img src="{{ asset('img/bt_edit.jpg') }}" class="imgover" alt="編集する">
        </a>

        @if ($param['STATUS'] == 1)
            <a href="{{ route('mail.sendmail', ['action' => $mailAction, 'quoteId' => $param[$formID]]) }}">
                <img src="{{ asset('img/bt_send_mail.jpg') }}" class="imgover" alt="メール送付">
            </a>
        @endif
    @endif

    <a href="{{ route($formController . '.download', ['quoteId' => $param[$formID]]) }}">
        <img src="{{ asset('img/bt_download.jpg') }}" class="imgover" alt="ダウンロード">
    </a>

    <a href="{{ route($formController . '.preview', ['quoteId' => $param[$formID]]) }}" target="_blank">
        <img src="{{ asset('img/bt_preview.jpg') }}" class="imgover" alt="プレビュー">
    </a>

    @if ($param['STATUS'] == 1)
        <a href="{{ route($formController . '.download_with_coverpage', ['quoteId' => $param[$formID]]) }}">
            <img src="{{ asset('img/bt_invoice.jpg') }}" class="imgover" alt="ダウンロード">
        </a>
    @endif

    <!-- HTML Form for Action -->
    <form action="{{ url('action') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="Action.type" value="{{ strtolower($formType) }}">
        <input type="hidden" name="{{ $param[$formID] }}" value="1">
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
        <a href="javascript:move_to_index();">
            <img src="{{ asset('img/bt_index.jpg') }}" class="imgover" alt="一覧">
        </a>
    </form>
</div>
