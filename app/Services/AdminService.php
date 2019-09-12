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
    public function __construct(Member $member)
    {
        $this->model = $member;
    }

    /**
     * 列表
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
        $nickname   = isset($params['nickname']) ? $params['nickname'] : '';

        $wheres = [
            ['column' => 'id', 'value' => 0, 'operator' => '>']
        ];

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

}