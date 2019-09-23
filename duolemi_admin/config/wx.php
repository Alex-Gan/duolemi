<?php
return [
    //  +---------------------------------
    //  微信相关配置
    //  +---------------------------------

    // 小程序app_id
    'app_id' => 'wxc56da5478d843331', //wxdf281d0380146243
    // 小程序app_secret
    'app_secret' => '3a2dda7a81eb536b5f7929ac820f7e43', //2bbf3576322c808644aad86fb1cf3dc6

    // 微信使用code换取用户openid及session_key的url地址
    'login_url' => "https://api.weixin.qq.com/sns/jscode2session?" .
        "appid=%s&secret=%s&js_code=%s&grant_type=authorization_code",

    // 微信获取access_token的url地址
    'access_token_url' => "https://api.weixin.qq.com/cgi-bin/token?" .
        "grant_type=client_credential&appid=%s&secret=%s",

    'expires_in' => 7200,

    // 微信支付参数
    'mch_id' => '1378450802', //微信支付分配的商户号
    'key' => 'junQDS5PWvFTJHHjTOePx50e0ntz7nxn', //支付密钥
];