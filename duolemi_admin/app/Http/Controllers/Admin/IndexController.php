<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 13:21
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class IndexController extends BaseController
{
    /**
     * 首页
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        //用户信息
        $userData = $this->getUserData($request);
        //获取菜单
        $menu = $this->getMenuList();
        return View('admin/index', $userData)->with('menu', $menu);
    }

    /**
     * 我的桌面
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function home(Request $request)
    {
        //用户信息
        $userData = $this->getUserData($request);
        $userData['curr_time'] = date("Y-m-d H:i:s", time());

        return View('admin/home', $userData);
    }
}