<?php

namespace App\Http\Controllers\Admin;

use App\Services\NavigationSettingsService;
use Illuminate\Http\Request;

class NavigationSettingsController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * NavigationSettingsController constructor.
     * @param NavigationSettingsService $navigationSettingsService
     */
    public function __construct(NavigationSettingsService $navigationSettingsService)
    {
        $this->service = $navigationSettingsService;
    }

    /**
     * 导航设置列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/navigation_settings_list', ['data' => $data]);
    }

    /**
     * 导航设置编辑页
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editView($id)
    {
        $data = $this->service->getNavigationSettingsById($id);

        return view('admin/navigation_settings_edit', ['data' => $data]);
    }

    /**
     * 编辑导航设置
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function editPut($id, Request $request)
    {
        return $this->service->editPut($id, $request->input());
    }
}
