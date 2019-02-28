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
        <a href="/">Top</a>
    </div>
    @isset($token)
        <div class="content">
            <h1>Token</h1>
            <ul>
                @foreach($token as $key => $value)
                    <li><b>{{$key}}</b>： {{$value}}</li>
                @endforeach
            </ul>
        </div>
        <div>
            <a href="https://api.twitter.com/oauth/authorize?oauth_token={{$token["oauth_token"]}}&lang=ja">login</a>
        </div>
    @endisset
    @isset($message)
        <div>{{$message}}</div>
    @endisset
    @isset($timelines)
        <div class="content">
            <h1>タイムライン</h1>
            <ul>
                @foreach($timelines as $timeline)
                    <li> {{$timeline->text}}</li>
                @endforeach
            </ul>
        </div>

    @endisset
</div>
</body>
</html>
