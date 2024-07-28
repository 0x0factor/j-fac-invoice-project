@extends('layout.default')

@section('content')
    @if (session()->has('flash'))
        {{ session('flash') }}
    @endif

    <div id="guide">
        <div id="guide_box" class="clearfix">
            <img src="{{ asset('img/company/i_guide.jpg') }}" alt="Guide Image">
            <p>こちらのページは取引先担当者確認の画面です。<br>「編集する」ボタンを押すと取引先担当者を編集することができます。</p>
        </div>
    </div>

    <br class="clear">

    <!-- contents_Start -->
    <div id="contents">
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Image"></div>
        <div class="edit_btn2">
            @if ($editauth)
                <a href="{{ url('customer_charges/edit/' . $chrcID) }}" class="imgover"
                    onclick="return confirm('Are you sure you want to edit?')">
                    <img src="{{ asset('img/bt_edit.jpg') }}" alt="Edit Image">
                </a>
            @endif

            {!! Form::open(['url' => 'moveback', 'method' => 'post', 'style' => 'display:inline;']) !!}
            <a href="javascript:move_to_index();" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="Index Image">
            </a>
            {!! Form::close() !!}
        </div>

        <h3>
            <div class="edit_01_c_charge"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box">
            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="Contents Top Image">
            <div class="contents_area">
                <table width="880" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th style="width:150px;">ステータス</th>
                        <td style="width:730px;">{{ $status[$CustomerCharge['STATUS']] }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>顧客名</th>
                        <td>{{ !empty($CustomerCharge['CST_ID']) ? $customer : '' }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>担当者名</th>
                        <td>{{ nl2br(e($CustomerCharge['CHARGE_NAME'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>担当者名カナ</th>
                        <td>{{ nl2br(e($CustomerCharge['CHARGE_NAME_KANA'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>部署名</th>
                        <td>{{ nl2br(e($CustomerCharge['UNIT'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>役職名</th>
                        <td>{{ nl2br(e($CustomerCharge['POST'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>メールアドレス</th>
                        <td>{{ nl2br(e($CustomerCharge['MAIL'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>郵便番号</th>
                        <td>
                            @if (!empty($CustomerCharge['POSTCODE1']) && !empty($CustomerCharge['POSTCODE2']))
                                {{ $CustomerCharge['POSTCODE1'] . ' - ' . $CustomerCharge['POSTCODE2'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>都道府県</th>
                        <td>
                            @if (!empty($CustomerCharge['CNT_ID']))
                                {{ $countys[$CustomerCharge['CNT_ID']] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>住所</th>
                        <td>{{ nl2br(e($CustomerCharge['ADDRESS'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>建物名</th>
                        <td>{{ nl2br(e($CustomerCharge['BUILDING'])) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>電話番号</th>
                        <td>
                            @if (
                                !empty($CustomerCharge['PHONE_NO1']) &&
                                    !empty($CustomerCharge['PHONE_NO2']) &&
                                    !empty($CustomerCharge['PHONE_NO3']))
                                {{ $CustomerCharge['PHONE_NO1'] . ' - ' . $CustomerCharge['PHONE_NO2'] . ' - ' . $CustomerCharge['PHONE_NO3'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>

                    <tr>
                        <th>FAX番号</th>
                        <td>
                            @if (!empty($CustomerCharge['FAX_NO1']) && !empty($CustomerCharge['FAX_NO2']) && !empty($CustomerCharge['FAX_NO3']))
                                {{ $CustomerCharge['FAX_NO1'] . ' - ' . $CustomerCharge['FAX_NO2'] . ' - ' . $CustomerCharge['FAX_NO3'] }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="line"><img src="{{ asset('img/i_line_solid.gif') }}" alt="Line Image">
                        </td>
                    </tr>
                </table>
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" alt="Contents Bottom Image" class="block">
        </div>
        <div class="arrow_under"><img src="{{ asset('img/i_arrow_under.jpg') }}" alt="Arrow Image"></div>
        <div class="edit_btn2">
            @if ($editauth)
                <a href="{{ url('customer_charges/edit/' . $chrcID) }}" class="imgover"
                    onclick="return confirm('Are you sure you want to edit?')">
                    <img src="{{ asset('img/bt_edit.jpg') }}" alt="Edit Image">
                </a>
            @endif
            <a href="{{ url('customer_charges/index') }}" class="imgover">
                <img src="{{ asset('img/bt_index.jpg') }}" alt="Index Image">
            </a>
        </div>
    </div>
@endsection
