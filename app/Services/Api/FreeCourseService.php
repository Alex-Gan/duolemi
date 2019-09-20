<?php
namespace App\Services\Api;

use App\Models\ExperienceCourse;
use App\Models\Member;
use App\Models\NavigationSettings;
use App\Models\PurchaseHistory;
use App\Models\WxPayLog;

class FreeCourseService extends BaseService
{
    /**
     * 体验课详情
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCourseDetail($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';

        if ($id == '') {
            return $this->formatResponse(404, '体验课id为空');
        }

        /*体验课信息*/
        $experience_course = ExperienceCourse::select(['id', 'name as title', 'experience_price as freePrice', 'original_price as price', 'banner', 'details as content', 'experience_price as totalCount'])
            ->where('id', $id)
            ->where('is_delete', 0)
            ->first();

        if (empty($experience_course)) {
            return $this->formatResponse(404, '体验课信息不存在');
        }

        /*banner图格式转化*/
        $banner_arr = json_decode($experience_course->banner, true);
        $banner_arr_new = [];
        foreach ($banner_arr as $banner) {
            $banner_arr_new[] = $banner['img'];
        }
        $experience_course->banner = $banner_arr_new;

        /*将详情简介内容格式化*/
        $experience_course->content = htmlspecialchars_decode($experience_course->content);

        /*客服电话*/
        $contact = NavigationSettings::where('id', 4)->value('type_relation');
        $experience_course->contact = !empty($contact) ? $contact : '40012345678';

