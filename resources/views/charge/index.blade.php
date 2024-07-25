@extends('layout.default')

@section('content')
@php
    $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
<script>
    try {
        window.addEventListener("load", function() {
            initTableRollovers('index_table');
        }, false);
    } catch (e) {
        window.attachEvent("onload", function() {
            initTableRollovers('index_table');
        });
    }
    </script>

    <script>
    function select_all() {
        $(".chk").prop("checked", $(".chk_all").prop("checked"));  // Correct usage
        $('input[name="delete"]').prop('disabled', false);  // Correct usage
        $('input[name="reproduce"]').prop('disabled', false);  // Correct usage
    }
    </script>

    <script>
    $(function() {
        @if(isset($name) && isset($action))
            setBeforeSubmit('{{ $name . ucfirst($action) . 'Form' }}');
        @else
            console.error("Name or action is not set.");
        @endif
    });
    </script>
{{-- Display flash message --}}
{{ session()->get('flash') }}

{{-- Begin form for search --}}
<form method="GET" action="{{ route('charge.index') }}">
    @csrf
    <div id="contents">
        <div class="arrow_under">
            <img src="{{ asset('img/i_arrow_under.jpg') }}" alt="">
        </div>

        <h3>
            <div class="search"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="search_box">
            <div class="search_area">
                <table width="600" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <th>担当者名</th>
                        <td><input type="text" name="CHARGE_NAME" class="w350"></td>
                    </tr>
                    <tr>
                        <th>部署名</th>
                        <td><input type="text" name="UNIT" class="w350"></td>
                    </tr>
                    <tr>
                        <th>ステータス</th>
                        <td>
                            <select name="STATUS">
                                <option value="" disabled selected>項目を選んでください</option>
                                @if(!empty($status) && (is_array($status) || is_object($status)))
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @else
                                    <option value=""></option>
                                @endif
                            </select>
                        </td>
                    </tr>
                </table>

                <div class="search_btn">
                    <table style="margin-left:-80px;">
                        <tr>
                            <td style="border:none;">
                                <a href="#" onclick="document.getElementById('chargeSearchForm').submit();">
                                    <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                                </a>
                            </td>
                            <td style="border:none;">
                                <a href="#" onclick="reset_forms();">
                                    <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="">
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <img src="{{ asset('img/document/bg_search_bottom.jpg') }}" class="block" alt="">
        </div>
        <div class="new_document">
            <a href="{{ route('charge.add') }}">
                <img src="{{ asset('img/bt_new.jpg') }}" alt="">
            </a>
        </div>

        <h3>
            <div class="edit_02_charge"><span class="edit_txt">&nbsp;</span></div>
        </h3>

        <div class="contents_box mb40">
            <div id='pagination'>
                {{ $paginator->total() }}
            </div>
            
            <div id='pagination'>
                <!-- Previous Page Link -->
                @if ($paginator->onFirstPage())
                    <span class="disabled"><< {{ __('前へ') }}</span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"><< {{ __('前へ') }}</a>
                @endif

                <!-- Pagination Elements -->
                @foreach ($paginator->links()->elements as $element)
                    <!-- "Three Dots" Separator -->
                    @if (is_string($element))
                        <span class="disabled">{{ $element }}</span>
                    @endif

                    <!-- Array Of Links -->
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <!-- Next Page Link -->
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next">{{ __('次へ') }} >></a>
                @else
                    <span class="disabled">{{ __('次へ') }} >></span>
                @endif
            </div>

            <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
            <div class="list_area">
                @if (is_array($list))
                    <form method="POST" action="{{ route('charge.delete') }}">
                        @csrf
                        <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                            <thead>
                                <tr>
                                    <th class="w50"><input type="checkbox" class="chk_all" onclick="select_all();"></th>
                                    <th class="w50">No.</th>
                                    <th class="w200">担当者名</th>
                                    <th class="w200">部署名</th>
                                    <th class="w200">電話番号</th>
                                    <th class="w100">印鑑</th>
                                    <th class="w100">ステータス</th>
                                    @if ($user['AUTHORITY'] != 1)
                                        <th class="w100">作成者</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($list as $key => $val)
                                    <?php $val['Charge']['SEAL'] = !empty($val['Charge']['SEAL']) ? 0 : 1; ?>
                                    <tr>
                                        <td>
                                            @if (empty($delcheck[$val['Charge']['CHR_ID']]))
                                                <input type="checkbox" name="{{ $val['Charge']['CHR_ID'] }}" class="chk">
                                            @else
                                                &nbsp;
                                            @endif
                                        </td>
                                        <td>{{ $val['Charge']['CHR_ID'] }}</td>
                                        <td>
                                            @if ($authcheck[$val['Charge']['CHR_ID']] == 1)
                                                <a href="{{ route('charge.check', $val['Charge']['CHR_ID']) }}">{{ $val['Charge']['CHARGE_NAME'] }}</a>
                                            @else
                                                {{ nl2br($val['Charge']['CHARGE_NAME']) }}
                                            @endif
                                        </td>
                                        <td>{{ $customHtml->ht2br($val['Charge']['UNIT']) ?: "&nbsp;" }}</td>
                                        <td>
                                            @if (!empty($val['Charge']['PHONE_NO1']) || !empty($val['Charge']['PHONE_NO2']) || !empty($val['Charge']['PHONE_NO3']))
                                                {{ $val['Charge']['PHONE_NO1'] . '-' . $val['Charge']['PHONE_NO2'] . '-' . $val['Charge']['PHONE_NO3'] }}
                                            @endif
                                        </td>
                                        <td>{{ $seal[$val['Charge']['SEAL']] }}</td>
                                        <td>{{ $status[$val['Charge']['STATUS']] }}</td>
                                        @if ($user['AUTHORITY'] != 1)
                                            <td>{{ $val['User']['NAME'] }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="list_btn">
                            <input type="submit" name="delete" value="削除" onclick="return del();" class="mr5">
                        </div>
                    </form>
                @endif
            </div>
            <img src="{{ asset('img/bg_contents_bottom.jpg') }}" class="block" alt="">
        </div>
    </div>
</form>

@endsection
