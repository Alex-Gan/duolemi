<?php
namespace App\Services\Api;

class WxPayService extends BaseService
{

    const WX_PAY_URL = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; //微信统一下单地址
    const WX_NOTIFY_URL = 'https://dlm.loverabbit.cn/api/notify_url'; //微信回调通知地址

    /**
     * 微信支付（小程序版本）
     *
     * @param $data
     * @return array
     */
    public function WxPay($data)
    {
        /*获取openid*/
        $openid = !empty($data['openid']) ? $data['openid'] : '';
        $total_fee = $data['total_fee']*100; //单位为分需转成元
        $attach = $data['attach']; //附加数据，如 {'name': '张三', 'mobile': '15000319670'}

        $pay_sign_params = array(
            'appid'            => config('wx.app_id'),
            'mch_id'           => config('wx.mch_id'),
            'nonce_str'        => self::getNonceStr(),
            'body'             => '购买体验课',
            //'attach'           => $attach,
            'out_trade_no'     => self::getOutTradeNo(),
            'total_fee'        => $total_fee,
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'notify_url'       => self::WX_NOTIFY_URL,
            'trade_type'       => 'JSAPI',
            'openid'           => $openid
        );

        /*通过签名算法计算得出的签名值*/
        $sign = self::MakeSign($pay_sign_params);

        /*将sign进行签名*/
        $pay_sign_params['sign'] = $sign;

        /*参数值用XML转义*/
        $pay_sign_params_xml = $this->ToXml($pay_sign_params);

        /*统一下单*/
        $response = self::postXmlCurl($pay_sign_params_xml, self::WX_PAY_URL);

        /*参数值转array*/
        $response_arr = $this->xmlToArray($response);
        //dd($response_arr);

        /*统一下单成功，继续进行签名*/
        if ($response_arr['return_code'] === 'SUCCESS' && $response_arr['result_code'] === 'SUCCESS') {
            /*将prepay_id拼接好进行签名*/
            $package = "prepay_id=".$response_arr['prepay_id'];

            /*小程序支付签名参数*/
            $payment_sign_params = array(
                'appId'     => $response_arr['appid'],
                'timeStamp' => (string) time(),
                'nonceStr'  => self::getNonceStr(),
                'package'   => $package,
                'signType'  => 'MD5',
            );

            /*支付签名*/
            $pay_sign = self::MakeSign($payment_sign_params);

            /*paySign参数*/
            $payment_sign_params['paySign'] = $pay_sign;

            return $this->formatResponse(0, '支付签名成功', $payment_sign_params);
        } else {
            return $this->formatResponse(1, '支付签名失败', $response_arr);
        }
    }

    /**
     * 产生随机字符串，不长于32位
     *
     * @param int $length
     * @return 产生的随机字符串
     */
    public static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }

        return $str;
    }

    /**
     * 商户订单号
     *
     * @return string
     */
    public static function getOutTradeNo() {
        $str = 'D'.date("YmdHis").mt_rand(0000,9999);

        return $str;
    }

    /**
     * 生成签名
     *
     * @param $config  参加签名参数
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    public function MakeSign($config)
    {
        $key = config('wx.key');

        //签名步骤一：按字典序排序参数
        ksort($config);
        $string = $this->ToUrlParams($config);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //die;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     *
     * @param $config
     * @return string
     */
    public function ToUrlParams($config)
    {
        $buff = "";
        foreach ($config as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 数组转换xml格式
     *
     * @param $data
     * @return string
     */
    public function ToXml($data)
    {
        $xml = "<xml>";
        foreach ($data as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * 将XML转为array
     *
     * @param $xml
     * @return mixed
     */
    public function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 以post方式提交xml到对应的接口url
     *
     * @param $xml 需要post的xml数据
     * @param $url url
     * @param int $second url执行超时时间，默认30s
     * @return bool|string
     */
    private static function postXmlCurl($xml, $url, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);

        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
        }
    }
}