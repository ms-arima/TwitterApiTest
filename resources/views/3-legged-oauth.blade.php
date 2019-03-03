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

    <p><a href="/">Top</a></p>
    <p><a href="/threeLeggedOauth">3者間認証(3-legged-oauth)</a></p>
    @isset($message)
        <div>{{$message}}</div>
    @endisset
    @isset($token)
        <div class="content">
            <h1>Token</h1>
            <ul>
                @foreach($token as $key => $value)
                    <li><b>{{json_encode($key)}}</b>： {{json_encode($value)}}</li>
                @endforeach
            </ul>
        </div>
        <div>
            @isset($authorizeUrl)
                <a href="{{$authorizeUrl}}">login</a>
            @endisset
        </div>
    @endisset
    @isset($timelines)
        <div class="content">
            <h1>タイムライン</h1>
            <div class="card-columns">
                @foreach($timelines as $timeline)
                    <div class="card p-2">
                        <img class="card-img-top" src="{{$timeline["tweeter_profile_image_url"]}}">
                        <div class="card-body">
                            <h5 class="card-title">{{$timeline["tweeter_name"]}} {{'@'.$timeline["tweeter_screen_name"]}}</h5>
                            <p class="card-text">{{$timeline["text"]}}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endisset
</div>
</body>
</html>
