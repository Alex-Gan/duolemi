<?php
namespace App\Services\Api;

use App\Models\ExperienceCourse;
use App\Models\Member;
use App\Models\NavigationSettings;
use App\Models\PurchaseHistory;

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

        $wx_pay_sign_data = [
            'openid' => $openid,
            'total_fee' => $experience_course_data['experience_price'],
            'attach' => json_encode($attach),
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

            $pay_sign = $response_arr['pay_sign']; //支付结果返回的sign
            unset($response_arr['pay_sign']);

            //生成sign
            $sign = (new WxPayService())->MakeSign($response_arr);
            if ($pay_sign == $sign) {

                /*附件中的订单信息*/
                $attach_arr = json_decode($response_arr['attach'], true);

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
}