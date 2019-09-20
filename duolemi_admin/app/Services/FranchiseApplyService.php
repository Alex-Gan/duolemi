<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 15:20
 */
namespace App\Services;

use App\Models\FranchiseApply;
use App\Models\FranchiseCourseProgress;

class FranchiseApplyService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * FranchiseApplyService constructor.
     * @param FranchiseApply $franchiseApply
     */
    public function __construct(FranchiseApply $franchiseApply)
    {
        $this->model = $franchiseApply;
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
                    $status_text = "信息已提交";
                    break;
                case 2:
                    $status_text = "资质已审核";
                    break;
                case 3:
                    $status_text = "教师培训";
                    break;
                case 4:
                    $status_text = "已开课";
                    break;
                case 5:
                    $status_text = "加盟完成";
                    break;
                case 6:
                    $status_text = "已结算返佣";
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
     * 加盟申请记录
     *
     * @param $id
     * @return mixed
     */
    public function getFranchiseApply($id)
    {
        $franchise_apply = FranchiseApply::find($id);

        if (!empty($franchise_apply)) {
            $franchise_course_progress = FranchiseCourseProgress::where('franchise_apply_id', $franchise_apply->id)->orderBy('processing_at', 'desc')->get();

            foreach ($franchise_course_progress as &$progress) {
                switch ($progress['status']) {
                    case 1:
                        $progress['status_text'] = '信息已提交';
                        break;
                    case 2:
                        $progress['status_text'] = '资质已审核';
                        break;
                    case 3:
                        $progress['status_text'] = '教师培训';
                        break;
                    case 4:
                        $progress['status_text'] = '已开课';
                        break;
                    case 5:
                        $progress['status_text'] = '加盟完成';
                        break;
                    case 6:
                        $progress['status_text'] = '已结算返佣';
                        break;
                }
            }

            $franchise_apply->progress = $franchise_course_progress;
        }

        return $franchise_apply;
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
        $franchise_apply = FranchiseApply::find($id);
        $franchise_apply->status = intval($data['status']);
        $franchise_apply->lately_handle_at = date("Y-m-d H:i:s", time());
        $res = $franchise_apply->save();

        if ($res) {
            FranchiseCourseProgress::create([
                'member_id' => $franchise_apply->member_id,
                'franchise_course_id' => $franchise_apply->franchise_course_id,
                'franchise_apply_id' => $franchise_apply->id,
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