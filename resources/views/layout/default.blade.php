<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="imagetoolbar" content="no" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="copyright" content="" />
    <meta name="robots" content="index,follow" />
    <meta http-equiv="imagetoolbar" content="no" />
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-store">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
    @php
        $controller = Request::segment(1);
        $action = Request::segment(2);
    @endphp
    @php
        $user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
    @endphp
    @if (preg_match('/^home/', $controller))
        <title>{{ $main_title }}｜{{ $title }}</title>
    @else
        <title>
            {{ $main_title }}｜
            @if (isset($title_text))
                {{ $title_text }}｜
            @endif
            {!! nl2br(e($title)) !!}
        </title>
    @endif
    <script>
        var controller_name =
            @isset($controller_name)
                '{{ $controller_name }}'
            @endisset ;
    </script>
    <script src="{{ asset('js/jquery-1.4.4.min.js') }}"></script>
    @if (isset($rb_flag) && $rb_flag)
        <script src="{{ asset('regularbill/js/jkl-calendar.js') }}"></script>
    @else
        <script src="{{ asset('js/jkl-calendar.js') }}"></script>
    @endif
    <script src="{{ asset('js/mathcontext.js') }}"></script>
    <script src="{{ asset('js/bigdecimal.js') }}"></script>
    <script src="{{ asset('js/forms.js') }}"></script>
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script src="{{ asset('js/alphafilter.js') }}" defer></script>

    <script src="{{ asset('js/ready.js') }}"></script>
    <script src="{{ asset('js/rollover.js') }}"></script>
    <script src="{{ asset('js/rollover-table.js') }}"></script>
    <link href="{{ asset('css/import.css') }}" rel="stylesheet">
    @yield('link')
</head>

