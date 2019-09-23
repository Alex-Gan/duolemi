<?php

namespace App\Http\Controllers\Admin;

use App\Services\WithdrawService;
use Illuminate\Http\Request;

class WithdrawController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * WithdrawController constructor.
     * @param WithdrawService $withdrawService
     */
    public function __construct(WithdrawService $withdrawService)
    {
        $this->service = $withdrawService;
    }

    /**
     * 提现列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/withdraw_list', ['data' => $data]);
    }

    /**
     * 提现详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id)
    {
        $data = $this->service->getDetail($id);

        return view('admin/withdraw_detail', ['data' => $data]);
    }

    /**
     * 提现审核通过
     *
     * @param $id
     * @return array
     */
    public function auditPass($id)
    {
        return $this->service->auditPass($id);
    }

    /**
     * 提现审核驳回
     *
     * @param $id
     * @return array
     */
    public function auditReject($id)
    {
        return $this->service->auditReject($id);
    }
}
