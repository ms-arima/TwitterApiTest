<?php

namespace App\Http\Model;



use App\Http\Model\Libs\TwitterConfig;
use App\Http\Model\Libs\TwitterCurl;

class TwitterOauth
{


    /**
     * @return array
     * @throws \Exception
     */
    public static function getOauthRequestToken()
    {
        $twitterClient = new TwitterCurl('oauth/request_token');
        $params = array(
            "oauth_callback" => TwitterConfig::OAUTH_CALLBACK_URL,
        );
        $result = $twitterClient->post($params);

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

        $twitterClient = new TwitterCurl("/oauth/access_token");
        $result = $twitterClient->post($params, $oauth_token_secret);

        $token = [];
        parse_str($result, $token);
        return $token;
    }
}