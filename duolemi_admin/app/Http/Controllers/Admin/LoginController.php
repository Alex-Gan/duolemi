<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 13:21
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\LoginService;

class LoginController
{
    protected $service;

    public function __construct(LoginService $loginService)
    {
        $this->service = $loginService;
    }

    /**
     * 渲染登录页面
     */
    public function loginView()
    {
        return view('admin/login');
    }

    /**
     * 登录
     *
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|between:6,20',
        ], [
            'username.required' => '用户名不能为空',
            'password.required' => '密码不能为空',
            'password.between'  => '密码必须在6~20位之间',
        ]);

        if ($validator->fails()) {

            $warnings = $validator->errors()->first();

            return [
                'code' => 405,
                'msg'  => $warnings
            ];
        }

        //用户名、密码
        $username = $request->input('username', '');
        $password = $request->input('password', '');

        $result = $this->service->checkLogin($username, $password);

        if ($result) {
            //将登陆信息存入session
            $request->session()->put('sess_admin_user_key', $result);

            return [
                'code' => 0,
                'msg'  => '登录成功'
            ];
        } else {
            return [
                'code' => 405,
                'msg'  => '用户名或密码错误'
            ];
        }
    }

    /**
     * 退出
     *
     * @param Request $request
     * @return array
     */
    public function logout(Request $request)
    {
        //删除session中的数据
        $request->session()->forget("sess_admin_user_key");

        return [
            'code' => 0,
            'msg'  => '注销成功'
        ];
    }
}