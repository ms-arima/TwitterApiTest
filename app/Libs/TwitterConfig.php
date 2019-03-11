<?php

namespace App\Libs;

/**
 * Twitter const
 * @package Vasta\Lib\Twitter
 */
class TwitterConfig
{
    const API_KEY = "";
    const API_SECRET = "";
    const ACCESS_TOKEN = "";
    const ACCESS_TOKEN_SECRET = "";
    // 本番用
    // const BASE_URL = 'https://ads-api.twitter.com/4/';
    // テスト用
    // const BASE_URL = 'https://ads-api-sandbox.twitter.com/4/';
    const BASE_URL = 'https://api.twitter.com/';
    const SIGNATURE_METHOD = 'HMAC-SHA1'; //変更する場合は App\Lib\TwitterCurl::createHeaderQueryのハッシュ値生成部分も変更する可能性有り。
    const OAUTH_VERSION = '1.0';
    const TIME_OUT_SECOND = 10;

    const OAUTH_CALLBACK_URL = "http://127.0.0.1:8000/threeLeggedOauth"; // 3者間認証用
}
