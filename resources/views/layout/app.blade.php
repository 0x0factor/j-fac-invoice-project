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
    <link rel="icon" href="{{ asset('/favicon.ico') }}" type="image/x-icon" />
    <title>抹茶請求書</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/import.css') }}" />
    @yield('link')
</head>

<body>
    <div id="wrapper">

        <!-- header_Start -->
        @include('layout.header')
        <!-- header_End -->

        <!-- contents_Start -->
        @yield('content')
        <!-- contents_End -->

        <!-- footer_Start -->
        @include('layout.footer')
        <!-- footer_End -->
    </div>
    @yield('script')

</body>

</html>