<body>

    <!-- wrapper_Start -->
    <div id="wrapper">

        <!-- header_Start -->
        <div id="header" class="clearfix">
            <h1><a href="/"><img src="{{ asset('/img/cms/i_logo.jpg') }}" height="40" alt="抹茶請求書"></a></h1>



            <div id="logout">
                <form action="{{ url('logout') }}" method="POST" id="logging-out">
                    @csrf
                    <button type="submit" style="border: none;">
                        <img src="{{ asset('img/bt_logout.jpg') }}" alt="ログアウト" class="imgover">
                    </button>
                </form>
            </div>


            <br class="clear" />
            <div>
                <ul id="menu" class="menu">
                    @if (preg_match('/^home/', $controller))
                        <li><img src="{{ asset('img/bt_menu01_on.jpg') }}" alt="HOME"></li>
                    @else
                        <li><a href="{{ route('home') }}"><img src="{{ asset('img/bt_menu01.jpg') }}" alt="HOME" class="imgover"></a></li>
                    @endif
                    <li>
                        @if (preg_match(
                                '/^quotes|^bills|^totalbills|^regularbill|^deliveries|^mails|^customers\/select/',
                                $controller . '/' . $action))
                            <img src="{{ asset('img/bt_menu02_on.jpg') }}" alt="帳票管理">
                        @elseif(isset($plugin) && $plugin == 'regularbill')
                            <img src="{{ asset('img/bt_menu02_on.jpg') }}" alt="帳票管理">
                        @else
                            <img src="{{ asset('img/bt_menu02.jpg') }}" alt="帳票管理" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('customer.select') }}">顧客から絞り込み</a></span></li>
                            <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>
                            <li><span><a href="{{ route('quote.index') }}">見積書一覧</a></span></li>
                            <li><span><a href="{{ route('quote.add') }}">見積書を作成する</a></span></li>
                            <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('bill.index') }}">請求書一覧</a></span></li>
                            <li><span><a href="{{ route('bill.add') }}">請求書を作成する</a></span></li>
                            <li><span><a href="{{ route('totalbill.index') }}">合計請求書一覧</a></span></li>
                            <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('delivery.index') }}">納品書一覧</a></span></li>
                            <li><span><a href="{{ route('delivery.add') }}">納品書を作成する</a></span></li>
                            <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>

                            @if (isset($rb_flag) && $rb_flag)
                                <li><span><a
                                            href="{{ route('regularbill.index', ['plugin' => 'regularbill']) }}">定期請求書雛形一覧</a></span>
                                </li>
                                <li><span><a
                                            href="{{ route('regularbill.add', ['plugin' => 'regularbill']) }}">定期請求書雛形を作成する</a></span>
                                </li>
                                <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>
                            @endif

                            <li><span><a href="{{ route('mail.index') }}">確認メール一覧</a></span></li>
                            <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter"></li>
                        </ul>
                    </li>
                    <li>
                        @if (preg_match('/^customers|^customer_charges|^coverpages/', $controller) &&
                                !preg_match('/^customers\/select/', $controller . '/' . $action))
                            <img src="{{ asset('img/bt_menu03_on.jpg') }}" alt="顧客管理">
                        @else
                            <img src="{{ asset('img/bt_menu03.jpg') }}" alt="顧客管理" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('customer.index') }}">取引先を見る</a></span></li>
                            <li><span><a href="{{ route('customer_charge.index') }}">取引先担当者を見る</a></span></li>
                            <li><span><a href="{{ route('coverpage.index') }}">送付状を作成する</a></span></li>
                            <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter">
                            </li>
                        </ul>
                    </li>
                    <li>
                        @if (preg_match('/^companies|^charges|^items/', $controller))
                            <img src="{{ asset('img/bt_menu04_on.jpg') }}" alt="自社設定">
                        @else
                            <img src="{{ asset('img/bt_menu04.jpg') }}" alt="自社設定" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('company.index') }}">基本情報を見る</a></span></li>
                            <li><span><a href="{{ route('charge.index') }}">自社担当者を見る</a></span></li>
                            <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>

                            <li><span><a href="{{ route('item.index') }}">商品を見る</a></span></li>
                            <li><span><a href="{{ route('item.add') }}">商品を登録する</a></span></li>
                            <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter">
                            </li>
                        </ul>
                    </li>
                    <li>
                        @if (isset($user) && $user['AUTHORITY'] == 0)
                            @if (preg_match('/^administers|^histories|^configurations|^postcode|^view_options|^personals/', $controller))
                                <img src="{{ asset('img/bt_menu05_on.jpg') }}" alt="管理者メニュー">
                            @else
                                <img src="{{ asset('img/bt_menu05.jpg') }}" alt="管理者メニュー" class="imgover">
                            @endif
                            <ul class="dmenu">
                                <li><span><a href="{{ route('administer.index') }}">ユーザを管理する</a></span></li>
                                <li><span><a href="{{ route('history.index') }}">操作履歴を見る</a></span></li>
                                <li><span><a href="{{ route('configuration.index') }}">環境設定をする</a></span></li>
                                <li><span><a href="{{ route('postcode.index') }}">郵便番号を管理する</a></span></li>
                                <li><span><a href="{{ route('view_option.index') }}">デザイン設定をする</a></span></li>
                                <li class="line"><img src="{{ asset('img/i_line_dmenu.gif') }}"></li>
                                <li><span><a href="{{ route('personal.passEdit') }}">パスワードを変更する</a></span></li>
                                <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}"
                                        class="alphafilter"></li>
                            </ul>
                        @endif
                    </li>
                    <li>
                        @if (preg_match('/^personals/', $controller))
                            <img src="{{ asset('img/bt_menu06_on.jpg') }}" alt="ユーザメニュー">
                        @else
                            <img src="{{ asset('img/bt_menu06.jpg') }}" alt="ユーザメニュー" class="imgover">
                        @endif
                        <ul class="dmenu">
                            <li><span><a href="{{ route('personal.passEdit') }}">パスワードを変更する</a></span></li>
                            <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter">
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div id="submenu">
            <div class="login_user">
                @if (isset($user))
                    ようこそ: {{ $user['NAME'] }} 様
                @endif
            </div>
        </div>
        <div id="pagetitle">
            <h2><img src="{{ asset('img/i_arrow_pagetitle.jpg') }}">{{ $main_title }}</h2>
        </div>
        <!-- header_End -->

        <div id="main">
            @yield('content')
            <div id="popup-bg"></div>
            <div id="popup"></div>
        </div>

        <!-- footer_Start -->
        <div id="footer">
            オープンソースの業務ソフト～バックオフィスに安らぎを～
            <address>
                抹茶請求書ver.{{ config('app.version') }} <br />
                Copyright &copy; 2024 ICZ corporation. All rights reserved.
            </address>
        </div>

        <!-- footer_End -->
    </div>
    @yield('script')
</body>
<!-- wrapper_End -->

</html>
