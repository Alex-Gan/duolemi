<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 10:28
 */
namespace App\Services;

use App\Models\Admin;
use App\Models\Member;

class AdminService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * MemberService constructor.
     * @param Member $member
     */
    public function __construct(Admin $admin)
    {
        $this->model = $admin;
    }

    /**
     * 账号列表
     *
     * @param $params
     * @return array
     */
    public function getUserList($params)
    {
        $page = isset($params['page'])?intval($params['page']):1;
        $limit = isset($params['limit'])?intval($params['limit']):10;
        $offset = $page > 0 ? ($page-1)*$limit : 0;

        //搜索条件
        $username = isset($params['username']) ? $params['username'] : '';
        $nickname = isset($params['nickname']) ? $params['nickname'] : '';

        $wheres = [
            ['column' => 'status', 'value' => 1, 'operator' => '=']
        ];

        if (!empty($username)) {
            $wheres[] = ['column' => 'username', 'value' => $username, 'operator' => '='];
        }

        if (!empty($nickname)) {
            $wheres[] = ['column' => 'nickname', 'value' => $nickname, 'operator' => '='];
        }

        //排序
        $sorts = [
            ['column' => 'created_at', 'direction' => 'desc']
        ];

        $data = $this->getList($wheres, $offset, $limit, $sorts);

        $result = [
            'data'    => $data,
            '_count'  => $this->getCount($wheres),
            '_limit'  => $limit,
            '_curr'   => $page,
            '_query'  => http_build_query($params),
            '_params' => $params
        ];

        return $result;
    }

    /**
     * 修改密码以及修改个人信息
     *
     * @param $user_data
     * @param $request
     * @return array
     */
    public function modify($user_data, $request)
    {
        $params = $request->input();
        $oldPassWord = $params['pass']; //原密码
        $password = $params['password']; //新密码
        $confirmPassword = $params['confirmPassword']; //确认密码

        //将密码进行加密，然后跟数据库里面的密码进行比较
        $old_salt_password = md5(md5($oldPassWord, $user_data->salt));

        if ($user_data->password != $old_salt_password) {
            return ['code' => 1, 'msg' => '原密码不正确,审核效验失败！'];
        }

        if ($password != $confirmPassword) {
            return ['code' => 1, 'msg' => '两次密码不一致'];
        }

        //获取密码跟盐
        $pass_salt = $this->randCreatePassword($password);

        $user_data_model = Admin::find($user_data->id);
        $user_data_model->nickname = $params['nickname'];
        $user_data_model->password = $pass_salt['password'];
        $user_data_model->salt     = $pass_salt['salt'];
        $res = $user_data_model->save();
        if ($res) {
            //清空当前登录session
            $request->session()->forget("sess_admin_user_key");
            return ['code' => 0, 'msg' => '修改成功'];
        } else {
            return ['code' => 1, 'msg' => '修改失败'];
        }
    }

    /**
     * 随机生成密码
     *
     * @param $password
     * @return array
     */
    private function randCreatePassword($password)
    {
        $salt = $this->randCreateSalt();

        return [
            'password' => md5(md5($password, $salt)),
            'salt'     => $salt
        ];
    }

    /**
     * 随机生成密码盐
     */
    private function randCreateSalt()
    {
        return self::rand_str(6,1);
    }

    /**
     * 获取随机字符串
     *
     * @param int $randLength 长度
     * @param int $includenumber 是否包含数字
     * @return string
     */
    public static function rand_str($randLength = 6, $includenumber = 0)
    {
        if ($includenumber) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNPQEST123456789';
        } else {
            $chars = 'abcdefghijklmnopqrstuvwxyz';
        }

        $len = strlen($chars);
        $randStr = '';

        for ($i = 0; $i < $randLength; $i++) {
            $randStr .= $chars[mt_rand(0, $len - 1)];
        }

        $tokenvalue = $randStr;

        return $tokenvalue;
    }

    /**
     * 添加账号
     *
     * @param $data
     * @return array
     */
    public function add($data)
    {
        /*检验两次输入密码是否一致*/
        if ($data['password'] != $data['confirmPassword']) {
            return ['code' => 1, 'msg' => '两次密码不一致'];
        }

        /*检查账号是否存在*/
        $admin_has = Admin::where('username', $data['username'])->exists();
        if ($admin_has) {
            return ['code' => 1, 'msg' => '该账号已存在'];
        }

        /*获取加密后的密码以及盐*/
        $rand_data = $this->randCreatePassword($data['password']);

        $aid = Admin::create([
            'username'   => $data['username'],
            'nickname'   => $data['nickname'],
            'password'   => $rand_data['password'],
            'salt'       => $rand_data['salt'],
            'created_at' => date("Y-m-d H:i:s", time())
        ]);

        if ($aid) {
            return ['code' => 0, 'msg' => '添加账号成功'];
        } else {
            return ['code' => 1, 'msg' => '添加账号失败'];
        }
    }

    /**
     * 管理员数据
     *
     * @param $id
     * @return mixed
     */
    public function getAdminInfo($id)
    {
        return Admin::find($id);
    }

    /**
     * 编辑管理员
     *
     * @param $id
     * @param $data
     * @return array
     */
    public function edit($id, $data)
    {
        /*检验两次输入密码是否一致*/
        if ($data['password'] != $data['confirmPassword']) {
            return ['code' => 1, 'msg' => '两次密码不一致'];
        }

        /*用户信息*/
        $admin = Admin::find($id);

        if ($admin->username != $data['username']) {
            /*检查账号是否存在*/
            $admin_has = Admin::where('username', $data['username'])->exists();
            if ($admin_has) {
                return ['code' => 1, 'msg' => '该账号已存在'];
            }
        }

        /*获取加密后的密码以及盐*/
        $rand_data = $this->randCreatePassword($data['password']);

        $admin->username = $data['username'];
        $admin->nickname = $data['nickname'];
        $admin->password = $rand_data['password'];
        $admin->salt     = $rand_data['salt'];
        $res = $admin->save();

        if ($res) {
            return ['code' => 0, 'msg' => '编辑账号成功'];
        } else {
            return ['code' => 1, 'msg' => '编辑账号失败'];
        }
    }

    /**
     * 删除
     *
     * @param $id
     * @return array
     */
    public function delete($id)
    {
        if ($id == 1) {
            return ['code' => 1, 'msg' => '管理员不能删除'];
        }

        $res = Admin::where('id', $id)->update(['status' => -1]);

        if ($res) {
            return ['code' => 0, 'msg' => '删除成功'];
        } else {
            return ['code' => 1, 'msg' => '删除失败'];
        }
    }
}