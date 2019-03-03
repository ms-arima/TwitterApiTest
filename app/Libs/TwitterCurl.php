<?php

namespace App\Libs;
class TwitterCurl
{


    private $api_key = "";
    private $api_secret = "";

    public function __construct($api_key = TwitterConfig::API_KEY, $api_secret = TwitterConfig::API_SECRET)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }


    /**
     * @param string $request_url_path
     * @param string $request_method
     * @param array $params
     * @param string $request_token_secret
     * @param array $request_url_query
     * @return mixed
     * @throws \Exception
     */
    public function handleRequest(string $request_url_path, string $request_method, array $params = [], string $request_token_secret = "", array $request_url_query = [])
    {
        $request_url = TwitterConfig::BASE_URL . $request_url_path;
        $headerQuery = $this->createHeaderQuery($request_url, $request_method, $params, $request_token_secret);
        $header = $this->createAuthorizationHeader($headerQuery);
        if ($request_url_query) {
            $request_url .= '?' . http_build_query($request_url_query);
        }

        $response = $this->execCurl($request_url, $request_method, $header);

        return $response;
    }

    /**
     * @param string $request_url
     * @param string $request_method
     * @param array $params
     * @param string $request_token_secret
     * @return string
     */
    private function createHeaderQuery(string $request_url, string $request_method, array $params, string $request_token_secret): string
    {
        // キーを作成する (URLエンコードする)
        $signature_key = rawurlencode($this->api_secret) . "&" . rawurlencode($request_token_secret);

        $oauth_params = array(
            "oauth_consumer_key" => $this->api_key,
            "oauth_signature_method" => TwitterConfig::SIGNATURE_METHOD,
            "oauth_timestamp" => time(),
            "oauth_nonce" => microtime(),
            "oauth_version" => TwitterConfig::OAUTH_VERSION,
        );

        $oauth_params = array_merge($params, $oauth_params);

        // 各パラメータをURLエンコードする
        foreach ($oauth_params as $key => $value) {
            // コールバックURLはエンコードしない
            if ($key == "oauth_callback") {
                continue;
            }

            // URLエンコード処理
            $oauth_params[$key] = rawurlencode($value);
        }

        ksort($oauth_params);

        $request_params = http_build_query($oauth_params, "", "&");
//        $request_params = str_replace(array('+', '%7E'), array('%20', '~'), $request_params);
        $request_params = str_replace([
            '+',
            '!',
            '*',
            "'",
            '(',
            ')',
            '%20'
        ], [
            '%7E',
            '%21',
            '%2A',
            '%27',
            '%28',
            '%29',
            '~'
        ], $request_params);

        $request_params = rawurlencode($request_params);

        $encoded_request_method = rawurlencode($request_method);
        $encoded_request_url = rawurlencode($request_url);
        $signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params;

        $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);
        $signature = base64_encode($hash);
        $oauth_params["oauth_signature"] = $signature;
        $header = http_build_query($oauth_params, "", ",");

        \Log::debug($header);
        return $header;
    }

    /**
     * @param string $request_url
     * @param string $request_method
     * @param array $header
     * @return mixed
     * @throws \Exception
     */
    private function execCurl(string $request_url, string $request_method, array $header)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request_url);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得する
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request_method);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);    // リクエストヘッダーの内容
        curl_setopt($curl, CURLOPT_TIMEOUT, TwitterConfig::TIME_OUT_SECOND);    // タイムアウトの秒数
        $response = curl_exec($curl);
        $result_info = curl_getinfo($curl);

        if (($error = curl_error($curl)) !== '') {
            curl_close($curl);
            throw new \Exception($error);
        }

        curl_close($curl);


        // 取得したデータ
        $curl_result['response'] = substr($response, $result_info["header_size"]);
        $curl_result['curl_info'] = $result_info;
        return $curl_result;
    }

    /**
     * @param string $headerQuery
     * @return array
     */
    private function createAuthorizationHeader(string $headerQuery): array
    {
        $header = array(
            "Authorization: OAuth " . $headerQuery,
        );
        return $header;
    }


}