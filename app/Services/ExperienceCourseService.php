<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\ExperienceCourse;

class ExperienceCourseService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * ExperienceCourseService constructor.
     * @param ExperienceCourse $experienceCourse
     */
    public function __construct(ExperienceCourse $experienceCourse)
    {
        $this->model = $experienceCourse;
    }

    /**
     * 权限管理列表
     *
     * @param $params
     * @return array
     */
    public function getUserList($params)
    {
        $page = isset($params['page'])?intval($params['page']):1;
        $limit = isset($params['limit'])?intval($params['limit']):10;
        $offset = $page > 0 ? ($page-1)*$limit : 0;

        //搜索条件
        $name = isset($params['name']) ? $params['name'] : '';

        $wheres = [
            ['column' => 'is_delete', 'value' => 0, 'operator' => '=']
        ];

        if (!empty($name)) {
            $wheres[] = ['column' => 'name', 'value' => $name, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'sort', 'direction' => 'desc'],
            ['column' => 'created_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        foreach ($data as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            $item['banner'] = $banner_arr[0]['img']; //获取第一张图片

            //课程名称进行截取
            $item['name'] = $this->substrEllipsis($item['name'], 15);

            //上下架状态
            $item['status_text'] = $item['status'] == 1 ? '上架' : '下架';
        }

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
     * 添加体验课程
     *
     * @param $params
     * @return array
     */
    public function add($params)
    {
        $data = [
            'name'              => $params['name'],
            'introduction'      => $params['introduction'],
            'banner'            => json_encode($params['banner']),
            'details'           => htmlspecialchars($params['details']),
            'sort'              => intval($params['sort']),
            'original_price'    => $params['original_price'],
            'experience_price'  => $params['experience_price'],
            'rebate_commission' => $params['rebate_commission'],
            'status'            => intval($params['status']),
            'created_at'        => date("Y-m-d H:i:s", time())
        ];

        $res = ExperienceCourse::create($data);
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
        $res = ExperienceCourse::where('id', $id)->update(['is_delete' => 1]);

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
     * 查询编辑页面内容
     *
     * @param $id
     * @return mixed
     */
    public function editView($id)
    {
        $franchise_course_data = ExperienceCourse::where('id', $id)->first();

        //将banner转化格式
        $franchise_course_data['banner'] = json_decode($franchise_course_data['banner'], true);

        $franchise_course_data['banner_json'] = json_encode($franchise_course_data['banner']);

        //将详情介绍转html格式
        $franchise_course_data['details'] = stripslashes($franchise_course_data['details']);
        return $franchise_course_data;
    }

    /**
     * 修改
     *
     * @param $id
     * @param $params
     * @return array
     */
    public function editPut($id, $params)
    {
        $experience_course_data = ExperienceCourse::find($id);

        $experience_course_data->name = $params['name'];
        $experience_course_data->introduction = $params['introduction'];
        $experience_course_data->banner = json_encode($params['banner']);
        $experience_course_data->details = htmlspecialchars($params['details']);
        $experience_course_data->sort = $params['sort'];
        $experience_course_data->original_price = $params['original_price'];
        $experience_course_data->experience_price = $params['experience_price'];
        $experience_course_data->rebate_commission = $params['rebate_commission'];
        $experience_course_data->status = $params['status'];
        $res = $experience_course_data->save();

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '修改成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '修改失败'
            ];
        }
    }

    /**
     * 更改体验课程上下架状态
     *
     * @param $id
     * @return array
     */
    public function changeStatus($id)
    {
        $experience_course_data = $this->model::find(intval($id));

        if ($experience_course_data->status == 1) {
            $experience_course_data->status = 2;
        } else {
            $experience_course_data->status = 1;
        }
        $res = $experience_course_data->save();

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '更改状态成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '更改状态失败'
            ];
        }
    }

    /**
     * 字符串截取
     *
     * @param $str
     * @param $length
     * @return string
     */
    private function substrEllipsis($str, $length)
    {
        $str_len = mb_strlen($str, 'utf8');
        if ($str_len > $length) {
            return mb_substr($str, 0, $length, 'utf-8').'...';
        } else {
            return $str;
        }
    }
}