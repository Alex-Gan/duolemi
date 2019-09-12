<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 14:30
 */
namespace app\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\BaseService;

class BaseController
{
    /**
     * 获取菜单列表
     *
     * @return array
     */
    public function getMenuList()
    {
        return (new BaseService)->getMenuList();
    }

    /**
     * 从session中获取登录用户信息
     *
     * @param Request $request
     * @return mixed
     */
    public function getUserData($request)
    {
        //获取登录信息session
        return $request->session()->get("sess_admin_user_key");
    }
}