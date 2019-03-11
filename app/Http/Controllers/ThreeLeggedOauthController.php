<?php

namespace App\Http\Controllers;

use App\Http\Model\TwitterOauth;
use App\Http\Model\TwitterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ThreeLeggedOauthController extends Controller
{
    /**
     * Twitter 認証動作確認
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {

        if (Input::has("oauth_token") && Input::has("oauth_verifier")) {
            //「連携アプリを認証」をクリックして帰ってきた時
            session_start();
            $request_token_secret = $_SESSION["oauth_token_secret"];

            $token = TwitterOauth::getOauthAccessToken(Input::get("oauth_token"), Input::get("oauth_verifier"), $request_token_secret);

            $message = '認証成功';

            // タイムライン取得
            $user_id = array(
                'user_id' => '1100327713007529985'
            );

            $timelines = TwitterStatus::getUserTimeLines($token['oauth_token'], $token['oauth_token_secret'], $user_id);

            return view('3-legged-oauth', compact('token', 'message', 'timelines'));
        } elseif (Input::has("denied")) {
            //「キャンセル」をクリックして帰ってきた時
            $message = '認証キャンセル';
            return view('3-legged-oauth', compact('message'));
        } else {
            $token = TwitterOauth::getOauthRequestToken();
            session_start();
            session_regenerate_id(true);
            $_SESSION["oauth_token_secret"] = $token["oauth_token_secret"];
            $authorizeUrl = "https://api.twitter.com/oauth/authorize?oauth_token={$token["oauth_token"]}&lang=ja";
            return view('3-legged-oauth', compact('token', 'authorizeUrl'));
        }
    }
}
