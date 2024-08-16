
    <div>
        <!-- 住所検索の結果画面 -->
        @if (is_array($data))
            @foreach ($data as $value)
                <a href="/" onclick="return setaddress('{{ $value['Post']['POSTCODE'] }}');">
                    〒{{ substr($value['Post']['POSTCODE'], 0, 3) }}-{{ substr($value['Post']['POSTCODE'], 3, 4) }}
                    {{ $value['Post']['COUNTY'] }}{{ $value['Post']['CITY'] }}{{ $value['Post']['AREA'] }}
                </a><br>
            @endforeach
        @else
            <p>No address data found.</p>
        @endif
    </div>
