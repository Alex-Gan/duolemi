<?php
namespace App\Services\Api;

class BaseService
{
    /**
     * 输出结果格式化
     *
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function formatResponse(int $code = 0, $msg = 'success', $data = [])
    {
        $response_data = [
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ];

        $response = response()->json($response_data);

        return $response;
    }

    /**
     * 获取微信小程序的调用凭据
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAccessToken()
    {
        $cache_access_token = \Cache::get('cache_access_token');
        if (!empty($cache_access_token)) {
            return $cache_access_token;
        } else {
            $appid = config("wx.app_id");
            $appsecret = config("wx.app_secret");
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $response = curl_request($url);

            if (!empty($response)) {
                $response_arr = json_decode($response, true);
                /*获取access_token成功，存入cache中*/
                if (!empty($response_arr['access_token'])) {
                    /*写入缓存cache中, 并返回*/
                    \Cache::put('cache_access_token', $response_arr['access_token'], (7200-120)/60);
                    return $response_arr['access_token'];
                } else {
                    return $this->getAccessToken();
                }
            }
        }
    }
}