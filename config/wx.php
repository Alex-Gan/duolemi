<?php
return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 小程序app_id
    'app_id' => 'wx6aab2e266059a588', //wxdf281d0380146243
    // 小程序app_secret
    'app_secret' => '6904c80daac1a6e691b05b01a9057cfe', //2bbf3576322c808644aad86fb1cf3dc6

    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    'expires_in' => 7200,

    // 微信支付参数
    'mch_id' => '1235897302', //微信支付分配的商户号
    'key' => 'ningboduolemiyingyuewenhuachuanb', //支付密钥
];