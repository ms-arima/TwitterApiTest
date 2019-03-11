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

    @isset($timelines)
        <div class="content">
            <h1>フォロワー</h1>
            @if(filled($timelines))
                <div class="btn-group mb-2" role="group" aria-label="基本のボタングループ">
                    @if($timelines['prev_cursor'] !== '0')
                        <a href="{{url()->current()}}?cursor={{$timelines['prev_cursor']}}" class="btn btn-info">前へ</a>
                    @endif
                    @if($timelines['next_cursor'] !== '0')
                        <a href="{{url()->current()}}?cursor={{$timelines['next_cursor']}}" class="btn btn-warning">次へ</a>
                    @endif
                </div>
            @endif
            <ul>
                @forelse($timelines['users'] as $user)
                    <li><strong>{{$user['tweeter_name']}}</strong>：{{'@'.$user['tweeter_screen_name']}}</li>
                @empty
                    データがありません。
                @endforelse
            </ul>
        </div>
    @endisset
</div>
</body>
</html>
