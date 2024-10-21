@extends('layout.default')

@section('scripts')
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
            $(".chk").prop("checked", $(".chk_all").prop("checked")); // Correct usage
            $('input[name="delete"]').prop('disabled', false); // Correct usage
            $('input[name="reproduce"]').prop('disabled', false); // Correct usage
        }
    </script>

    <script>
        $(function() {
            @if (isset($name) && isset($action))
                setBeforeSubmit('{{ $name . ucfirst($action) . 'Form' }}');
            @else
                console.error("Name or action is not set.");
            @endif
        });
    </script>
@endsection

@section('content')
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
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
                    <table width="600" id="search_box"  cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <th>担当者名</th>
                            <td><input type="text" name="CHARGE_NAME" class="w350" value="{{$searchData['CHARGE_NAME']}}"></td>

                        </tr>
                        <tr>
                            <th>部署名</th>
                            <td><input type="text" name="UNIT" class="w350" value="{{$searchData['UNIT']}}"></td>
                        </tr>
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <select name="STATUS">
                                    <option value="" disabled selected>項目を選んでください</option>
                                    @foreach ($status as $key => $value)
                                        <option value="{{ $key }}" {{ in_array($key, (array)request('STATUS', $searchStatus)) ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach

                                </select>
                            </td>
                        </tr>
                    </table>

                    <div class="search_btn">
                        <table style="margin-left:-80px;">
                            <tr>
                                <td style="border:none;">
                                    <button onclick="document.getElementById('chargeSearchForm').submit();" style="border:none;">
                                        <img src="{{ asset('img/bt_search.jpg') }}" alt="">
                                    </button>
                                </td>
                                <td style="border:none;">
                                    <button type="button" onclick="reset_custom_forms();" style="border:none;">
                                        <img src="{{ asset('img/bt_search_reset.jpg') }}" alt="">
                                    </button>
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
                    {{ $paginator->total() }} 件中 {{ ($paginator->count() * ($paginator->currentPage() - 1) + 1) }} - {{ ($paginator->count() * $paginator->currentPage()) }} 件表示中
                </div>

                <div id='pagination'>
                    <!-- Previous Page Link -->
                    @if ($paginator->onFirstPage())
                        <span class="disabled">
                            << {{ __('前へ') }}</span> |
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}{{ request()->has('sort') ? '&sort='.request('sort').'&direction='.request('direction') : '' }}{{ request()->has('CHARGE_NAME') ? '&CHARGE_NAME='.request('CHARGE_NAME') : '' }}{{ request()->has('UNIT') ? '&UNIT='.request('UNIT') : '' }}{{ request()->has('STATUS') ? '&STATUS='.request('STATUS') : '' }}" rel="prev">
                            << {{ __('前へ') }}</a> |
                    @endif

                    <!-- Pagination Elements -->
                    @foreach ($paginator->links()->elements as $element)
                        <!-- "Three Dots" Separator -->
                        @if (is_string($element))
                            <span class="disabled">{{ $element }}</span> |
                        @endif

                        <!-- Array Of Links -->
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="active">{{ $page }}</span> |
                                @else
                                    <a href="{{ $url }}{{ request()->has('sort') ? '&sort='.request('sort').'&direction='.request('direction') : '' }}{{ request()->has('CHARGE_NAME') ? '&CHARGE_NAME='.request('CHARGE_NAME') : '' }}{{ request()->has('UNIT') ? '&UNIT='.request('UNIT') : '' }}{{ request()->has('STATUS') ? '&STATUS='.request('STATUS') : '' }}">{{ $page }}</a> |
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    <!-- Next Page Link -->
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}{{ request()->has('sort') ? '&sort='.request('sort').'&direction='.request('direction') : '' }}{{ request()->has('CHARGE_NAME') ? '&CHARGE_NAME='.request('CHARGE_NAME') : '' }}{{ request()->has('UNIT') ? '&UNIT='.request('UNIT') : '' }}{{ request()->has('STATUS') ? '&STATUS='.request('STATUS') : '' }}" rel="next">{{ __('次へ') }} >></a>
                    @else
                        <span class="disabled">{{ __('次へ') }} >></span>
                    @endif
                </div>

                <img src="{{ asset('img/bg_contents_top.jpg') }}" alt="">
                <div class="list_area">
                    @if (is_array($list))
                        <form method="POST" action="{{ route('charge.index') }}">
                            @csrf
                            <table width="900" cellpadding="0" cellspacing="0" border="0" id="index_table">
                                <thead>
                                    <tr>
                                        <th class="w50">
                                            <input type="checkbox" name="action.select_all" class="chk_all"
                                                onclick="select_all();">
                                        </th>
                                        @php
                                            $columns = [
                                                'CHR_ID' => 'No.',
                                                'CHARGE_NAME' => '担当者名',
                                                'UNIT' => '部署名',
                                                'PHONE_NO' => '電話番号',
                                                'SEAL' => '印鑑',
                                                'STATUS' => 'ステータス',
                                                'USR_ID' => '作成者'
                                            ];
                                        @endphp

                                        @foreach($columns as $field => $label)
                                            @if($field !== 'USR_ID' || $user['AUTHORITY'] != 1)
                                                <th class="{{ in_array($field, ['CHR_ID', 'SEAL', 'STATUS']) ? 'w100' : (in_array($field, ['USR_ID']) ? 'w100' : 'w200') }}">
                                                    <a href="{{ route('charge.index', ['sort' => $field, 'direction' => request('sort') === $field && request('direction') === 'asc' ? 'desc' : 'asc']) }}">
                                                        {{ $label }}
                                                        @if (request('sort') === $field)
                                                            {{ request('direction') === 'asc' ? '↓' : '↑' }}
                                                        @endif
                                                    </a>
                                                </th>
                                            @endif
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($list as $key => $val)
                                        <?php $val['SEAL'] = !empty($val['SEAL']) ? 0 : 1; ?>
                                        <tr>
                                            <td>
                                                @if (empty($delcheck[$val['CHR_ID']]))
                                                    <input type="checkbox" name="{{ $val['CHR_ID'] }}"
                                                        class="chk">
                                                @else
                                                    &nbsp;
                                                @endif
                                            </td>
                                            <td>{{ $val['CHR_ID'] }}</td>
                                            <td>
                                                <a href="{{ route('charge.check', $val['CHR_ID']) }}">{{ $val['CHARGE_NAME'] }}</a>
                                        </td>
                                            <td>{{ nl2br($val['UNIT']) ?: '&nbsp;' }}</td>
                                            <td>
                                                @if (!empty($val['PHONE_NO1']) || !empty($val['PHONE_NO2']) || !empty($val['PHONE_NO3']))
                                                    {{ $val['PHONE_NO1'] . '-' . $val['PHONE_NO2'] . '-' . $val['PHONE_NO3'] }}
                                                @endif
                                            </td>
                                            <td>{{ $seal[$val['SEAL']] }}</td>
                                            <td>{{ $val['STATUS'] }}</td>
                                            @if ($user['AUTHORITY'] != 1)
                                                <td>{{ $val['NAME'] }}</td>
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
