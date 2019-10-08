<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 16:00
 */
namespace App\Services;

use App\Models\Guider;
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
        $real_name = isset($params['real_name']) ? trim($params['real_name']) : '';

        $wheres = [
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

        if (!empty($real_name)) {
            $wheres[] = ['column' => 'real_name', 'value' => $real_name, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'status', 'direction' => 'asc'],
            ['column' => 'withdraw_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        /*状态*/
        foreach ($data as &$item) {
            if ($item['status'] == 1) {
                $item['status_text'] = '待审核';
            } else if ($item['status'] == 2) {
                $item['status_text'] = '已审核';
            } else if($item['status'] == 3) {
                $item['status_text'] = '已拒绝';
            } else {
                $item['status_text'] = '未知';
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
     * 提现详情
     *
     * @param $id
     * @return mixed
     */
    public function getDetail($id)
    {
        $data = $this->model::find($id);

        if ($data['status'] == 1) {
            $data['status_text'] = '待审核';
        } else if($data['status'] == 2) {
            $data['status_text'] = '已审核';
        }  else if($data['status'] == 3) {
            $data['status_text'] = '已拒绝';
        } else {
            $data['status_text'] = '未知';
        }

        return $data;
    }

    /**
     * 提现审核通过
     *
     * @param $id
     * @return array
     */
    public function auditPass($id)
    {
        $model = $this->model::find($id);
        $model->status = 2;
        if ($model->save()) {

            /*扣除用户余额*/
            $member_id = $model->member_id;
            Guider::where('member_id', $member_id)->increment('total_withdraw_comission', $model->apply_money); //累计提现
            Guider::where('member_id', $member_id)->decrement('comission', $model->apply_money); //佣金余额

            return [
                'code' => 0,
                'msg'  => '审核成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '审核失败'
            ];
        }
    }

    /**
     * 提现审核驳回
     *
     * @param $id
     * @return array
     */
    public function auditReject($id)
    {
        $model = $this->model::find($id);
        $model->status = 3;
        if ($model->save()) {
            return [
                'code' => 0,
                'msg'  => '审核成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '审核失败'
            ];
        }
    }
}