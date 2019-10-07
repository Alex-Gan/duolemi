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
     * 我的佣金
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCommission(Request $request)
    {
        return $this->service->myCommission($request->input());
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

    /**
     * 获取提现人相关信息
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function withdrawDetail(Request $request)
    {
        return $this->service->withdrawDetail($request->input());
    }

    /**
     * 获取文章内容
     *
     * @param Request $request
     * @return mixed
     */
    public function getArticleDetails(Request $request)
    {
        return $this->service->getArticleDetails($request->input('id'));
    }

    /**
     * 我的客户-根据关键词或获取全部
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCustomer(Request $request)
    {
        return $this->service->myCustomer($request->input());
    }

    /**
     * 我的客户详情
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myCustomerDetail(Request $request)
    {
        return $this->service->myCustomerDetail($request->input());
    }
}
