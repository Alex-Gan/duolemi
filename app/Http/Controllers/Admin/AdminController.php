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
     * 账号管理
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $user_list = $this->service->getUserList($request);
        return view('admin/admin_list', ['data' => $user_list]);
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

    /**
     * 添加管理员页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin/admin_add');
    }

    /**
     * 添加管理员
     *
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        return $this->service->add($request->input());
    }

    /**
     * 编辑管理员页面
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editView($id)
    {
        $user_data = $this->service->getAdminInfo($id);
        return view('admin/admin_edit', $user_data);
    }

    /**
     * 编辑管理员
     *
     * @param $id
     * @param Request $request
     * @return array
     */
    public function edit($id, Request $request)
    {
        return $this->service->edit($id, $request->input());
    }

    /**
     * 删除管理员
     *
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        return $this->service->delete($id);
    }
}
