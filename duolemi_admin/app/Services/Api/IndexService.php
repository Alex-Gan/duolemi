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
        $franchise_course = FranchiseCourse::select(['id', 'banner', 'details as content'])
            ->where('is_delete', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($franchise_course as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            unset($item['banner']);
            $item['img'] = $banner_arr[0];
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
        $experience_course = ExperienceCourse::select(['id', 'banner', 'details as content'])
            ->where('is_delete', 0)
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($experience_course as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            unset($item['banner']);
            $item['img'] = $banner_arr[0];
        }

        return $experience_course;
    }
}