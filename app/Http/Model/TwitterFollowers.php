<?php

namespace App\Http\Model;


use App\Libs\TwitterConfig;
use App\Libs\TwitterCurl;
use Illuminate\Support\Facades\Log;

class TwitterFollowers
{

    const API_STATUS_VERSION = '1.1';

    /**
     * @param array $user_id
     * @param string $cursor_id
     * @return array
     * @throws \Exception
     */
    public static function getFollowers(array $user_id, string $cursor_id)
    {
        $urlQuery = array(
            'count' => 3,
        );
        if (filled($cursor_id)) {
            //ページネーション
            $urlQuery += array(
                'cursor' => $cursor_id,
            );

        }
        $urlQuery = array_merge($user_id, $urlQuery);


        $twitterClient = new TwitterCurl(
            self::API_STATUS_VERSION . "/followers/list.json",
            "GET",
            $urlQuery
        );

        $params = array(
            'oauth_token' => TwitterConfig::ACCESS_TOKEN,
        );
        $params = array_merge($urlQuery, $params);
        $result = $twitterClient->handleRequest(
            $params,
            TwitterConfig::ACCESS_TOKEN_SECRET
        );

        $response_json = json_decode($result);

        \Log::debug($result);
        $json = [];
        foreach ($response_json->users as $data) {
            $json['users'][] = array(
                'id' => $data->id_str,
                'tweeter_name' => $data->name,
                'tweeter_screen_name' => $data->screen_name,
            );
        }
        $json['next_cursor'] = $response_json->next_cursor_str;
        $json['prev_cursor'] = $response_json->previous_cursor_str;
        return $json;

    }

}