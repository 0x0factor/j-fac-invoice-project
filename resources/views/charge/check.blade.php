@extends('layout.default')

@section('content')
    <!-- resources/views/charge/view.blade.php -->

    <!-- 完了メッセージ -->
    @if (session()->has('flash'))
        <div class="flash-message">
            {{ session('flash') }}
        </div>
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="">
            <p>こちらのページは自社担当者一覧の画面です。<br />「編集する」ボタンを押下すると自社担当者を変更できます。</p>
        </div>
    </div>

    <br class="clear" />
    <!-- header_End -->
    <!-- contents_Start -->
    <div class="edit_btn">
        @if (isset($editauth))
            <a href="{{ route('charge.edit', ['charge_ID' => $charge['CHR_ID']]) }}" class="imgover">
                <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
            </a>
        @endif
        <form method="POST" action="{{ route('charge.index') }}" style="display:inline;">
            @csrf
            <a href="javascript:move_to_index();" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
            </a>
        </form>
    </div>

    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>
        <h3>
            <div class="edit_01"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="contents_area">
                <form method="POST" action="{{ route('charge.edit', ['charge_ID' => $charge['CHR_ID']]) }}" enctype="multipart/form-data" class="Charge">
\                    @csrf
                    @method('PUT')
                    <table width="880" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>ステータス</th>
                            <td>{{ $status[$charge['STATUS']] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th style="width:150px;"><span class="float_l">担当者名</span></th>
                            <td style="width:730px;">
                                {!! nl2br(e($charge['CHARGE_NAME'])) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">担当者名カナ</span></th>
                            <td>{!! nl2br(e($charge['CHARGE_NAME_KANA'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">部署名</span></th>
                            <td>{!! nl2br(e($charge['UNIT'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">役職名</span></th>
                            <td>{!! nl2br(e($charge['POST'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">メールアドレス</span></th>
                            <td>{!! nl2br(e($charge['MAIL'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">郵便番号</span></th>
                            <td>〒{{ $charge['POSTCODE1'] }}-{{ $charge['POSTCODE2'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">都道府県</span></th>
                            <td>{{ $countys[$charge['CNT_ID']] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">住所</span></th>
                            <td>{!! nl2br(e($charge['ADDRESS'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">建物名</span></th>
                            <td>{!! nl2br(e($charge['BUILDING'])) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">電話番号</span></th>
                            <td>{{ $charge['PHONE_NO1'] }} - {{ $charge['PHONE_NO2'] }} - {{ $charge['PHONE_NO3'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">FAX番号</span></th>
                            <td>{{ $charge['FAX_NO1'] }} - {{ $charge['FAX_NO2'] }} - {{ $charge['FAX_NO3'] }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">担当者印</span></th>
                            <td>
                                {{ $charge['CHARGE_NAME'] }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt=""></td>
                        </tr>
                        <tr>
                            <th><span class="float_l">押印設定</span></th>
                            <td>{{ $seal_flg[$charge['CHR_SEAL_FLG']] }}</td>
                        </tr>
                    </table>
                </form>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="" class="block">
        </div>
        <div class="edit_btn">
            @if (isset($editauth))
                <a href="{{ route('charge.edit', ['charge_ID' => $charge['CHR_ID']]) }}" class="imgover">
                    <img src="{{ asset('img/bt_edit.jpg') }}" alt="編集する">
                </a>
            @endif
            <a href="{{ route('charge.index') }}" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="一覧">
            </a>
        </div>
    </div>

    <input type="hidden" name="USR_ID" value="{{ $user['USR_ID'] }}">
@endsection
