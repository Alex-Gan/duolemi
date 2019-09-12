<?php
if (! function_exists('curl_request')) {
    //参数1：访问的URL，参数2：post数据(不填则为GET)，参数3：提交的$cookies,参数4：是否返回$cookies
    /**
     * curl 请求
     * @param string $url
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param string $encoding
     * @param int $timeout
     * @return mixed
     * @throws \Exception
     */
     function curl_request($url, $method = 'GET', $data = null, $headers = [], $encoding = null, $timeout = 30) {
        $method = strtoupper($method);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if (!empty($encoding)) {
            curl_setopt($ch, CURLOPT_ENCODING, $encoding);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $result = curl_exec($ch);

        if ($result === false) {
            throw new \Exception('Curl error :['.$url.']['.curl_errno($ch).'] '.curl_error($ch));
        }

        curl_close($ch);
        return $result;
    }
}