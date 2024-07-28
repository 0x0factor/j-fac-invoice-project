<div>

    <style type="text/css">
        <!--
        dt,
        dd {
            margin: 0;
            padding: 0;
        }

        .float {
            float: left;
        }

        .clear {
            clear: both;
        }

        .w300 {
            width: 100px;
        }
        -->
    </style>

    <div class="clear">

        <h2>見積額</h2>
        <div class="float">
            <dl class="w300">
                <dt>先々月</dt>
                <dd>{{ $quote['month_before_last']['count'] }}件</dd>
                <dd>{{ $quote['month_before_last']['money'] }}円</dd>
            </dl>
        </div>

        <div class="float">
            <dl class="w300">
                <dt>先月</dt>
                <dd>{{ $quote['last_month']['count'] }}件</dd>
                <dd>{{ $quote['last_month']['money'] }}円</dd>
            </dl>
        </div>

        <div class="float">
            <dl class="w300">
                <dt>今月</dt>
                <dd>{{ $quote['this_month']['count'] }}件</dd>
                <dd>{{ $quote['this_month']['money'] }}円</dd>
            </dl>
        </div>

    </div>

    <div class="clear">

        <h2>請求額</h2>
        <div class="float">
            <dl class="w300">
                <dt>先々月</dt>
                <dd>{{ $bill['month_before_last']['count'] }}件</dd>
                <dd>{{ $bill['month_before_last']['money'] }}円</dd>
            </dl>
        </div>

        <div class="float">
            <dl class="w300">
                <dt>先月</dt>
                <dd>{{ $bill['last_month']['count'] }}件</dd>
                <dd>{{ $bill['last_month']['money'] }}円</dd>
            </dl>
        </div>

        <div class="float">
            <dl class="w300">
                <dt>今月</dt>
                <dd>{{ $bill['this_month']['count'] }}件</dd>
                <dd>{{ $bill['this_month']['money'] }}円</dd>
            </dl>
        </div>

    </div>

    <div class="clear"></div>

</div>
