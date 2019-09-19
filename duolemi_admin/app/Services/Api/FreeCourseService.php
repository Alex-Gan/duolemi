<?php
namespace App\Services\Api;

use App\Models\ExperienceCourse;
use App\Models\Member;
use App\Models\NavigationSettings;

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

        /*
        //测试数据
        $openid = 'oeGsr5JJzSBAmtIZZMUvI9US095E';
        */

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
}