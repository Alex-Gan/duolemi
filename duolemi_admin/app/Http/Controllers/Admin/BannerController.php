<?php

namespace App\Http\Controllers\Admin;

use App\Services\BannerService;
use Illuminate\Http\Request;

class BannerController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * WithdrawController constructor.
     * @param BannerService $bannerService
     */
    public function __construct(BannerService $bannerService)
    {
        $this->service = $bannerService;
    }

    /**
     * 轮播图列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/banner_list', ['data' => $data]);
    }

    /**
     * 添加轮播图页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        $data = $this->service->getNavigationPage();

        return view('admin/banner_add', ['data' => $data]);
    }

    /**
     * 添加
     *
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request)
    {
        return $this->service->create($request->post());
    }

    /**
     * 删除轮播图
     *
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        return $this->service->delete($request->post('id'));
    }
}
