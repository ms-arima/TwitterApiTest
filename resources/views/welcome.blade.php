<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/app.js')}}"></script>

</head>
<body>
<div class="container">

    <div>
        <h4>動作確認機能</h4>
        <ul>
            <li><a href="/threeLeggedOauth">3者間認証(3-legged-oauth)</a></li>
            <li><a href="/userTimeLine">ツイートCRD</a></li>
            <li><a href="/followers">ページネーション</a></li>
        </ul>
    </div>
    <div>
        <h4>Twitter Token</h4>
        <ul>
            <li><b>API_KEY</b>：{{\App\Libs\TwitterConfig::API_KEY}}</li>
            <li><b>API_SECRET</b>：{{\App\Libs\TwitterConfig::API_SECRET}}</li>
            <li><b>ACCESS_TOKEN</b>：{{\App\Libs\TwitterConfig::ACCESS_TOKEN}}</li>
            <li><b>ACCESS_TOKEN_SECRET</b>：{{\App\Libs\TwitterConfig::ACCESS_TOKEN_SECRET}}</li>
        </ul>
    </div>
</div>
</body>
</html>
