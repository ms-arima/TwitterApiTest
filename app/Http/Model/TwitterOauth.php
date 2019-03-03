<?php

namespace App\Http\Model;


use App\Libs\TwitterConfig;
use App\Libs\TwitterCurl;

class TwitterOauth
{


    /**
     * @return array
     * @throws \Exception
     */
    public static function getOauthRequestToken()
    {
        $twitterClient = new TwitterCurl();
        $params = array(
            "oauth_callback" => TwitterConfig::OAUTH_CALLBACK_URL,
        );
        $result = $twitterClient->handleRequest('oauth/request_token', "POST", $params);
        if ($result['curl_info']['http_code'] != 200) {
            throw new \Exception("API アクセスエラー " . $result['curl_info']['http_code'] . " " . $result['response']);
        }

        $token = [];
        parse_str($result['response'], $token);
        return $token;
    }

    /**
     * @param string $oauth_token
     * @param string $oauth_verifier
     * @param string $oauth_token_secret
     * @return array
     * @throws \Exception
     */
    public static function getOauthAccessToken(string $oauth_token,string  $oauth_verifier,string  $oauth_token_secret)
    {
        $params = array(
            "oauth_token" => $oauth_token,
            "oauth_verifier" => $oauth_verifier
        );

        $twitterClient = new TwitterCurl();
        $result = $twitterClient->handleRequest("/oauth/access_token", "POST", $params, $oauth_token_secret);
        if ($result['curl_info']['http_code'] != 200) {
            throw new \Exception("API アクセスエラー " . $result['curl_info']['http_code'] . " " . $result['response']);
        }

        $token = [];
        parse_str($result['response'], $token);
        return $token;
    }
}