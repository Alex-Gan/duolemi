<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\Guider;

class GuiderService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * GuiderService constructor.
     * @param Guider $guider
     */
    public function __construct(Guider $guider)
    {
        $this->model = $guider;
    }

    /**
     * 推广员列表
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
        $nickname   = isset($params['nickname']) ? $params['nickname'] : '';
        $mobile = isset($params['mobile']) ? $params['mobile'] : '';

        $wheres = [
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

        if (!empty($nickname)) {
            $wheres[] = ['column' => 'nickname', 'value' => $nickname, 'operator' => '='];
        }

        if (!empty($mobile)) {
            $wheres[] = ['column' => 'mobile', 'value' => $mobile, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'created_at', 'direction' => 'desc']
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
}