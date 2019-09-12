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
}
