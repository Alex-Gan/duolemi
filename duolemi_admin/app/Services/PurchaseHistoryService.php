<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\Customer;
use App\Models\ExperienceCourse;
use App\Models\ExperienceProgress;
use App\Models\FranchiseApply;
use App\Models\FranchiseCourse;
use App\Models\Guider;
use App\Models\PurchaseHistory;

class PurchaseHistoryService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * PurchaseHistoryService constructor.
     * @param PurchaseHistory $purchaseHistory
     */
    public function __construct(PurchaseHistory $purchaseHistory)
    {
        $this->model = $purchaseHistory;
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
        $name   = isset($params['name']) ? $params['name'] : '';
        $mobile = isset($params['mobile']) ? $params['mobile'] : '';
        $status = isset($params['status']) ? $params['status'] : '';

        $wheres = [
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

        if (!empty($name)) {
            $wheres[] = ['column' => 'name', 'value' => $name, 'operator' => '='];
        }

        if (!empty($mobile)) {
            $wheres[] = ['column' => 'mobile', 'value' => $mobile, 'operator' => '='];
        }

        if (!empty($status)) {
            $wheres[] = ['column' => 'status', 'value' => $status, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'created_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        foreach ($data as &$item) {
            //状态
            switch ($item['status']) {
                case 1:
                    $status_text = "已购买";
                    break;
                case 2:
                    $status_text = "已面试";
                    break;
                case 3:
                    $status_text = "正在体验";
                    break;
                case 4:
                    $status_text = "体验完成";
                    break;
                default:
                    $status_text = "--";
            }
            $item['status_text'] = $status_text;
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
     * 购买记录
     *
     * @param $id
     * @return mixed
     */
    public function getPurchaseHistory($id)
    {
        $purchase_history = PurchaseHistory::find($id);

        if (!empty($purchase_history)) {
            $purchase_history_progress = ExperienceProgress::where('purchase_history_id', $purchase_history->id)->orderBy('processing_at', 'desc')->get();

            foreach ($purchase_history_progress as &$progress) {
                switch ($progress['status']) {
                    case 1:
                        $progress['status_text'] = '已购买';
                        break;
                    case 2:
                        $progress['status_text'] = '已面试';
                        break;
                    case 3:
                        $progress['status_text'] = '正在体验';
                        break;
                    case 4:
                        $progress['status_text'] = '体验完成';
                        break;
                }
            }

            $purchase_history->progress = $purchase_history_progress;
        }

        return $purchase_history;
    }

    /**
     * 处理加盟申请
     *
     * @param $id
     * @param $data
     * @return array
     */
    public function handle($id, $data)
    {
        $purchase_history = PurchaseHistory::find($id);

        if ($purchase_history->status == $data['status']) {
            return [
                'code' => 1,
                'msg'  => '无需重复执行此操作'
            ];
        }

        $purchase_history->status = intval($data['status']);
        $res = $purchase_history->save();

        if ($res) {

            //更改我的顾客状态
            Customer::where('source_order_id', $id)->where('type', 1)->update(['status' => $data['status']]);

            /*如果状态为已结算返佣,需给用户返佣*/
            if ($data['status'] == 4) {
                Customer::where('source_order_id', $id)->where('type', 1)->update(['moneyStatus' => 2]);

                $new_purchase_history = PurchaseHistory::find($id);

                /*佣金返利*/
                $rebate_commission = ExperienceCourse::where('id', $new_purchase_history->experience_course_id)->value('rebate_commission');

                /*返利给上级*/
                $customer = Customer::where('source_order_id', $id)->where('type', 1)->first();

                if (!empty($customer)) {
                    Guider::where('member_id', $customer->superior_member_id)->increment('total_comission', $rebate_commission); //累计佣金
                    Guider::where('member_id', $customer->superior_member_id)->increment('comission', $rebate_commission); //佣金余额
                    Guider::where('member_id', $customer->superior_member_id)->decrement('expect_comission', $rebate_commission); //待结算佣金
                }
            }

            ExperienceProgress::create([
                'member_id' => $purchase_history->member_id,
                'experience_course_id' => $purchase_history->experience_course_id,
                'purchase_history_id' => $purchase_history->id,
                'status' => $data['status'],
                'remark' => trim($data['remark']),
                'processing_at' => date("Y-m-d H:i:s", time()),
                'created_at' => date("Y-m-d H:i:s", time())
            ]);

            return [
                'code' => 0,
                'msg'  => '保存成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '保存失败'
            ];
        }
    }
}