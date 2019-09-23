<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\CommissionService;

class CommissionController
{
    /*定义service变量*/
    protected $service;

    /**
     * 构造方法
     *
     * CommissionController constructor.
     * @param CommissionService $commissionService
     */
    public function __construct(CommissionService $commissionService)
    {
        $this->service = $commissionService;
    }

    /**
     * 提现申请
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function CashWithdraw(Request $request)
    {
        return $this->service->CashWithdraw($request->input());
    }

    /**
     * 获取佣金提现列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawList(Request $request)
    {
        return $this->service->withdrawList($request->input());
    }
}
