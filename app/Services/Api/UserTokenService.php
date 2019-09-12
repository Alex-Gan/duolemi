<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 10:28
 */
namespace App\Services\Api;

class UserTokenService
{
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    /**
     * 构造函数中赋值成员变量
     *
     * UserTokenService constructor.
     * @param $code
     */
    public function __construct($code)
    {
        $this->wxAppId = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppId, $this->wxAppSecret, $code);
    }

    /**
     * 获取用户的令牌方法
     *
     * @return mixed
     * @throws \Exception
     */
    public function getUserToken()
    {
        //调用公共函数中的http方法(也就是curl的方法,我也是在网上抄的。存放在common.php中就可以直接用了）
        $result = curl_request($this->wxLoginUrl);
        //判断连接是否成功
        if ($result[0] == 0) {
            //将返回的json处理成数组
            $wxResult = json_decode($result[1], true);
            //判空
            if (empty($wxResult)) {
                throw new \Exception('获取session_key,openID时异常，微信内部错误');
            } else {
                //判断返回的结果中是否有错误码
                if (isset($wxResult['errcode'])) {
                    //如果有错误码，调用抛出错误方法
                    $this->_throwWxError($wxResult);
                } else {
                    //没有错误码，调用私有的派发token方法
                    $token = $this->_grantToken($wxResult);
                    return $token;
                }
            }
        } else {
            throw new \Exception('连接微信服务器失败');
        }
    }

    /**
     * 微信获取open_id失败，抛出异常方法
     * @param $wxResult
     * @throws \Exception
     */
    private function _throwWxError($wxResult)
    {
        throw  new \Exception(
            [
                'message' => $wxResult['errmsg'],
                'errorCode' => $wxResult['errcode']
            ]
        );
    }


    /**
     * 派发User 令牌
     *
     * @param $wxResult
     * @return mixed
     * @throws \Exception
     */
    private function _grantToken($wxResult)
    {
        //拿到open_id
        $openId = $wxResult['openid'];
        //判断open_id是否存在
        $id = User::getUidByOpenId($openId);
        //如果数据库中不存在
        if (!$id) {
            //添加一条记录,返回新创建的id
            $id = User::createUser($openId);
            if (!$id) {
                throw new \Exception('新增一条User失败');
            }
        }
        //拼接数据为一个数组。（这个方法就是将wxResult中的openid和session_key取出，然后和用户id一起放进一个数组）
        $tokenValue = $this->_splicingValue($wxResult, $id);
        //制作令牌
        //存入缓存
        $token = $this->_saveCache($tokenValue);
        //返回token
        return $token;
    }

    /**
     *
     *
     * @param $wxResult
     * @param $uid
     * @return mixed
     */
    private function _splicingValue($wxResult, $uid)
    {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        return $cachedValue;
    }

    /**
     * 将令牌存入缓存,返回token
     *
     * @param $tokenValue
     * @return mixed
     * @throws \Exception
     */
    private function _saveCache($tokenValue)
    {
        //调用父类中的随机字符串方法
        $key = parent::_makeToken();
        //序列化包含id,openid,sessionKey的数组
        $value = serialize($tokenValue);
        //在配置中取出保存时间的配置
        $expriesTime = config('wx.expires_in');
        //存入缓存
        $result = \cache($key, $value, $expriesTime);
        //如果存入失败，抛出异常
        if (!$result) {
            throw new \Exception(
                ['errorCode' => 10003, 'message' => 'Token save fail']
            );
        }
        //返回随机字符串（也就是要返回给客户端的token)
        return $key;
    }

    /**
     * 构建token随机字符串
     */
    public function _makeToken()
    {
        //随机抽取32位字符串方法，保存在common.php中
        $randChar = $this->getRandChar(32);
        //时间戳
        $timestamp = time();
        //配置中的盐值
        $salt = config('secret.token_salt');
        //拼接之后sha1加密
        return sha1($randChar . $timestamp . $salt);
    }

    /**
     * 随机生成32位的
     *
     * @param $length
     * @return string|null
     */
    private function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol) - 1;

        for ($i = 0;
             $i < $length;
             $i++) {
            $str .= $strPol[rand(0, $max)];
        }

        return $str;
    }
}