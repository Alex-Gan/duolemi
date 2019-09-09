<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 14:46
 */
namespace App\Services;

use App\Models\Admin;

class LoginService
{
    /**
     * 检验登录
     *
     * @param $username
     * @param $password
     * @return bool|array
     */
    public function checkLogin($username, $password)
    {
        $admin = Admin::where('username', $username)->where('status', 1)->first();
        //当前用户不存在
        if (empty($admin)) {
            return false;
        }

        //将密码进行加密，然后跟数据库里面的密码进行比较
        $salt_password = md5(md5($password, $admin->salt));

        if ($salt_password != $admin->password) {
            return false;
        }

        return $admin;
    }
}