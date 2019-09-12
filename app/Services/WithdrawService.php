<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 16:00
 */
namespace App\Services;

use App\Models\Withdraw;

class WithdrawService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * WithdrawService constructor.
     * @param Withdraw $withdraw
     */
    public function __construct(Withdraw $withdraw)
    {
        $this->model = $withdraw;
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

        //搜索条件
        $nickname   = isset($params['nickname']) ? $params['nickname'] : '';

        $wheres = [
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

        if (!empty($nickname)) {
            $wheres[] = ['column' => 'nickname', 'value' => $nickname, 'operator' => '='];
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