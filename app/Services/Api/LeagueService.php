<?php
namespace App\Services\Api;

use App\Models\Banner;
use App\Models\ExperienceCourse;
use App\Models\FranchiseApply;
use App\Models\FranchiseCourse;
use App\Models\Member;
use App\Models\NavigationSettings;

class LeagueService extends BaseService
{
    /**
     * 加盟课详情
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagueDetail($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';

        if ($id == '') {
            return $this->formatResponse(404, '加盟课id为空');
        }

        /*加盟课信息*/
        $franchise_course = FranchiseCourse::select(['id', 'title', 'subtitle as subTitle', 'banner', 'details as content'])
            ->where('id', $id)
            ->where('is_delete', 0)
            ->first();

        if (empty($franchise_course)) {
            return $this->formatResponse(404, '加盟课信息不存在');
        }

        /*banner图格式转化*/
        $banner_arr = json_decode($franchise_course->banner, true);
        $banner_arr_new = [];
        foreach ($banner_arr as $banner) {
            $banner_arr_new[] = $banner['img'];
        }
        $franchise_course->banner = $banner_arr_new;

        /*将详情简介内容格式化*/
        $franchise_course->content = htmlspecialchars_decode($franchise_course->content);

        /*客服电话*/
        $contact = NavigationSettings::where('id', 4)->value('type_relation');
        $franchise_course->contact = !empty($contact) ? $contact : '40012345678';

        return $this->formatResponse(0, 'ok', $franchise_course);
    }

    /**
     * 申请加盟
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagueApply($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        /******************数据效验begin************************/
        $validator = \Validator::make($data, [
            'id' => 'required',
            'openid' => 'required',
            'mobile' => 'required|regex:/^1[3-9]{1}\d{9}$/',
            'name' => 'required',
            'sex' => 'required',
            'age' => 'required',
            'region' => 'required'
        ],
        [
             'id.required' => '加盟课id不能为空',
             'openid.required' => 'openid不能为空',
             'mobile.required' => '手机号码不能为空',
             'mobile.regex' => '手机号码不正确',
             'name.required' => '姓名不能为空',
             'sex.required' => '性别不能为空',
             'age.required' => '年龄不能为空',
             'region.required' => '地区不能为空',
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

        /*效验是否申请过*/
        $franchise_apply_has = FranchiseApply::where('member_id', $member->id)
            ->where('franchise_course_id', $id)
            ->exists();

        if ($franchise_apply_has) {
            return $this->formatResponse(404, '已申请提交过，无需重复申请');
        }

        $save_data = [
            'member_id' => $member->id,
            'franchise_course_id' => $id,
            'name' => $data['name'],
            'mobile' => $data['mobile'],
            'sex' => $data['sex'],
            'age' => $data['age'],
            'province' => $data['region'][0],
            'city' => $data['region'][1],
            'area' => $data['region'][2],
            'apple_at' => date('Y-m-d H:i:s', time()),
            'created_at' => date('Y-m-d H:i:s', time())
        ];

        /*备注不为空*/
        if (!empty($data['remark'])) {
            $save_data['remark'] = $data['remark'];
        }

        /*存入数据库*/
        $res = FranchiseApply::create($save_data);

        if ($res) {
            return $this->formatResponse(0, '申请加盟成功');
        } else {
            return $this->formatResponse(500, '申请加盟失败');
        }
    }
}