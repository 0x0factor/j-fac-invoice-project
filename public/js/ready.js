$(document).ready(function ($) {

    //インスタンス化
    window.form = new FormClass();
    var windowclass = new WindowClass();
    var popupclass = new PopupClass(windowclass);

    //初期化
    form.f_init();
    popupclass.init(form);
    check_if_searched();
});
