@php
$user = Auth::user(); // Assuming you are using Laravel's built-in authentication system
@endphp
@if(isset($user))
<div id="header" class="clearfix">
    <h1>
        <a href="{{ url('/') }}">
            <img src="{{ asset('/img/cms/i_logo.jpg') }}" height="40" alt="抹茶請求書">
        </a>
    </h1>
    <div id="logout">
        <a href="#">
            <img src=" {{ asset('img/bt_logout.jpg') }}" alt="ログアウト" class="imgover">
            <form action="/logout" method="POST" id="logging-out">
                @csrf
            </form>
        </a>
    </div>
    <br class="clear" />
    @php
    $routeName = app('request')->route()->getName();
    $plugin = isset($request->plugin) ? $request->plugin : '';
    @endphp
    <div>
        <ul id="menu" class="menu">
            @if (request()->routeIs('home.*'))
            <li><img src="{{ asset('img/bt_menu01_on.jpg') }}" alt="HOME"></li>
            @else
            <li><a href="{{ route('home') }}" class="imgover">
                    <img src="{{ asset('img/bt_menu01.jpg') }}" alt="HOME">
                </a>

            </li>
            @endif
            <li>
                @if(preg_match('/^quotes|^bills|^totalbills|^regularbill|^deliveries|^mails|^customers\/select/',
                $routeName) || ($plugin == 'regularbill'))
                <img src="{{ asset('img/bt_menu02_on.jpg') }}" alt="帳票管理">
                @else
                <img src="{{ asset('img/bt_menu02.jpg') }}" alt="帳票管理" class="imgover">
                @endif


                <ul class="dmenu">
                    <li><span><a href="{{ route('customers.select') }}">顧客から絞り込み</a></span></li>
                    <li class="line"><img src="{{ asset('/img/i_line_dmenu.gif') }}"></li>
                    <li><span><a href="{{ route('quotes.movetoindex') }}">見積書一覧</a></span></li>
                    <li><span><a href="{{ route('quote.add') }}">見積書を作成する</a></span></li>
                    <li class="line">"><img src="{{ asset('/img/i_line_dmenu.gif') }}"></li>
                    <li><span><a href="{{ route('bills.movetoindex') }}">請求書一覧</a></span></li>
                    <li><span><a href="{{ route('bills.add') }}">請求書を作成する</a></span></li>
                    <li><span><a href="{{ route('totalbills.movetoindex') }}">合計請求書一覧</a></span></li>
                    <li class="line">"><img src="{{ asset('/img/i_line_dmenu.gif') }}"></li>
                    <li><span><a href="{{ route('deliveries.movetoindex') }}">納品書一覧</a></span></li>
                    <li><span><a href="{{ route('deliveries.add') }}">納品書を作成する</a></span></li>
                    <li class="line">"><img src="{{ asset('/img/i_line_dmenu.gif') }}"></li>
                    @if(isset($rb_flag) && $rb_flag)
                    <li><span><a
                                href="{{ route('regularbill.index', ['plugin' => 'regularbill']) }}">定期請求書雛形一覧</a></span>
                    </li>
                    <li><span><a
                                href="{{ route('regularbill.add', ['plugin' => 'regularbill']) }}">定期請求書雛形を作成する</a></span>
                    </li>
                    <li class="line">"><img src="{{ asset('/img/i_line_dmenu.gif') }}"></li>
                    @endif
                    <li><span><a href="{{ route('mails.index') }}">確認メール一覧</a></span></li>
                    <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class='alphafilter'></li>
                </ul>
            </li>



            <li>
                @php
                $controller = 'customers'; // Replace with the variable representing the current controller
                $action = 'index'; // Replace with the current action, if applicable
                @endphp
                @if(preg_match('/^customers|^customer_charges|^coverpages/', $controller) &&
                !preg_match('/^customers\/select/',
                $controller.'/'.$action))
                <img src="{{ asset('img/bt_menu03_on.jpg') }}" alt="顧客管理">
                @else
                <img src="{{ asset('img/bt_menu03.jpg') }}" alt="顧客管理" class="imgover">
                @endif
                <ul class="dmenu">
                    <li><span><a href="{{ route('customers.movetoindex') }}">取引先を見る</a></span></li>
                    <li><span><a href="{{ route('customer_charges.index') }}">取引先担当者を見る</a></span></li>
                    <li><span><a href="{{ route('coverpages.index') }}">送付状を作成する</a></span></li>
                    <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter"></li>
                </ul>
            </li>
            <li>
                @php
                $controller = 'companies'; // assign the current controller value
                @endphp
                @if(preg_match('/^companies|^charges|^items/', $controller))
                <img src="{{ asset('img/bt_menu04_on.jpg') }}" alt="自社設定">
                @else
                <img src="{{ asset('img/bt_menu04.jpg') }}" alt="自社設定" class="imgover">
                @endif
                <ul class="dmenu">
                    <li><span><a href="{{ route('companies.index') }}">基本情報を見る</a></span></li>
                    <li><span><a href="{{ route('charges.movetoindex') }}">自社担当者を見る</a></span></li>
                    <li class="line"><img src="{{ asset('/img/i_line_dmenu.gif') }}" alt=""></li>
                    <li><span><a href="{{ route('items.movetoindex') }}">商品を見る</a></span></li>
                    <li><span><a href="{{ route('items.add') }}">商品を登録する</a></span></li>
                    <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter"></li>
                </ul>
            </li>
            <li>

                @if(isset($user) && $user['AUTHORITY'] == 0)
                @php
                $controllers = ['administers', 'histories', 'configurations', 'postcode', 'view_options', 'personals'];
                $controllerMatch = false;
                foreach($controllers as $c) {
                if (preg_match('/^' . $c . '/', $controller)) {
                $controllerMatch = true;
                break;
                }
                }
                @endphp
                @if($controllerMatch)
                <img src="{{ asset('img/bt_menu05_on.jpg') }}" alt="管理者メニュー">
                @else
                <img src="{{ asset('img/bt_menu05.jpg') }}" alt="管理者メニュー" class="imgover">
                @endif
                <ul class="dmenu">
                    <li><span><a href="{{ route('administers.movetoindex') }}">ユーザを管理する</a></span></li>
                    <li><span><a href="{{ route('histories.movetoindex') }}">操作履歴を見る</a></span></li>
                    <li><span><a href="{{ route('configurations.index') }}">環境設定をする</a></span></li>
                    <li><span><a href="{{ route('postcode.index') }}">郵便番号を管理する</a></span></li>
                    <li><span><a href="{{ route('view_options.index') }}">デザイン設定をする</a></span></li>
                    <li class="line"><img src="{{ asset('/img/i_line_dmenu.gif') }}" alt=""></li>
                    <li><span><a href="{{ route('personals.passEdit') }}">パスワードを変更する</a></span></li>
                    <li class="last"><img src="{{ asset('bg_dmenu_btm.png') }}" class="alphafilter"></li>
                </ul>
                @else
                @if(preg_match('/^personals/', $controller))
                <img src="{{ asset('img/bt_menu06_on.jpg') }}" alt="ユーザメニュー">
                @else
                <img src="{{ asset('img/bt_menu06.jpg') }}" alt="ユーザメニュー" class="imgover">
                @endif
                <ul class="dmenu">
                    <li><span><a href="{{ route('personals.passEdit') }}">パスワードを変更する</a></span></li>
                    <li class="last"><img src="{{ asset('img/bg_dmenu_btm.png') }}" class="alphafilter"></li>
                </ul>
                @endif
            </li>
        </ul>
    </div>
</div>
<div id="submenu">
    <div class="login_user">
        @if(isset($user)) ようこそ: {{ $user['NAME'] }} 様 @endif
    </div>
</div>
<div id="pagetitle">
    <h2><img src="{{ asset('img/i_arrow_pagetitle.jpg') }}">{{ $main_title }}</h2>

</div>

@else
<div id="header" class="clearfix">
    <h1><img src="{{ asset('/img/cms/i_logo.jpg') }}" alt="抹茶請求書" height="40" /></h1>
</div>
<div id="submenu_no" class="mb20">
    <img src="{{ asset('/img/bg_submenu_no.jpg') }}" alt="抹茶請求書" />
</div>
<br class="clear" />
@endif
