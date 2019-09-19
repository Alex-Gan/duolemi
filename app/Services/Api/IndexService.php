<?php
namespace App\Services\Api;

use App\Models\Banner;
use App\Models\ExperienceCourse;
use App\Models\FranchiseCourse;
use App\Models\NavigationSettings;

class IndexService extends BaseService
{
    /**
     * 首页接口
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndexData()
    {
        $data = [
            'banner' => $this->getBannerData(),
            'leagueCourse' => $this->getLeagueCourse(),
            'navList' => $this->getNavList(),
            'freeCourse' => $this->getFreeCourse(),
        ];

        return $this->formatResponse(0, 'ok', $data);
    }

    /**
     * banner图
     *
     * @return mixed
     */
    private function getBannerData()
    {
       return Banner::select(['image as img', 'type', 'type_relation_id as id'])
           ->where('is_delete', 0)
           ->orderBy('sort', 'asc')
           ->get();
    }

    /**
     * 导航设置数据
     *
     * @return mixed
     */
    private function getNavList()
    {
        return NavigationSettings::select(['name', 'icon as img', 'type', 'type_relation as content'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * 加盟课程
     *
     * @return mixed
     */
    private function getLeagueCourse()
    {
        $franchise_course = FranchiseCourse::select(['id', 'banner', 'subtitle as content'])
            ->where('is_delete', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($franchise_course as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            unset($item['banner']);
            $item['img'] = $banner_arr[0]['img'];
        }

        return $franchise_course;
    }

    /**
     * 体验课程
     *
     * @return mixed
     */
    private function getFreeCourse()
    {
        $experience_course = ExperienceCourse::select(['id', 'banner', 'introduction as content'])
            ->where('is_delete', 0)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($experience_course as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            unset($item['banner']);
            $item['img'] = $banner_arr[0]['img'];
        }

        return $experience_course;
    }

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
}