<?php
namespace App\Services\Api;

use App\Models\Banner;
use App\Models\Customer;
use App\Models\ExperienceCourse;
use App\Models\FranchiseApply;
use App\Models\FranchiseCourse;
use App\Models\FranchiseCourseProgress;
use App\Models\Guider;
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
        $franchise_apply_has = FranchiseApply::where('member_id', $member->id)->exists();

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
            /*推广佣金*/
            $s_mid = !empty($data['s_mid']) ? intval($data['s_mid']) : '';

            if (!empty($s_mid)) {
                $super_member = Member::where('id', $s_mid)->first();
                if (!empty($super_member)) {
                    $guider = Guider::where('member_id', $super_member->id)->first();
                    if (!empty($guider)) {
                        /*加盟课返利佣金*/
                        $expect_comission = FranchiseCourse::where('id', $res->franchise_course_id)->value('rebate_commission');
                        Guider::where('id', $guider->id)->increment('team_join_size');
                        Guider::where('id', $guider->id)->increment('expect_comission', $expect_comission);

                        /*课程名称*/
                        $courseName = FranchiseCourse::where('id', $res->franchise_course_id)->value('title');

                        /*我的客户*/
                        Customer::create([
                            'member_id' => $member->id,
                            'superior_member_id' => $super_member->id,
                            'faceImg' => $member->avatar,
                            'name' => $member->nickname,
                            'mobile' => $member->mobile,
                            'date' => date("Y-m-d H:i:s", time()),
                            'money' => $expect_comission,
                            'type' => 2,
                            'courseName' => $courseName,
                            'source_order_id' => $res->id,
                            'created_at' => date("Y-m-d H:i:s", time())
                        ]);
                    }
                }
            }

            /*加盟进度 to-do: 加盟进度*/
            /*
            FranchiseCourseProgress::create([
                'member_id' => $member->id,
                'franchise_course_id' => $id,
                'franchise_apply_id' => $res->id,
                'remark' => !empty($data['remark']) ? $data['remark'] : '',
                'processing_at' => date("Y-m-d H:i:s", time()),
                'created_at' => date("Y-m-d H:i:s", time())
            ]);
            */

            return $this->formatResponse(0, '申请加盟成功');
        } else {
            return $this->formatResponse(500, '申请加盟失败');
        }
    }

    /**
     * 我的加盟详情
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function myLeagueDetail($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        /*我的加盟信息*/
        $franchise_apply = FranchiseApply::select(['id', 'name', 'mobile'])
            ->where('member_id', $member->id)
            ->first();

        if (empty($franchise_apply)) {
            return $this->formatResponse(404, '暂无加盟信息');
        }

        /*加盟进度*/
        $franchise_course_progress = FranchiseCourseProgress::where('member_id', $member->id)
            ->where('franchise_apply_id', $franchise_apply->id)
            ->orderBy('processing_at', 'desc')
            ->get();

        $franchise_apply->join_progress = $franchise_course_progress;

        return $this->formatResponse(0, 'ok', $franchise_apply);
    }

    /**
     * 生成加盟课二维码
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagueCode($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if ($id == '') {
            return $this->formatResponse(404, '加盟课id为空');
        }

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        /*开始生成海报*/
        return $this->makeLeagueCode($member, $id);

        /*
        if ($free_course_code['code'] == 0) {
            //创建推客
            $this->createGuider($member->id);

            return $this->formatResponse(0, 'ok', $free_course_code['data']);
        }
        */
    }

    /**
     * 生成海报
     *
     * @param $member
     * @return array
     */
    private function makeLeagueCode($member, $id)
    {
        try {
            $bg = public_path()."/images1/experience_course_template.jpg";
            $img_bg = imagecreatefromjpeg($bg); //背景图
            $img_code = $this->getwxacodeunlimit($id, $member->id);//二维码
            $user_head = imagecreatefromjpeg($member->avatar); //用户头像

            //获取背景图的宽高
            $width = imagesx($img_bg);
            $height = imagesy($img_bg);

            $im = imagecreatetruecolor($width, $height);  //创建一张与背景图同样大小的真彩色图像
            imagecopy($im, $img_bg, 0, 0, 0, 0, $width, $height);

            //添加头像
            /*获取背景图的宽高*/
            $head_width = imagesx($user_head);
            $head_height = imagesy($user_head);
            imagecopymerge($im, $user_head, 100, 500, 0, 0, $head_width, $head_height, 100);

            // 字体文件
            $font_file = public_path('/fonts/PINGFANG BOLD_0.TTF');
            $font_size = 60;
            $title = "我是".$member->nickname;
            $title1 = "我为多乐米音乐学院代言!";

            /*添加文字描述*/
            $color = imagecolorallocate($im, 256, 256, 256); // 灰色
            imagettftext($im, $font_size, 0, 300, 550, $color, $font_file, $title);
            imagettftext($im, $font_size, 0, 300, 680, $color, $font_file, $title1);

            $width_code = 300;
            $height_code = 300;

            /*开始合成*/
            imagecopymerge($im, $img_code, $width - $width_code - 500, $height - $height_code - 100, 0, 0, $width_code, $height_code, 100);

            /*保存路径*/
            $relative_path = '/images1/user_experience_course/league_code_user_'.$member->id.'.png';
            imagepng($im, public_path($relative_path));

            /*创建推客*/
            $this->createGuider($member->id);

            return $this->formatResponse(0, 'ok', ['code' => env('APP_URL').$relative_path]);
        } catch (\Exception $exception) {
            return $this->formatResponse(1, $exception->getMessage());
        }
    }

    /**
     * 成为推客
     *
     * @param $member_id
     * @return bool
     */
    private function createGuider($member_id)
    {
        $guider_has = Guider::where('member_id', $member_id)->exists();

        if (!$guider_has) {
            $member = Member::find($member_id);

            $res = Guider::create([
                'member_id' => $member_id,
                'nickname' => $member->nickname,
                'mobile' => $member->mobile,
                'add_guider_at' => date("Y-m-d H:i:s", time()),
                'created_at' => date("Y-m-d H:i:s", time())
            ]);

            if ($res) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 获取小程序码(适用于需要的码数量极多的业务场景)
     *
     * @param $id
     * @param $member_id
     * @return false|resource
     * @throws \Exception
     */
    public function getwxacodeunlimit($id, $member_id)
    {
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$access_token;
        $data = [
            'scene' => 's_mid='.$member_id.'&id='.$id,
            'page' => 'pages/league-detail/index',
            'width' => 300
        ];

        $response = curl_request($url, "POST", json_encode($data));

        $images = imagecreatefromstring($response);

        return $images;
    }
}