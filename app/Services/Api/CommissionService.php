<?php
namespace App\Services\Api;

use App\Models\Member;
use App\Models\Withdraw;

class CommissionService extends BaseService
{
    /**
     * 提现申请
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function CashWithdraw($data)
    {
        /*效验数据的合法性*/
        $validator = \Validator::make($data, [
            'openid' => 'required',
            'realName' => 'required',
            'applyMoney'=> 'required',
            'bankName'=> 'required',
            'branchName'=> 'required',
            'bankAccount'=> 'required'
        ],[
            'openid.required' => 'openid不能为空',
            'realName.required' => '真实姓名不能为空',
            'applyMoney.required' => '申请金额不能为空',
            'bankName.required' => '银行名称不能为空',
            'branchName.required' => '支行名称不能为空',
            'bankAccount.required' => '银行账户不能为空',
        ]);
        if ($validator->fails()) {
            return $this->formatResponse(400, $validator->messages()->first());
        }

        /*检查用户身份*/
        $member = Member::where('openid', $data['openid'])->first();
        if (empty($member)) {
            return $this->formatResponse(1, '用户信息不存在');
        }

        /*申请金额不低于100，且只能是100的整数倍*/
        if ($data['applyMoney'] < 100 || $data['applyMoney'] % 100 != 0) {
            return $this->formatResponse(1, '申请金额不低于100，且只能是100的整数倍');
        }

        /*同一个用户同一时间只能有一笔待审核提现申请*/
        $withdraw_has = Withdraw::where('member_id', $member->id)->where('status', 1)->exists();
        if ($withdraw_has) {
            return $this->formatResponse(1, '同一个用户同一时间只能有一笔待审核提现申请');
        }

        $res = Withdraw::create([
            'member_id' => $member->id,
            'real_name' => $data['realName'],
            'apply_money' => $data['applyMoney'],
            'bank_name' => $data['bankName'],
            'branch_name' => $data['branchName'],
            'bank_account' => $data['bankAccount'],
            'withdraw_at' => date("Y-m-d H:i:s", time()),
            'created_at' => date("Y-m-d H:i:s", time())
        ]);

        if ($res) {
            return $this->formatResponse(0, '提现申请成功');
        } else {
            return $this->formatResponse(1, '提现申请失败');
        }
    }

    /**
     * 获取佣金提现列表
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawList($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';
        $status = !empty($data['status']) ? intval($data['status']) : 0;

        /*检查用户身份*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(1, '用户信息不存在');
        }
        if ($status > 0) {
            $withdraw = Withdraw::select(['id', 'apply_money', 'withdraw_at', 'status'])
                ->where('member_id', $member->id)
                ->where('status', $status)
                ->orderBy('withdraw_at', 'desc')
                ->get();
        } else {
            $withdraw = Withdraw::select(['id', 'apply_money', 'withdraw_at', 'status'])
                ->where('member_id', $member->id)
                ->orderBy('withdraw_at', 'desc')
                ->get();
        }

        /*状态转化*/
        foreach ($withdraw as &$item) {
            if ($item['status'] == 1) {
                $item['status'] = '待发放';
            } else if ($item['status'] == 2) {
                $item['status'] = '已发放';
            } else if ($item['status'] == 3) {
                $item['status'] = '申请失败';
            }
        }

        return $this->formatResponse(0, 'ok', $withdraw);
    }
}