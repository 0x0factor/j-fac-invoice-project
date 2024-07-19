@extends('layout.app')

@section('content')
<!-- contents_Start -->
<div id="contents" class="clearfix">

    <div id="contents_l">
        <h3>
            <div class="function_01"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="function_box mb20">
            <img src="{{ asset('/img/index/bg_function_top.jpg') }}" alt="" />
            <div class="function_area">
                <p> <img src="{{ asset('/img/index/tm_01_1.jpg') }}" alt="" /></p>
                <p> <img src="{{ asset('/img/index/bt_admin.jpg') }}" alt="" />
                    <br />
                    管理者の方はまず「自社設定」で自社情報を登録します。 自社名、住所、連絡先などの基本情報や、帳票で押印する自社判の登録、支払い情報などを設定します。
                </p>

                <p> <img src="{{ asset('/img/index/bt_user.jpg') }}" alt="" />
                    <br />管理者以外の方は「自社情報設定」を編集・変更することはできません。変更を加える場合は、管理者様より行ってください。
                </p>
                <br />

                <p> <img src="{{ asset('/img/index/tm_01_2.jpg') }}" alt="" /></p>
                <p>「自社設定」の「自社担当者を見る」では、自社担当者の設定を行います。自社担当者を設定することで各取引先に対する自社担当者を設定することができます。</p>
                <br />

                <p> <img src="{{ asset('/img/index/tm_01_3.jpg') }}" alt="" /></p>
                <p>「自社設定」の「商品を登録する」では、商品を登録することができます。「商品管理」は簡易的な商品マスターで、よく帳票で利用する商品を登録することで、帳票作成の際、プルダウンで選択するだけで「商品名」「単位」「価格」の項目が自動入力されます。
                </p>

            </div>
            <img src="{{ asset('/img/index/bg_function_bottom.jpg') }}" alt="" class='block' />
        </div>
        <h3>
            <div class="function_02"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="function_box mb20">
            <img src="{{ asset('/img/index/bg_function_top.jpg') }}" alt="" />
            <div class="function_area">
                <p><img src="{{ asset('/img/index/tm_02_1.jpg') }}" alt="" /></p>
                <p>「顧客管理」の「取引先を登録する」では、取引先の住所、連絡先などの基本情報を登録することができます。取引先情報を登録することで、帳票作成の際に情報を呼び出すことができます。</p>
                <br />

                <p><img src="{{ asset('/img/index/tm_02_2.jpg') }}" alt="" /></p>
                <p>「顧客管理」の「取引先担当者を見る」では、取引先の担当者の氏名、所属会社、連絡先などの情報を登録します。取引先担当者を登録しておくことで、帳票のメール送付機能で送付先を指定する際に、簡単に氏名、メールアドレスを呼び出すことができます。
                </p>
            </div>
            <img src="{{ asset('/img/index/bg_function_bottom.jpg') }}" alt="" class='block' />
        </div>
        <h3>
            <div class="function_03"><span class="edit_txt">&nbsp;</span></div>
        </h3>
        <div class="function_box mb20">
            <img src="{{ asset('/img/index/bg_function_top.jpg') }}" alt="" />
            <div class="function_area">

                <p><img src="{{asset('/img/index/tm_03_1.jpg')}}" /></p>
                <p>帳票を作成する場合は、「帳票管理」の「見積書を作成する」「請求書を作成する」「納品書を作成する」の各サブメニューをクリックすることで、各帳票の新規作成ページへ遷移し、新たに帳票を作成することができます。
                </p>

                <br />
                <p><img src="{{asset('/img/index/tm_03_2.jpg')}}" /></p>
                <p>一度作成した帳票の編集を行う場合は、各帳票の一覧画面より、該当する「件名」をクリックすると各帳票の確認画面へ遷移しますが、「編集する」を押下すると、その帳票を編集することができます。
                </p>
                <br />

                <p><img src="{{asset('/img/index/tm_03_3.jpg')}}" /></p>
                <p>作成した帳票データをPDFで出力する場合は、各帳票の一覧画面より、該当する「件名」をクリックし、確認画面へ遷移します。「ダウンロード」ボタンを押下することで、PDFデータをダウンロード、保存することができます。
                </p>

                <br />

                <p><img src="{{asset('/img/index/tm_03_4.jpg')}}" /></p>
                <p>見積書、請求書、納品書の各帳票を顧客別に確認したい場合は、「帳票管理」メニューの「顧客から絞込み」を押します。そうすると「取引先一覧」ページに遷移し、「顧客管理」で登録されている全ての顧客ごとに「見積書
                    / 請求書 / 納品書 」のリンクが表示されます。その各リンクをクリックすることで、顧客別に帳票を絞り込んで表示することができます。
                </p>

            </div>
            <img src="{{asset('/img/index/bg_function_bottom.jpg')}}" alt="" class='block' />
        </div>
    </div>

    <div id="contents_r">
        <h3>
            <div class="news"><span class="news_txt">最新の情報</span></div>
        </h3>
        <div class="news_box mb20">
            <h4>
                <div class="news_sub"><span class="pl20">見積書</span></div>
            </h4>
            <div class="news_area">
                <dl>
                    @if($quote)
                    @foreach($quote as $value)
                    <dt>{{ $customHtml->dtf($value['Quote']['LAST_UPDATE']) }}</dt>
                    @if($user['AUTHORITY'] != 1)
                    <dd>作成者 ： {{ $value['Quote']['USR_NAME'] }}</dd>
                    @endif
                    <dd class="sub">件名 ：
                        {{ $html->link($value['Quote']['SUBJECT'], "/quotes/check/" . $value['Quote']['MQT_ID']) }}</dd>
                    @endforeach
                    @else
                    <dt></dt>
                    <dd class="sub">最新の見積書はありません</dd>
                    @endif
                </dl>
            </div>
            <h4>
                <div class="news_sub"><span class="pl20">請求書</span></div>
            </h4>
            <div class="news_area">
                <dl>
                    @if ($bill)
                    @foreach ($bill as $value)
                    <dt>{{ $customHtml->dtf($value['Bill']['LAST_UPDATE']) }}</dt>
                    @if ($user['AUTHORITY'] != 1)
                    <dd>作成者 ： {{ $value['Bill']['USR_NAME'] }}</dd>
                    @endif
                    <dd class="sub">件名 ：
                        {{ $html->link($value['Bill']['SUBJECT'], "/bills/check/".$value['Bill']['MBL_ID']) }}</dd>
                    @endforeach
                    @else
                    <dt></dt>
                    <dd class="sub">最新の請求書はありません</dd>
                    @endif
                </dl>
            </div>
            <h4>
                <div class="news_sub"><span class="pl20">納品書</span></div>
            </h4>
            <div class="news_area">
                <dl>
                    @if ($delivery)
                    @foreach ($delivery as $value)
                    <dt>{{ $customHtml->dtf($value['Delivery']['LAST_UPDATE']) }}</dt>
                    @if ($user['AUTHORITY'] != 1)
                    <dd>作成者 ： {{ $value['Delivery']['USR_NAME'] }}</dd>
                    @endif
                    <dd class="sub">件名 ：
                        {{ $html->link($value['Delivery']['SUBJECT'], "/deliveries/check/".$value['Delivery']['MDV_ID']) }}
                    </dd>
                    @endforeach
                    @else
                    <dt></dt>
                    <dd class="sub">最新の納品書はありません</dd>
                    @endif
                </dl>
            </div>
            <img src="{{asset('/img/index/bg_news_bottom.jpg')}}" class='block' />
        </div>
    </div>


</div>
<!-- contents_End -->
@endsection