        return $this->formatResponse(0, 'ok', $experience_course);
    }

    /**
     * 支付体验课
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCoursePay($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if ($id == '') {
            return $this->formatResponse(404, '验课id为空');
        }

        /******************数据效验begin************************/
        $validator = \Validator::make($data, [
            'name' => 'required',
            'mobile' => 'required|regex:/^1[3-9]{1}\d{9}$/',
        ],
        [
            'name.required' => '姓名不能为空',
            'mobile.required' => '手机号码不能为空',
            'mobile.regex' => '手机号码不正确',
        ]);

        if ($validator->fails()) {
            $msg = $validator->getMessageBag()->first();
            return $this->formatResponse(404, $msg);
        }
        /******************数据效验end************************/

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        /*获取体验课信息*/
        $experience_course_data = ExperienceCourse::where('id', $id)
            ->where('is_delete', 0)
            ->where('status', 1)
            ->first();

        if (empty($experience_course_data)) {
            return $this->formatResponse(404, '该体验课不存在或已下架');
        }


        //测试数据
        $openid = 'oeGsr5JJzSBAmtIZZMUvI9US095E';


        /*附加信息*/
        $attach = [
            'openid' => $openid,
            'experience_course_id' => $id,
            'name' => $data['name'],
            'mobile' => $data['mobile']
        ];

        /*支付金额*/
        $total_fee = $experience_course_data['experience_price'];

        /*微信支付记录日志*/
        $wx_pay_log = WxPayLog::create(['attach' => json_encode($attach)]);

        $wx_pay_sign_data = [
            'openid' => $openid,
            'total_fee' => $total_fee,
            'attach' => $wx_pay_log->id,
        ];

        /*进行支付*/
        $wx_pay_data = (new WxPayService())->WxPay($wx_pay_sign_data);

        return $wx_pay_data;
    }

    /**
     * 支付结果通知
     */
    public function notifyUrl()
    {
        $xmlData = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $response_arr = json_decode(json_encode(simplexml_load_string($xmlData, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        \Log::info('response_arr:'.json_encode($response_arr));

        if ($response_arr['return_code'] === 'SUCCESS' && $response_arr['result_code'] === 'SUCCESS') {

            $pay_sign = $response_arr['sign']; //支付结果返回的sign
            unset($response_arr['sign']);

            //生成sign
            $sign = (new WxPayService())->MakeSign($response_arr);
            if ($pay_sign == $sign) {

                /*附件中的订单信息*/
                $wx_pay_log_id = $response_arr['attach'];
                $attach = WxPayLog::where('id', $wx_pay_log_id)->value('attach');
                $attach_arr = json_decode($attach, true);

                /*用户信息*/
                $member = Member::where('openid', $attach_arr['openid'])->first();

                /*订单信息*/
                $create_data = [
                    'member_id' => $member->id,
                    'experience_course_id' => $attach_arr['experience_course_id'],
                    'avatar' => $member->avatar,
                    'nickname' => $member->nickname,
                    'name' => $attach_arr['name'],
                    'mobile' => $attach_arr['mobile'],
                    'created_at' => date("Y-m-d H:i:s", time())
                ];

                /*创建购买体验课记录*/
                $res = PurchaseHistory::create($create_data);

                if ($res) {
                    /*通知微信支付成功*/
                    $response_data = array(
                        'return_code' => 'SUCCESS',
                        'return_msg'  => 'OK'
                    );

                    $response_xml_data = $this->ToXml($response_data);
                    echo $response_xml_data;exit;
                }
            }
        }

        /*通知微信支付成功*/
        $response_data = array(
            'return_code' => 'SUCCESS',
            'return_msg'  => 'OK'
        );

        $response_xml_data = $this->ToXml($response_data);
        echo $response_xml_data;exit;
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

    public function test()
    {
        $attach = 3;
        /*微信支付记录日志*/
        $wx_pay_log = WxPayLog::create(['attach' => json_encode($attach)]);
        dd($wx_pay_log->id);

        $aa = serialize(['name'=>'甘超胜', 'mobile' => '15000319670']);
        dd(unserialize($aa));

        $response_json  = '{"appid":"wxc56da5478d843331","bank_type":"CFT","cash_fee":"1","fee_type":"CNY","is_subscribe":"N","mch_id":"1378450802","nonce_str":"dtrtwybnehgjphl7pdbrrkyztc2ysg9c","openid":"oeGsr5JJzSBAmtIZZMUvI9US095E","out_trade_no":"D201909192007513855","result_code":"SUCCESS","return_code":"SUCCESS","sign":"D4CEA4FE400118F55E4D35738C3726E2","time_end":"20190919200802","total_fee":"1","trade_type":"JSAPI","transaction_id":"4200000385201909191496263592"}';

        $response_arr = json_decode($response_json, true);

        if ($response_arr['return_code'] === 'SUCCESS' && $response_arr['result_code'] === 'SUCCESS') {

            //unset($response_arr['sign']);
            $response_sign_arr = [
                'appid' => $response_arr['appid'],
                //'attach' => $response_arr['attach'],
                'bank_type' => $response_arr['bank_type'],
                'cash_fee' => $response_arr['cash_fee'],
                'fee_type' => $response_arr['fee_type'],
                'is_subscribe' => $response_arr['is_subscribe'],
                'mch_id' => $response_arr['mch_id'],
                'nonce_str' => $response_arr['nonce_str'],
                'openid' => $response_arr['openid'],
                'out_trade_no' => $response_arr['out_trade_no'],
                'result_code' => $response_arr['result_code'],
                'return_code' => $response_arr['return_code'],
                //'sign' => $response_arr['sign'],
                'time_end' => $response_arr['time_end'],
                'trade_type' => $response_arr['trade_type'],
                'total_fee' => $response_arr['total_fee'],
                'transaction_id' => $response_arr['transaction_id'],
            ];

            //生成sign
            $sign = (new WxPayService())->MakeSign($response_sign_arr); //D4CEA4FE400118F55E4D35738C3726E2 D4CEA4FE400118F55E4D35738C3726E2
            dd($sign);
            echo $sign;
            echo "<hr/>";
            echo $response_arr['sign'];
            die;
            if ($response_arr['sign'] == $sign) {

                /*附件中的订单信息*/
                $attach_arr = json_decode($response_arr['attach'], true);

                dd($attach_arr);

                /*用户信息*/
                $member = Member::where('openid', $attach_arr['openid'])->first();

                /*订单信息*/
                $create_data = [
                    'member_id' => $member->id,
                    'experience_course_id' => $attach_arr['experience_course_id'],
                    'avatar' => $member->avatar,
                    'nickname' => $member->nickname,
                    'name' => $attach_arr['name'],
                    'mobile' => $attach_arr['mobile'],
                    'created_at' => date("Y-m-d H:i:s", time())
                ];

                /*创建购买体验课记录*/
                $res = PurchaseHistory::create($create_data);

                if ($res) {
                    /*通知微信支付成功*/
                    $response_data = array(
                        'return_code' => 'SUCCESS',
                        'return_msg'  => 'OK'
                    );

                    $response_xml_data = $this->ToXml($response_data);
                    echo $response_xml_data;exit;
                }
            }
        }
    }
}