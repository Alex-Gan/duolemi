<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\ExperienceProgress;
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
        $purchase_history->status = intval($data['status']);
        $res = $purchase_history->save();

        if ($res) {
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