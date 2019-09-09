<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\FranchiseCourse;

class FranchiseCourseService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * FranchiseCourseService constructor.
     * @param FranchiseCourse $franchiseCourse
     */
    public function __construct(FranchiseCourse $franchiseCourse)
    {
        $this->model = $franchiseCourse;
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
        $title = isset($params['title']) ? $params['title'] : '';

        $wheres = [
            ['column' => 'is_delete', 'value' => 0, 'operator' => '=']
        ];

        if (!empty($title)) {
            $wheres[] = ['column' => 'title', 'value' => $title, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'created_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        foreach ($data as &$item) {
            $banner_arr = json_decode($item['banner'], true);
            $item['banner'] = $banner_arr[0]['img']; //获取第一张图片

            //标题、副标题内容截取
            $item['title'] = $this->substrEllipsis($item['title'], 15);
            $item['subtitle'] = $this->substrEllipsis($item['subtitle'], 20);
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
     * 添加加盟课
     *
     * @param $params
     * @return array
     */
    public function add($params)
    {
        $data = [
            'title'      => $params['title'],
            'subtitle'   => $params['subtitle'],
            'banner'     => json_encode($params['banner']),
            'details'    => htmlspecialchars($params['details']),
            'created_at' => date("Y-m-d H:i:s", time())
        ];

        $res = FranchiseCourse::create($data);
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
        $res = FranchiseCourse::where('id', $id)->update(['is_delete' => 1]);

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
        $franchise_course_data = FranchiseCourse::where('id', $id)->first();

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
        $franchise_course_data = FranchiseCourse::find($id);
        $franchise_course_data->title = $params['title'];
        $franchise_course_data->subtitle = $params['subtitle'];
        $franchise_course_data->banner = json_encode($params['banner']);
        $franchise_course_data->details = htmlspecialchars($params['details']);
        $res = $franchise_course_data->save();

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