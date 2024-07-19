@extends('layout.default')

@section('content')

<script type="text/javascript">
<!--
function set_default(_option) {
	if(_option == 'logo') {
		$('#ViewOptionLogoDefault').val('i_logo.jpg');
		$('#default_logo').attr('style','');
		$('#logo_image').attr('style','display: none');
		$('#logo_name').html('i_logo.jpg');
		$('#ViewOptionLogo').val('');
		$('#logo_reset').attr('style','display: none');

	}
	if(_option == 'title') {
		$('#ViewOptionTitle').val('抹茶請求書');
	}
	if(_option == 'footer') {
		$('#ViewOptionFooter').val('抹茶請求書');
	}
	return false;

}

function reset_default(){
	$('#ViewOptionLogoDefault').val(0);

}
// -->
</script>

@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session()->get('message') }}
    </div>
@endif

<div id="guide">
    <div id="guide_box" class="clearfix">
        <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide">
        <p>こちらのページはデザイン設定編集の画面です。@if($user["AUTHORITY"] == 0)<br />必要な情報を入力の上「保存する」ボタンを押下するとデザイン設定を変更できます。@endif</p>
    </div>
</div>
<br class="clear" />

<div id="contents">
    <div class="arrow_under">
        <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow">
    </div>
    <h3>
        <div class="edit_02_view_option">
            <span class="edit_txt">&nbsp;</span>
        </div>
    </h3>
    <div class="contents_box">
        <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Background">
        <div class="contents_area">
            <form action="{{ route('view_options.update') }}" method="POST" enctype="multipart/form-data" class="ViewOption">
                @csrf
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    @foreach($options as $option)
                        @php
                            $option_name = $option['ViewOption']['OPTION_NAME'];
                            $option_name_jp = $option['ViewOption']['OPTION_NAME_JP'];
                            $option_value = $option['ViewOption']['OPTION_VALUE'];
                        @endphp
                        <tr>
                            <th width="330px">{{ $option_name_jp }}</th>
                            <td width="550px">
                                @if($option_name === 'logo')
                                    @if(!empty($option_value))
                                        <img src="{{ asset('cms/i_logo.jpg') }}" height="40" id="default_logo" style="display: none" alt="Default Logo">
                                        <img src="{{ asset('cms/' . $option_value) }}" height="40" id="logo_image" alt="Logo Image">
                                        <span id="logo_name">{{ $option_value }}</span><br /><br />
                                    @endif
                                    <input type="file" name="{{ $option_name }}" onclick="reset_default()"><br />
                                    @if($option_value !== 'i_logo.jpg')
                                        <a href="#" id="logo_reset" onclick="return set_default('{{ $option_name }}');">
                                            <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="Reset">
                                        </a>
                                    @endif
                                    <input type="hidden" name="logo_default" value="0">
                                    <br /><span class="usernavi">{{ $usernavi['LOGO'] }}</span>
                                @else
                                    <input type="text" name="{{ $option_name }}" value="{{ old($option_name, $option_value) }}" class="w600{{ $errors->has($option_name) ? ' error' : '' }}" maxlength="100">
                                    <a href="#" onclick="return set_default('{{ $option_name }}');">
                                        <img src="{{ asset('img/bt_s_reset.jpg') }}" alt="Reset">
                                    </a>
                                @endif
                                <br />
                                <span class="must">
                                    @if($option_name === 'logo' && isset($logo_error))
                                        @switch($logo_error)
                                            @case(0)
                                                @break
                                            @case(1)
                                                画像はjpegかpngのみです<br />
                                                @break
                                            @case(2)
                                                画像サイズは1MBまでです<br />
                                                @break
                                            @case(3)
                                                画像はjpegかpngのみです<br />
                                                画像サイズは1MBまでです<br />
                                                @break
                                            @case(4)
                                                正しい画像形式ではありません
                                                @break
                                        @endswitch
                                    @else
                                        @error($option_name)
                                            {{ $message }}
                                        @enderror
                                    @endif
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line">
                                <img src="{{ asset('img/i_line_solid.gif') }}" alt="Line">
                            </td>
                        </tr>
                    @endforeach
                </table>
                <div class="edit_btn">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" name="submit" class="imgover">
                        <img src="{{ asset('img/bt_save.jpg') }}" alt="保存する">
                    </button>
                    <button type="submit" name="cancel" class="imgover">
                        <img src="{{ asset('img/bt_cancel.jpg') }}" alt="キャンセル">
                    </button>
                </div>
            </form>
        </div>
        <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="Bottom Background">
    </div>
</div>
@endsection
