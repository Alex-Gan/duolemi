<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-15
 * Time: 08:56
 */
namespace App\Services;

use App\Models\Article;
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
                'title' => '首页1',
                'path' => 'pages/index1/index1'
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