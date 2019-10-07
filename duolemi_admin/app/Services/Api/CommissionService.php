<?php
namespace App\Services\Api;

use App\Models\Article;
use App\Models\Customer;
use App\Models\Guider;
use App\Models\Member;
use App\Models\Withdraw;

class CommissionService extends BaseService
{
    /**
     * 我的佣金
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCommission($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if (empty($openid)) {
            return $this->formatResponse(1, 'openid不能为空');
        }

        /*检查用户身份*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(1, '用户信息不存在');
        }

        /*佣金信息*/
        $guider = Guider::select(['expect_comission as forTheAccount', 'comission as account'])
            ->where('member_id', $member->id)
            ->first();

        /*推客信息不存在*/
        if (empty($guider)) {
            $guider['forTheAccount'] = 0;
            $guider['account'] = 0;
        }

        /*佣金规则*/
        //$guider['CommissionRules'] = "";

        return $this->formatResponse(0, 'ok', $guider);
    }


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

    /**
     * 获取提现人相关信息
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawDetail($data)
    {
        $openid = !empty($data['openid']) ? $data['openid'] : '';

        if (empty($openid)) {
            return $this->formatResponse(1, 'openid不能为空');
        }

        /*检查用户身份*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(1, '用户信息不存在');
        }

        /*提现人相关信息*/
        $withdraw = Withdraw::select(['real_name as realName', 'bank_name as bankName', 'branch_name as branchName', 'bank_account as bankAccount
', 'apply_money as account'])
            ->where('member_id', $member->id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (empty($withdraw)) {
            $withdraw = [];
        } else {
            $withdraw['name'] = $member->nickname;
        }
        return $this->formatResponse(0, 'ok', $withdraw);
    }

    /**
     * 获取文章内容
     *
     * @param $id
     * @return mixed
     */
    public function getArticleDetails($id)
    {
        $content = Article::where('id', $id)->value('content');

        if (!empty($content)) {
            /*将详情简介内容格式化*/
            $content = htmlspecialchars_decode($content);
        }

        return $this->formatResponse(0, 'ok', $content);
    }

    /**
     * 我的客户-根据关键词或获取全部
     *
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCustomer($data)
    {
        $openid = $data['openid'];
        $keyword = !empty($data['keyword']) ? $data['keyword'] : ''; //关键词，根据客户名称或手机号搜索

        if (empty($openid)) {
            return $this->formatResponse(1, 'openid不能为空');
        }

        /*检查用户身份*/
        $member = Member::where('openid', $openid)->first();
        if (empty($member)) {
            return $this->formatResponse(1, '用户信息不存在');
        }

        /*我的客户*/
        if (!empty($keyword)) {
            $customer = Customer::select(['id', 'faceImg', 'name', 'mobile', 'created_at as date', 'money', 'moneyStatus', 'type', 'status'])
                ->where('superior_member_id', $member->id)
                ->where(function ($query) use($keyword){
                    $query->where('name', '=', $keyword)->orWhere(function($query) use($keyword){
                        $query->where('mobile','=', $keyword);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $customer = Customer::select(['id', 'faceImg', 'name', 'mobile', 'created_at as date', 'money', 'moneyStatus', 'type', 'status'])
                ->where('superior_member_id', $member->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return $this->formatResponse(0, 'ok', $customer);
    }
}