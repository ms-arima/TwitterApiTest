<?php

namespace App\Http\Model;


use App\Libs\TwitterConfig;
use App\Libs\TwitterCurl;

class TwitterStatus
{


    /**
     * @param string $oauth_token
     * @param string $oauth_token_secret
     * @param string $user_id
     * @return array
     * @throws \Exception
     */
    public static function getUserTimeLines(string $oauth_token, string $oauth_token_secret, string $user_id)
    {
        $twitterClient = new TwitterCurl();
        $urlQuery = array(
            'user_id' => $user_id,
            'count' => 10
        );
        $params = $urlQuery;
        $params['oauth_token'] = $oauth_token;
        $result = $twitterClient->handleRequest(
            "1.1/statuses/user_timeline.json",
            "GET",
            $params,
            $oauth_token_secret,
            $urlQuery
        );

        if ($result['curl_info']['http_code'] != 200) {
            throw new \Exception("API アクセスエラー " . $result['curl_info']['http_code'] . " " . $result['response']);
        }

        $response_json = json_decode($result['response']);

        $json = [];
        foreach ($response_json as $data) {
            $json[] = array(
                'text' => $data->text,
                'tweeter_name' => $data->user->name,
                'tweeter_screen_name' => $data->user->screen_name,
                'tweeter_profile_image_url' => $data->user->profile_image_url,
            );

        }

        return $json;
    }

}