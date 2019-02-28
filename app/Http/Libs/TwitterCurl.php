<?php

namespace App\Http\Libs;
class TwitterCurl
{


    private $api_key = "";
    private $api_secret = "";

    public function __construct(){
        $this->api_key = env('TWITTER_API_KEY');
        $this->api_secret = env('TWITTER_API_KEY_SECRET');
    }


    public function getOauthToken($request_url, $request_method, $addParams = [], $access_token_secret = "", $urlQuery = [])
    {

        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode($this->api_secret) . "&" . rawurlencode($access_token_secret);


        // パラメータ([oauth_signature]を除く)を連想配列で指定
        $params = $addParams + array(
                "oauth_consumer_key" => $this->api_key,
                "oauth_signature_method" => "HMAC-SHA1",
                "oauth_timestamp" => time(),
                "oauth_nonce" => microtime(),
                "oauth_version" => "1.0",
            );

        // 各パラメータをURLエンコードする
        foreach ($params as $key => $value) {
            // コールバックURLはエンコードしない
            if ($key == "oauth_callback") {
                continue;
            }

            // URLエンコード処理
            $params[$key] = rawurlencode($value);
        }

        // 連想配列をアルファベット順に並び替える
        ksort($params);

        // パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
        $request_params = http_build_query($params, "", "&");

        // 一部の文字列をフォロー
        $request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);

        // 変換した文字列をURLエンコードする
        $request_params = rawurlencode($request_params);

        // リクエストメソッドをURLエンコードする
        $encoded_request_method = rawurlencode($request_method);

        // リクエストURLをURLエンコードする
        $encoded_request_url = rawurlencode($request_url);

        // リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
        $signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params;

        // キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
        $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);

        // base64エンコードして、署名[$signature]が完成する
        $signature = base64_encode($hash);

        // パラメータの連想配列、[$params]に、作成した署名を加える
        $params["oauth_signature"] = $signature;

        // パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
        $header_params = http_build_query($params, "", ",");

        // リクエスト用のコンテキストを作成する
        $context = array(
            "http" => array(
                "method" => $request_method, // リクエストメソッド (POST)
                "header" => array(              // カスタムヘッダー
                    "Authorization: OAuth " . $header_params,
                ),
            ),
        );

        // パラメータがある場合、URLの末尾に追加
        if ($urlQuery) {
            $request_url .= '?' . http_build_query($urlQuery);
        }

        // cURLを使ってリクエスト
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得する
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $context["http"]["method"]);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $context["http"]["header"]);    // リクエストヘッダーの内容
        curl_setopt($curl, CURLOPT_TIMEOUT, 5);    // タイムアウトの秒数
        $res1 = curl_exec($curl);
        $res2 = curl_getinfo($curl);
        curl_close($curl);

        // 取得したデータ
        $response = substr($res1, $res2["header_size"]);    // 取得したデータ(JSONなど)
        $header = substr($res1, 0, $res2["header_size"]);    // レスポンスヘッダー (検証に利用したい場合にどうぞ)
        return $response;
    }

}