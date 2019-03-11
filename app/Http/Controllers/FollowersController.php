<?php

namespace App\Http\Controllers;

use App\Http\Model\TwitterFollowers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class FollowersController extends Controller
{

    public function index()
    {
        $user_id = array(
            'screen_name' => 'nikkei' //閲覧したいフォロワーのIDを入力
        );
        $timelines = TwitterFollowers::getFollowers($user_id, Input::get('cursor', ''));

        return view('follower', compact('timelines'));
    }

}
