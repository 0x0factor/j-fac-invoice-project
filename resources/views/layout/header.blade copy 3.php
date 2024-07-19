
    @php
    $controller = Request::segment(1);
    $action = Request::segment(2);
    @endphp

    @if(preg_match('/^home/', $controller))
    <title>{{ $main_title }}｜{{ $title }}</title>
    @else
    <title>{{ $main_title }}｜{{ $title_text }}｜{!! nl2br(e($title)) !!}</title>
    @endif
    <script>
    var controller_name = @isset($controller_name)
    '{{ $controller_name }}'
    @endisset;
    </script>
    <script src="{{ asset('js/jquery-1.4.4.min.js') }}"></script>
    @if(isset($rb_flag) && $rb_flag)
    <script src="{{ asset('regularbill/js/jkl-calendar.js') }}"></script>
    @else
    <script src="{{ asset('js/jkl-calendar.js') }}"></script>
    @endif
    <script src="{{ asset('js/mathcontext.js') }}"></script>
    <script src="{{ asset('js/bigdecimal.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script src="{{ asset('js/alphafilter.js') }}" defer></script>
    {!! $customAjax->postcode() !!}
    {!! $customAjax->usercode() !!}
    {!! $customAjax->popup() !!}
    <script src="{{ asset('js/ready.js') }}"></script>
    <script src="{{ asset('js/rollover.js') }}"></script>
    <script src="{{ asset('js/rollover-table.js') }}"></script>



        <!-- header_Start -->
        <div id="header" class="clearfix">
            <h1><a href="/"><img src="{{ asset($logo) }}" height="40" alt="抹茶請求書"></a></h1>
            <div id="logout">
                <a href="{{ route('users.logout') }}"><img src="{{ asset('bt_logout.jpg') }}" alt="ログアウト"
                        class="imgover"></a>
            </div>
            <br class="clear" />
            <div>
                <ul id="menu" class="menu">
                    @if(preg_match('/^home/', $controller))
                    <li><img src="{{ asset('bt_menu01_on.jpg') }}" alt="HOME"></li>
                    @else
                    <li><a href="{{ route('homes.index') }}"><img src="{{ asset('bt_menu01.jpg') }}" alt="HOME"
                                class="imgover"></a></li>
                    @endif
                    <li>
                        @if(preg_match('/^quotes|^bills|^totalbills|^regularbill|^deliveries|^mails|^customers\/select/',
                        $controller."/".$action))
                        <img src="{{ asset('bt_menu02_on.jpg') }}" alt="帳票管理">
                        @elseif(isset($plugin) && $plugin == 'regularbill')
                        <img src="{{ asset('bt_menu02_on.jpg') }}" alt="帳票管理">
                        @else
                        <img src="{{ asset('bt_menu02.jpg') }}" alt="帳票管理" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('customers.select') }}">顧客から絞り込み</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>
                            <li><span><a href="{{ route('quotes.movetoindex') }}">見積書一覧</a></span></li>
                            <li><span><a href="{{ route('quote.add') }}">見積書を作成する</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('bills.movetoindex') }}">請求書一覧</a></span></li>
                            <li><span><a href="{{ route('bills.add') }}">請求書を作成する</a></span></li>
                            <li><span><a href="{{ route('totalbills.movetoindex') }}">合計請求書一覧</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('deliveries.movetoindex') }}">納品書一覧</a></span></li>
                            <li><span><a href="{{ route('deliveries.add') }}">納品書を作成する</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>

                            @if(isset($rb_flag) && $rb_flag)
                            <li><span><a
                                        href="{{ route('regularbill.index', ['plugin' => 'regularbill']) }}">定期請求書雛形一覧</a></span>
                            </li>
                            <li><span><a
                                        href="{{ route('regularbill.add', ['plugin' => 'regularbill']) }}">定期請求書雛形を作成する</a></span>
                            </li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>
                            @endif

                            <li><span><a href="{{ route('mails.index') }}">確認メール一覧</a></span></li>
                            <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                    </li>
                    <li>
                        @if(preg_match('/^customers|^customer_charges|^coverpages/', $controller) &&
                        !preg_match('/^customers\/select/', $controller."/".$action))
                        <img src="{{ asset('bt_menu03_on.jpg') }}" alt="顧客管理">
                        @else
                        <img src="{{ asset('bt_menu03.jpg') }}" alt="顧客管理" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('customers.movetoindex') }}">取引先を見る</a></span></li>
                            <li><span><a href="{{ route('customer_charges.index') }}">取引先担当者を見る</a></span></li>
                            <li><span><a href="{{ route('coverpages.index') }}">送付状を作成する</a></span></li>
                            <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                    </li>
                    <li>
                        @if(preg_match('/^companies|^charges|^items/', $controller))
                        <img src="{{ asset('bt_menu04_on.jpg') }}" alt="自社設定">
                        @else
                        <img src="{{ asset('bt_menu04.jpg') }}" alt="自社設定" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('companies.index') }}">基本情報を見る</a></span></li>
                            <li><span><a href="{{ route('charges.movetoindex') }}">自社担当者を見る</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('items.movetoindex') }}">商品を見る</a></span></li>
                            <li><span><a href="{{ route('items.add') }}">商品を登録する</a></span></li>
                            <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                    </li>
                    <li>
                        @if($user['AUTHORITY'] == 0)
                        @if(preg_match('/^administers|^histories|^configurations|^postcode|^view_options|^personals/',
                        $controller))
                        <img src="{{ asset('bt_menu05_on.jpg') }}" alt="管理者メニュー">
                        @else
                        <img src="{{ asset('bt_menu05.jpg') }}" alt="管理者メニュー" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('administers.movetoindex') }}">ユーザを管理する</a></span></li>
                            <li><span><a href="{{ route('histories.movetoindex') }}">操作履歴を見る</a></span></li>
                            <li><span><a href="{{ route('configurations.index') }}">環境設定をする</a></span></li>
                            <li><span><a href="{{ route('postcode.index') }}">郵便番号を管理する</a></span></li>
                            <li><span><a href="{{ route('view_options.index') }}">デザイン設定をする</a></span></li>
                            <li class="line"><img src="{{ asset('i_line_dmenu.gif') }}"></li>
                            <li><span><a href="{{ route('personals.pass_edit') }}">パスワードを変更する</a></span></li>
                            <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                        @endif
                    </li>
                    <li>
                        @if(preg_match('/^personals/', $controller))
                        <img src="{{ asset('bt_menu06_on.jpg') }}" alt="ユーザメニュー">
                        @else
                        <img src="{{ asset('bt_menu06.jpg') }}" alt="ユーザメニュー" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('personals.pass_edit') }}">パスワードを変更する</a></span></li>
                            <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div id="submenu">
            <div class="login_user">
                ようこそ: {{ Auth::user()->name }} 様
            </div>
        </div>
        <div id="pagetitle">
            <h2><img src="{{ asset('i_arrow_pagetitle.jpg') }}">{{ $main_title }}</h2>
        </div>
        <!-- header_End -->


    </div>