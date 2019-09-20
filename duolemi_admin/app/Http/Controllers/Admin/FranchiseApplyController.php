<?php

namespace App\Http\Controllers\Admin;

use App\Services\FranchiseApplyService;
use Illuminate\Http\Request;

class FranchiseApplyController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * FranchiseApplyController constructor.
     * @param FranchiseApplyService $franchiseApplyService
     */
    public function __construct(FranchiseApplyService $franchiseApplyService)
    {
        $this->service = $franchiseApplyService;
    }

    /**
     * 购买记录列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/franchise_apply_list', ['data' => $data]);
    }

    /**
     * 处理加盟申请页面
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleView($id)
    {
        $data = $this->service->getFranchiseApply($id);

        return view('admin/franchise_apply_edit', ['data' => $data]);
    }

    /**
     * 处理加盟申请
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function handle($id, Request $request)
    {
        return $this->service->handle($id, $request->input());
    }
}
