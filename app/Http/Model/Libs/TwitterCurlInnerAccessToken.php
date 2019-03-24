<?php

namespace App\Http\Model\Libs;

/**
 * Class TwitterCurlInnerAccessToken
 * @package App\Http\Model\Libs
 */
class TwitterCurlInnerAccessToken
{


    private $headerQuery = "";
    private $header = "";
    private $request_url = "";
    private $request_url_query = "";
    private $request_method = "";

    /**
     * TwitterCurl constructor.
     * @param string $request_url_path
     * @param array $request_url_query
     */
    public function __construct(string $request_url_path, array $request_url_query = [])
    {

        $this->request_url = TwitterConfig::BASE_URL . $request_url_path;
        if ($request_url_query) {
            $this->request_url_query = $this->request_url . '?' . http_build_query($request_url_query);
        } else {
            $this->request_url_query = $this->request_url;
        }

    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function get(array $params = [])
    {
        $this->request_method = 'GET';
        return $this->handleRequest($params);
    }

    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function post(array $params = [])
    {
        $this->request_method = 'POST';
        return $this->handleRequest($params);
    }


    /**
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    private function handleRequest(array $params = [])
    {
        $this->createHeaderQuery($params);
        $this->createAuthorizationHeader();
        $response = $this->execCurl();

        return $response;
    }

    /**
     * @param array $params
     */
    private function createHeaderQuery(array $params = [])
    {
        $oauth_params = array(
            "oauth_consumer_key" => TwitterConfig::API_KEY,
            "oauth_signature_method" => TwitterConfig::SIGNATURE_METHOD,
            "oauth_timestamp" => time(),
            "oauth_nonce" => microtime(),
            'oauth_token' => TwitterConfig::ACCESS_TOKEN,
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
        $replace_targets = array(
            '+' => '%20',
            '%7E' => '~'
        );
        $request_params = str_replace(array_keys($replace_targets), array_values($replace_targets), $request_params);

        $request_params = rawurlencode($request_params);

        $encoded_request_method = rawurlencode($this->request_method);
        $encoded_request_url = rawurlencode($this->request_url);
        $signature_data = $encoded_request_method . "&" . $encoded_request_url . "&" . $request_params;

        $signature_key = rawurlencode(TwitterConfig::API_SECRET) . "&" . rawurlencode(TwitterConfig::ACCESS_TOKEN_SECRET);
        $hash = hash_hmac("sha1", $signature_data, $signature_key, TRUE);
        $signature = base64_encode($hash);
        $oauth_params["oauth_signature"] = $signature;

        $this->headerQuery = http_build_query($oauth_params, "", ",");
        \Log::debug($this->headerQuery);
    }


    /**
     *
     * @return mixed
     * @throws \Exception
     */
    private function execCurl()
    {

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->request_url_query);    // リクエストURL
        curl_setopt($curl, CURLOPT_HEADER, true);    // ヘッダーを取得する
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->request_method);    // メソッド
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);    // 証明書の検証を行わない
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);    // curl_execの結果を文字列で返す
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);    // リクエストヘッダーの内容
        curl_setopt($curl, CURLOPT_TIMEOUT, TwitterConfig::TIME_OUT_SECOND);    // タイムアウトの秒数

        $response = curl_exec($curl);
        $result_info = curl_getinfo($curl);

        if (($error = curl_error($curl)) !== '') {
            curl_close($curl);
            throw new \Exception('Twitter Curl Error: ' . $error);
        }

        curl_close($curl);

        // 取得したデータ
        $curl_result = substr($response, $result_info["header_size"]);
        $response_http_code = $result_info['http_code'];

        if ($response_http_code != 200) {
            throw new \Exception("Twitter API Response Error: " . $response_http_code . " " . $curl_result);
        }

        return $curl_result;
    }

    /**
     *
     */
    private function createAuthorizationHeader()
    {
        $this->header = array(
            "Authorization: OAuth " . $this->headerQuery,
        );
    }


}