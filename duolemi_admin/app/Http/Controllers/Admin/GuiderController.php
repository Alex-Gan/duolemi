<?php

namespace App\Http\Controllers\Admin;

use App\Services\GuiderService;
use Illuminate\Http\Request;

class GuiderController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * GuiderController constructor.
     * @param GuiderService $guiderService
     */
    public function __construct(GuiderService $guiderService)
    {
        $this->service = $guiderService;
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

        return view('admin/guider_list', ['data' => $data]);
    }


}
