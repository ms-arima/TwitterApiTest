<?php

namespace App\Http\Controllers;

use App\Http\Libs\TwitterCurl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class HomeController extends Controller
{
    /**
     * Twitter 認証動作確認
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        if (Input::has("oauth_token") && Input::has("oauth_verifier")) {
            //「連携アプリを認証」をクリックして帰ってきた時
            $params = [
                "oauth_token" => Input::get("oauth_token"),
                "oauth_verifier" => Input::get("oauth_verifier")
            ];
            session_start();
            $request_token_secret = $_SESSION["oauth_token_secret"];
            $twitterClient = new TwitterCurl();
            $response = $twitterClient->getOauthToken("https://api.twitter.com/oauth/access_token", "POST", $params, $request_token_secret);
            $token = [];
            parse_str($response, $token);

            $message = '連携成功';

            // タイムライン取得

            $params = [
                "oauth_token" => $token['oauth_token'],
                'user_id' => $token['user_id'],
                'count' => 10
            ];
            $urlQuery = [
                'user_id' => $token['user_id'],
                'count' => 10
            ];
            $timelines = $twitterClient->getOauthToken(
                "https://api.twitter.com/1.1/statuses/user_timeline.json",
                "GET",
                $params,
                $token['oauth_token_secret'],
                $urlQuery
            );
            $timelines = json_decode($timelines);
            return view('welcome', compact('token', 'message', 'timelines'));
        } elseif (Input::has("denied")) {
            //「キャンセル」をクリックして帰ってきた時
            $message = '連携失敗';
            return view('welcome', compact('message'));
        } else {
            $twitterClient = new TwitterCurl();
            $params = [
                "oauth_callback" => "http://127.0.0.1:8000",
            ];
            $response = $twitterClient->getOauthToken("https://api.twitter.com/oauth/request_token", "POST", $params);
            $token = [];
            parse_str($response, $token);
            if (filled($token) && array_key_exists('token', $token)) {
                session_start();
                session_regenerate_id(true);
                $_SESSION["oauth_token_secret"] = $token["oauth_token_secret"];
            }
            return view('welcome', compact('token'));
        }
    }
}
