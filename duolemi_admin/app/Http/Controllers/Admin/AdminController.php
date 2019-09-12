<?php
/**
 * 后台管理员
 */
namespace App\Http\Controllers\Admin;

use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * AdminController constructor.
     * @param AdminService $adminService
     */
    public function __construct(AdminService $adminService)
    {
        $this->service = $adminService;
    }

    /**
     * 用户信息显示
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function modifyView(Request $request)
    {
        $user_data = $this->getUserData($request);
        return view('admin/modify', $user_data);
    }

    /**
     * 修改用户信息
     *
     * @param Request $request
     * @return array
     */
    public function modify(Request $request)
    {
        $user_data = $this->getUserData($request);
        return $this->service->modify($user_data, $request);
    }
}
