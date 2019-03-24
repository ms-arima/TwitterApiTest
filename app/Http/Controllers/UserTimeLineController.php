<?php

namespace App\Http\Controllers;

use App\Http\Model\Libs\TwitterConfig;
use App\Http\Model\TwitterStatus;
use Illuminate\Http\Request;

class UserTimeLineController extends Controller
{

    public function index()
    {
        // タイムライン取得
        $user_id = array(
            'screen_name' => 'MbIPBWPzF9mr1tU' // 自分のツイッターIDを入力
        );
        $timelines = TwitterStatus::getUserTimeLines(TwitterConfig::ACCESS_TOKEN, TwitterConfig::ACCESS_TOKEN_SECRET, $user_id);

        return view('userTimeLine', compact('timelines'));
    }

    public function show($id)
    {
        TwitterStatus::deleteTweet($id);

        return redirect(route('userTimeLine.index'));
    }

    public function store(Request $request)
    {
        TwitterStatus::addTweet($request->tweet);

        return redirect(route('userTimeLine.index'));

    }
}
