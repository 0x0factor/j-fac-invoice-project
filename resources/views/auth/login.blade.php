@extends('layout.app')
@section('content')

<div id="login">
    <form name="UserLoginForm" id="UserLoginForm" novalidate method="POST" action="/login" accept-charset="utf-8">
        @method('POST')
        @csrf
        @if ($errors->has('LOGIN_ID'))
        <span class="invalid-feedback">
            <strong>123123123</strong>
        </span>
        @endif
        <div class="login"><img src="{{ asset('/img/login/tl_login.jpg') }}" alt="ログイン画面" /></div>
        <div id="login_box">
            <div id="login_logo"><img src="{{ asset('/img/login/i_logo_login.jpg') }}" alt="" /></div>
            <div id="login_id"><img src="{{ asset('/img/login/tm_id.gif') }}" alt="ID" class="mr10" />
                <input type="text" name="LOGIN_ID" class="w320" value="" id="UserLOGINID" required />
            </div>

            <div id="login_pw"><img src="{{ asset('/img/login/tm_pw.gif') }}" alt="パスワード" class="mr10" />
                <input type="password" name="PASSWORD" class="w320" id="UserPASSWORD" required />
            </div>
            <div id="login_btn">
                <input type="image" src="{{ asset('/img/login/bt_login.jpg') }}" name="submit" alt="ログイン"
                    class="imgover" /> <a href="/users/reset">パスワードお忘れの方</a>
                <!-- <a href="/register" class="text-center">Register a new membership</a> -->
            </div>
        </div>
    </form>
</div>
<!-- contents_End -->

@endsection

@section('script')
<script src="/assets/plugins/jquery/jquery.min.js"></script>

<script src="/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<script src="/assets/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
<!-- contents_End -->

@endsection

<!-- wrapper_End -->
