<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-15
 * Time: 08:56
 */
namespace App\Services;

use App\Services\ArticleService;
use App\Models\NavigationSettings;

class NavigationSettingsService extends BaseService
{
    protected $model;
    protected $service;

    /**
     * 构造方法
     *
     * NavigationSettingsService constructor.
     * @param NavigationSettings $navigationSettings
     * @param ArticleService $articleService
     */
    public function __construct(NavigationSettings $navigationSettings, ArticleService $articleService)
    {
        $this->model = $navigationSettings;
        $this->service = $articleService;
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
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

        //排序
        $sorts = [
            ['column' => 'created_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        foreach ($data as &$item) {
            if ($item['type'] == 1) {
                $item['status_text'] = '内容页';
                $item['type_relation'] = $this->service->getActicle($item['type_relation'])['title'];
            } else if ($item['type'] == 2) {
                $item['status_text'] = '小程序跳转';
            } else if($item['type'] == 3) {
                $item['status_text'] = '电话拨打';
            }
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
     * 导航设置编辑
     *
     * @param $id
     * @return mixed
     */
    public function getNavigationSettingsById($id)
    {
        $data = $this->model::find($id);
        /*内容页对应的数据*/
        $data->article_data = $this->service->getAllArticle();

        /*小程序对应的数据*/
        $data->small_program_page_data = $this->getSmallProgramPage();

        return $data;
    }

    /**
     * 编辑导航设置
     *
     * @param $id
     * @param $params_arr
     * @return array
     */
    public function editPut($id, $params_arr)
    {
        $type = intval($params_arr['type']);

        $model = $this->model::find($id);

        if ($type == 1) { //内容页
            $type_relation = intval($params_arr['content_val']);
        } else if ($type == 2) { //小程序页
            $type_relation = trim($params_arr['small_program_val']);
        } else if($type == 3) { //拨打电话
            $type_relation = $params_arr['call_phone_val'];
        } else {
            $type_relation = '';
        }

        $model->name = $params_arr['name'];
        $model->icon = $params_arr['icon'];
        $model->type = $type;
        $model->type_relation = $type_relation;
        $model->created_at = date("Y-m-d H:i:s", time());

        $res = $model->save();

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '编辑成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '编辑失败'
            ];
        }
    }

    /**
     * 小程序页面
     *
     * @return array
     */
    private function getSmallProgramPage()
    {
        return [
            [
                'title' => '首页',
                'path' => 'pages/index/index'
            ],
            [
                'title' => '我的客户',
                'path' => 'pages/user-customer/index'
            ],
            [
                'title' => '测试页面',
                'path' => 'pages/test/test'
            ],
            [
                'title' => '日志',
                'path' => 'pages/logs/logs'
            ],
        ];
    }
}