<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
    <script src="{{asset('js/app.js')}}"></script>

</head>
<body>
<div class="container">

    <p><a href="/">Top</a></p>
    <form method="POST" action="{{url()->current()}}" accept-charset="UTF-8" class="mb-5 form-inline">
        {{ csrf_field() }}
        <input type="text" class="form-control" name="tweet" placeholder="ツイート内容" required>
        <button type="submit" class="btn btn-primary ml-1">Tweet</button>
    </form>

    @isset($timelines)
        <div class="content">
            <h1>ユーザータイムライン</h1>
            <div class="card-columns">
                @forelse($timelines as $timeline)
                    <div class="card p-2">
                        <img class="card-img-top" src="{{$timeline["tweeter_profile_image_url"]}}">
                        <div class="card-body">
                            <h5 class="card-title">{{$timeline["tweeter_name"]}} {{'@'.$timeline["tweeter_screen_name"]}}</h5>
                            <p class="card-text">{{$timeline["text"]}}</p>
                            <a href="{{route('userTimeLine.destroy', $timeline["id"])}}" class="btn btn-primary">削除</a>
                        </div>
                    </div>
                @empty
                    <div>
                        データがありません。
                    </div>
                @endforelse
            </div>
        </div>
    @endisset
</div>
</body>
</html>
