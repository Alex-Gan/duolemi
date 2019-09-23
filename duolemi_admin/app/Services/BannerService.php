<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-12
 * Time: 16:33
 */
namespace App\Services;

use App\Models\Banner;
use App\Models\ExperienceCourse;
use App\Models\FranchiseCourse;

class BannerService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * BannerService constructor.
     * @param Banner $banner
     */
    public function __construct(Banner $banner)
    {
        $this->model = $banner;
    }

    /**
     * 提现列表
     *
     * @param $params
     * @return array
     */
    public function getUserList($params)
    {
        $page = isset($params['page'])?intval($params['page']):1;
        $limit = isset($params['limit'])?intval($params['limit']):10;
        $offset = $page > 0 ? ($page-1)*$limit : 0;

        $wheres = [
            ['column' => 'is_delete', 'value' => 0, 'operator' => '=']
        ];

        //排序
        $sorts = [
            ['column' => 'sort', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        $result = [
            'data'    => $data,
            '_count'  => $this->getCount($wheres),
            '_limit'  => $limit,
            '_curr'   => $page,
            '_query'  => http_build_query($params),
            '_params' => $params
        ];

        return $result;
    }

    /**
     * 添加轮播图
     *
     * @param $params
     * @return array
     */
    public function create($params)
    {
        $type = !empty($params['type']) ? intval($params['type']) : 0;

        if ($type == 0) { //无
            $type_relation_id = 0;
        } else if ($type == 1) { //体验课程
            $type_relation_id = intval($params['experience_course']);
        } else if ($type == 2) { //加盟课程
            $type_relation_id = intval($params['franchise_course']);
        } else {
            $type_relation_id = 0;
        }

        $data = [
            'image'      => $params['image'],
            'type'       => intval($params['type']),
            'type_relation_id' => $type_relation_id,
            'sort'       => intval($params['sort']),
            'created_at' => date("Y-m-d H:i:s", time())
        ];

        $res = Banner::create($data);
        if ($res) {
            return [
                'code' => 0,
                'msg'  => '添加成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '添加失败'
            ];
        }
    }

    /**
     * 删除
     *
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        $res = Banner::where('id', $id)->update(['is_delete' => 1]);

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '删除成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '删除失败'
            ];
        }
    }

    /**
     * 加盟课、体验课导航
     *
     * @return array
     */
    public function getNavigationPage()
    {
        $experience_course = ExperienceCourse::select(['id', 'name'])->where('status', 1)->where('is_delete', 0)->get();
        $franchise_course = FranchiseCourse::select(['id', 'title'])->where('is_delete', 0)->get();

        $data = [
            'experience_course' => $experience_course,
            'franchise_course' => $franchise_course
        ];

        return $data;
    }
}