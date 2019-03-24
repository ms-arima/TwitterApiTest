<?php

namespace App\Http\Model;


use App\Http\Model\Libs\TwitterCurl;
use App\Http\Model\Libs\TwitterCurlInnerAccessToken;

class TwitterStatus
{

    const API_STATUS_VERSION = '1.1';


    /**
     * @param string $oauth_token
     * @param string $oauth_token_secret
     * @param array $user_id
     * @return array
     * @throws \Exception
     */
    public static function getUserTimeLines(string $oauth_token, string $oauth_token_secret, array $user_id)
    {

        $urlQuery = array(
            'count' => 10
        );
        $urlQuery = array_merge($user_id, $urlQuery);


        $twitterClient = new TwitterCurl(
            self::API_STATUS_VERSION . "/statuses/user_timeline.json",
            "GET",
            $urlQuery
        );

        $params = array(
            'oauth_token' => $oauth_token,
        );
        $params = array_merge($urlQuery, $params);
        $result = $twitterClient->handleRequest(
            $params,
            $oauth_token_secret
        );

        $response_json = json_decode($result);

        $json = [];
        foreach ($response_json as $data) {
            $json[] = array(
                'id' => $data->id_str,
                'text' => $data->text,
                'tweeter_name' => $data->user->name,
                'tweeter_screen_name' => $data->user->screen_name,
                'tweeter_profile_image_url' => $data->user->profile_image_url,
            );

        }

        return $json;
    }


    /**
     * @param $tweet_id
     * @throws \Exception
     */
    public static function deleteTweet($tweet_id)
    {
        $params = array(
            'oauth_token' => TwitterConfig::ACCESS_TOKEN
        );

        $twitterClient = new TwitterCurl(
            self::API_STATUS_VERSION . "/statuses/destroy/{$tweet_id}.json",
            "POST"
        );

        $twitterClient->handleRequest(
            $params,
            TwitterConfig::ACCESS_TOKEN_SECRET
        );

    }


    /**
     * @param string $text
     * @throws \Exception
     */
    public static function addTweet(string $text)
    {

        $urlQuery = array(
            'status' => $text
        );

        $params = array(
            'oauth_token' => TwitterConfig::ACCESS_TOKEN,
        );
        $params = array_merge($urlQuery, $params);

        $twitterClient = new TwitterCurl(
            self::API_STATUS_VERSION . "/statuses/update.json",
            "POST",
            $urlQuery
        );

        $twitterClient->handleRequest(
            $params,
            TwitterConfig::ACCESS_TOKEN_SECRET
        );

    }

}