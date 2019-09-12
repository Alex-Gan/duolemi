<?php

namespace App\Http\Controllers\Admin;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * WithdrawController constructor.
     * @param SettingsService $settingsService
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->service = $settingsService;
    }

    /**
     * 系统设置
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list()
    {
        $data = $this->service->getSettings();

        return view('admin/settings', ['data' => $data]);
    }

    /**
     * 设置
     *
     * @param Request $request
     * @return array
     */
    public function set(Request $request)
    {
        return $this->service->setSettings($request->input());
    }
}
