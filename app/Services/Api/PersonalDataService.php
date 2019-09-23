<?php
namespace App\Services\Api;

use App\Models\Member;

class PersonalDataService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * PersonalDataService constructor.
     * @param Member $member
     */
    public function __construct(Member $member)
    {
        $this->model = $member;
    }

    /**
     * 通过code微信换取身份openid
     *
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersonalOpenIdByCode($code)
    {
        if (empty($code)) {
            return $this->formatResponse(404, 'code不存在');
        }

        //开始进行授权
        $authorize_data = $this->authorizeData($code);

        if (isset($authorize_data['errcode'])) {
            return $this->formatResponse($authorize_data['errcode'], $authorize_data['errmsg']);
        } else {
            $response_data = [
                'openid' => $authorize_data['openid']
            ];
            return $this->formatResponse(0, 'ok', $response_data);
        }
    }

    /**
     * 授权方法组装
     *
     * @param $code
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authorizeData($code)
    {
        $wxAppId = config('wx.app_id');
        $wxAppSecret = config('wx.app_secret');
        $wxLoginUrl = sprintf(config('wx.login_url'), $wxAppId, $wxAppSecret, $code);

        $client = new \GuzzleHttp\Client();

        $res = $client->request('GET', $wxLoginUrl);

        $response = $res->getBody()->getContents();

        //将json格式变成array
        $responseArr = json_decode($response, true);

        return $responseArr;
    }

    /**
     * 同步微信用户信息到服务端
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPersonalData($data)
    {
        $validator = \Validator::make($data, [
            'openid' => 'required',
            'nickname' => 'required',
            'avatar'=> 'required',
        ],[
            'openid.required' => 'openid不能为空',
            'nickname.required' => '手机验证码不能为空',
            'avatar.required' => '手机验证码不能为空',
        ]);
        if ($validator->fails()) {
            return $this->formatResponse(400, $validator->messages()->first());
        }

        $member_has = $this->model::where('openid', $data['openid'])->exists();
        if ($member_has) {
            return $this->formatResponse(0, 'ok');
        }

        $res = $this->model::create([
            'openid' => $data['openid'],
            'nickname' => $data['nickname'],
            'avatar' => $data['avatar'],
            'created_at' => date("Y-m-d H:i:s", time())
        ]);

        if ($res) {
            return $this->formatResponse(0, 'ok');
        } else {
            return $this->formatResponse(1, '同步失败');
        }
    }

    /**
     * 检查用户是否登录过
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkLogin($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if (empty($openid)) {
            return $this->formatResponse(404, 'openid不能为空');
        }

        $member_data = $this->model::where('openid', $openid)->first();

        if (!empty($member_data)) {
            return $this->formatResponse(0, 'ok', $member_data);
        }

        return $this->formatResponse(1, '暂无数据');
    }
}