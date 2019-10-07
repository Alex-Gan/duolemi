<?php
namespace App\Services\Api;

use App\Models\Guider;
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
     * @param $falg
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersonalOpenIdByCode($code, $falg = true)
    {
        if (empty($code)) {
            return $this->formatResponse(404, 'code不存在');
        }

        //开始进行授权
        $authorize_data = $this->authorizeData($code);

        if (isset($authorize_data['errcode'])) {
            return $this->formatResponse($authorize_data['errcode'], $authorize_data['errmsg']);
        } else {

            /*将openid存入member表*/
            $member_has = Member::where('openid', $authorize_data['openid'])->exists();
            if (!$member_has) {
                Member::create([
                    'openid' => $authorize_data['openid'],
                    'created_at' => date("Y-m-d", time())
                ]);
            }

            if ($falg) {
                $response_data = [
                    'openid' => $authorize_data['openid']
                ];
                return $this->formatResponse(0, 'ok', $response_data);
            } else {
                $response_data = [
                    'openid' => $authorize_data['openid'],
                    'session_key' => $authorize_data['session_key']
                ];
                return $response_data;
            }
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
     * 同步微信用户信息到服务端（保存用户头像昵称）
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function syncPersonalData($data)
    {
        $validator = \Validator::make($data, [
            'openid' => 'required',
            'nickName' => 'required',
            'faceImg'=> 'required',
        ],[
            'openid.required' => 'openid不能为空',
            'nickName.required' => '昵称不能为空',
            'faceImg.required' => '头像不能为空',
        ]);
        if ($validator->fails()) {
            return $this->formatResponse(400, $validator->messages()->first());
        }

        $member = $this->model::where('openid', $data['openid'])->first();
        if ($member) {

            $member->nickname = $data['nickName'];
            $member->avatar = $data['faceImg'];
            $res = $member->save();

            if ($res) {
                return $this->formatResponse(0, 'ok', $data);
            } else {
                return $this->formatResponse(1, '保存失败');
            }
        } else {
            return $this->formatResponse(1, '保存失败');
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

    /**
     * 获取微信授权的手机号
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPhoneNumber($data)
    {
        $authorizeData = $this->getPersonalOpenIdByCode($data['code'], false);
        if (empty($authorizeData['session_key'])) {
            return $this->formatResponse(404, 'code已失效');
        }

        $validator = \Validator::make($data, [
            'code' => 'required',
            'encryptedData' => 'required',
            'iv' => 'required',
            'openid'=> 'required',
        ],[
            'code.required' => 'code不能为空',
            'encryptedData.required' => 'encryptedData不能为空',
            'iv.required' => 'iv不能为空',
            'openid.required' => 'openid不能为空',
        ]);

        if ($validator->fails()) {
            return $this->formatResponse(400, $validator->messages()->first());
        }

        /*获取会员信息*/
        $member_has = $this->model::where('openid', $data['openid'])->exists();
        if (empty($member_has)) {
            return $this->formatResponse(404, '会员信息为空');
        }

        /*解密手机号*/
        $appId = config('wx.app_id');
        $sessionKey = $authorizeData['session_key'];
        $errCode =  (new WXBizDataCrypt($appId, $sessionKey))->decryptData($data['encryptedData'],$data['iv'], $new_data);

        if ($errCode == 0) {
            $data_arr = json_decode($new_data, true);
            /*将手机号存入数据库*/
            //$this->model::where('openid', $data['openid'])->update(['mobile' => $data_arr['purePhoneNumber']]);
            return $this->formatResponse(0, 'ok', ['mobile' => $data_arr['purePhoneNumber']]);
        } else {

            return $this->formatResponse(1, '解密获取微信授权的手机号失败');
        }
    }

    /**
     * 绑定手机号
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function register($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';
        $mobile = !empty($data['mobile']) ? $data['mobile'] : '';
        if (empty($openid)) {
            return $this->formatResponse(404, 'openid不能为空');
        }

        if (empty($mobile)) {
            return $this->formatResponse(404, '手机号不能为空');
        }

        /*获取会员信息*/
        $member_has = $this->model::where('openid', $data['openid'])->exists();
        if (empty($member_has)) {
            return $this->formatResponse(404, '会员信息为空');
        }

        $res = $this->model::where('openid', $openid)->update(['mobile' => $mobile]);
        if ($res) {
            return $this->formatResponse(0, 'ok');
        } else {
            return $this->formatResponse(1, '绑定手机号失败');
        }
    }

    /**
     * 个人信息
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserInfo($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if (empty($openid)) {
            return $this->formatResponse(404, 'openid不能为空');
        }

        /*获取会员信息*/
        $member = $this->model::select(['id', 'nickname as nickName', 'avatar as faceImg', 'mobile'])
            ->where('openid', $data['openid'])
            ->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息为空');
        }

        /*是否绑定了手机号*/
        if (!empty($member->mobile)) {
            $member->is_binding = 1;
        } else {
            $member->is_binding = 0;
        }

        /*会员身份 1，普通用户，2，推广员*/
        $guider = Guider::where('member_id', $member->id)->exists();
        $member->role = $guider ? 2 : 1;

        return $this->formatResponse(0, 'ok', $member);
    }

    /**
     * 我的二维码
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCode($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if (empty($openid)) {
            return $this->formatResponse(404, 'openid不能为空');
        }

        /*获取会员信息*/
        $member_has = $this->model::where('openid', $openid)->exists();

        if (empty($member_has)) {
            return $this->formatResponse(404, '会员信息为空');
        }

        $data = [
            'code' => env('APP_URL').'/images1/code.jpg'
        ];

        return $this->formatResponse(0, 'ok', $data);
    }
}