<?php
namespace App\Services\Api;

use App\Models\ExperienceCourse;
use App\Models\ExperienceProgress;
use App\Models\Guider;
use App\Models\Member;
use App\Models\NavigationSettings;
use App\Models\PurchaseHistory;
use App\Models\WxPayLog;
use Illuminate\Support\Facades\DB;

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

            /*支付结果返回的sign*/
            $pay_sign = $response_arr['sign'];
            unset($response_arr['sign']);

            /*生成sign*/
            $sign = (new WxPayService())->MakeSign($response_arr);
            if ($pay_sign == $sign) {

                try {
                    /*开启事物*/
                    DB::beginTransaction();

                    /*附件中的订单信息*/
                    $wx_pay_log_id = $response_arr['attach'];
                    $wx_pay_res = WxPayLog::where('id', $wx_pay_log_id)->update(['status' => 2, 'response_params' => json_encode($response_arr)]);
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

                    /*体验课进度明细*/
                    /*
                    $res1 = ExperienceProgress::create([
                        'member_id' => $member->id,
                        'experience_course_id' => $attach_arr['experience_course_id'],
                        'purchase_history_id' => $res->id,
                        'status' => 1,
                        'remark' => '已购买',
                        'processing_at' => date("Y-m-d H:i:s", time()),
                        'created_at' => date("Y-m-d H:i:s", time())
                    ]);
                    */

                    if ($wx_pay_res && $res) {
                        /*提交事务*/
                        DB::commit();

                        /*通知微信支付成功*/
                        $response_data = array(
                            'return_code' => 'SUCCESS',
                            'return_msg'  => 'OK'
                        );

                        $response_xml_data = $this->ToXml($response_data);
                        echo $response_xml_data;exit;
                    }

                } catch (\Exception $e) {
                    /*回滚事务*/
                    DB::rollBack();

                    /*记录错误日志*/
                    \Log::info("微信支付逻辑处理错误:".$e->getMessage());
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

    /**
     * 我的体验课列表
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFreeCourse($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        $my_free_course = PurchaseHistory::select(['id', 'experience_course_id' ,'status', 'created_at'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($my_free_course as &$free_course) {
            $experience_course = $free_course->getExperienceCourse;
            $free_course->experience_course_name = $experience_course->name;
            /*banner图格式转化*/
            $banner_arr = json_decode($experience_course->banner, true);
            $free_course->img = $banner_arr[0]['img'];

            if ($free_course->status == 1) {
                $free_course->status = '已付款';
            } else if ($free_course->status == 2) {
                $free_course->status = '已面试';
            } else if ($free_course->status == 3) {
                $free_course->status = '正在体验';
            } else if ($free_course->status == 4) {
                $free_course->status = '体验完成';
            }

            unset($free_course['experience_course_id']);
            unset($free_course['getExperienceCourse']);
        }

        return $this->formatResponse(0, 'ok', $my_free_course);
    }

    /**
     * 我的体验课详情
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFreeCourseDetail($data)
    {
        $id = !empty($data['id']) ? intval($data['id']) : '';
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if ($id == '') {
            return $this->formatResponse(404, '体验课id为空');
        }

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        $purchase_history = PurchaseHistory::select(['id', 'experience_course_id', 'member_id', 'name', 'mobile'])->find($id);
        if (empty($purchase_history)) {
            return $this->formatResponse(404, '体验课详情信息不存在');
        }

        $purchase_history->course_name = ExperienceCourse::where('id', $purchase_history->experience_course_id)->value('name');

        /*检验当前体验课是否为自己的*/
        if ($purchase_history->member_id != $member->id) {
            return $this->formatResponse(403, '体验课详情信息不存在');
        }

        /*获取体验进度信息*/
        $experience_progress = ExperienceProgress::where('member_id', $purchase_history->member_id)
            ->where('experience_course_id', $purchase_history->experience_course_id)
            ->where('purchase_history_id', $purchase_history->id)
            ->orderBy('processing_at', 'desc')
            ->get();

        $purchase_history->experience_progress = $experience_progress;

        /*删除多余信息数据*/
        unset($purchase_history['experience_course_id']);
        unset($purchase_history['member_id']);

        return $this->formatResponse(0, 'ok', $purchase_history);
    }

    /**
     * 生成体验课二维码
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCourseCode($data)
    {
        $id = intval($data['id']); //体验课id
        $openid = $data['openid']; //用户身份

        /*效验体验课id*/
        if (empty($id)) {
            return $this->formatResponse(404, '体验课id为空');
        }

        /*效验身份是否存在*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(404, '会员信息不存在');
        }

        /*开始生成海报*/
        $free_course_code = $this->makeFreeCourseCode($member);

        if ($free_course_code['code'] == 0) {
            /*创建推客*/
            $this->createGuider($member->id);

            return $this->formatResponse(0, 'ok', $free_course_code['data']);
        }
    }

    /**
     * 生成海报
     *
     * @param $member
     * @return array
     */
    private function makeFreeCourseCode($member)
    {
        try {
            $bg = public_path()."/images/experience_course_template.jpg";
            $file_code = public_path()."/images/code.jpg";
            $img_bg = imagecreatefromjpeg($bg); //背景图
            $img_code = imagecreatefromjpeg($file_code);//二维码
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

            $width_code = 258;
            $height_code = 258;

            /*开始合成*/
            imagecopymerge($im, $img_code, $width - $width_code - 500, $height - $height_code - 100, 0, 0, $width_code, $height_code, 100);

            /*保存路径*/
            $relative_path = '/images/user_experience_course/experience_course_user_'.$member->id.'.png';
            imagepng($im, public_path($relative_path));

            return ['code' => 0, 'msg' => 'ok', 'data' => ['code' => env('APP_URL').$relative_path]];
        } catch (\Exception $exception) {
            return ['code' => 1, 'msg' => $exception->getMessage()];
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
}