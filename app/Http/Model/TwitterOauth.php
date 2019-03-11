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
        $twitterClient = new TwitterCurl('oauth/request_token', "POST");
        $params = array(
            "oauth_callback" => TwitterConfig::OAUTH_CALLBACK_URL,
        );
        $result = $twitterClient->handleRequest($params);

        $token = [];
        parse_str($result, $token);
        return $token;
    }

    /**
     * @param string $oauth_token
     * @param string $oauth_verifier
     * @param string $oauth_token_secret
     * @return array
     * @throws \Exception
     */
    public static function getOauthAccessToken(string $oauth_token, string $oauth_verifier, string $oauth_token_secret)
    {
        $params = array(
            "oauth_token" => $oauth_token,
            "oauth_verifier" => $oauth_verifier
        );

        $twitterClient = new TwitterCurl("/oauth/access_token", "POST");
        $result = $twitterClient->handleRequest($params, $oauth_token_secret);

        $token = [];
        parse_str($result, $token);
        return $token;
    }
}