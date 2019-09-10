<?php

namespace App\Http\Controllers\Admin;

use App\Services\PromoterService;
use Illuminate\Http\Request;

class PromoterController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * PromoterController constructor.
     * @param PromoterService $promoterService
     */
    public function __construct(PromoterService $promoterService)
    {
        $this->service = $promoterService;
    }

    /**
     * 推广员列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/promoter_list', ['data' => $data]);
    }


}
