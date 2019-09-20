<?php

namespace App\Http\Controllers\Admin;

use App\Services\PurchaseHistoryService;
use Illuminate\Http\Request;

class PurchaseHistoryController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * PurchaseHistoryController constructor.
     * @param PurchaseHistoryService $purchaseHistoryService
     */
    public function __construct(PurchaseHistoryService $purchaseHistoryService)
    {
        $this->service = $purchaseHistoryService;
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

        return view('admin/purchase_history_list', ['data' => $data]);
    }

    /**
     * 处理购买记录页面
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleView($id)
    {
        $data = $this->service->getPurchaseHistory($id);

        return view('admin/purchase_history_edit', ['data' => $data]);
    }

    /**
     * 处理购买记录
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
