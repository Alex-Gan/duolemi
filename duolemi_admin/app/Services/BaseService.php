<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 14:32
 */

namespace App\Services;

class BaseService
{
    protected $model;

    /**
     * 获取全部
     *
     * @param array $wheres
     * @param null $offset
     * @param null $limit
     * @param array $sorts
     * @param null $select
     * @param array $index
     * @return mixed
     */
    public function getList($wheres=array(), $offset=null, $limit=null, $sorts=array(), $select=null,$index=array())
    {
        $query = $this->model->query();

        if($index && isset($index['key']) && $index['type']){
            $query->index($index['key'],$index['type']);
        }
        foreach($wheres as $where) {
            $query->where($where['column'], $where['operator'], $where['value']);
        }
        if(!is_null($select)) {
            $query->addSelect($select);
        }
        if(!is_null($offset)) {
            $query->skip($offset);
        }
        if(!is_null($limit)) {
            $query->take($limit);
        }
        if($sorts){
            foreach($sorts as $sort) {
                $query->orderBy($sort['column'], $sort['direction']);
            }
        }
        return $query->get();
    }

    /**
     * 获取条数
     *
     * @param array $wheres
     * @return mixed
     */
    public function getCount($wheres=array())
    {
        $query = $this->model->query();
        foreach($wheres as $where) {
            $query->where($where['column'], $where['operator'], $where['value']);
        }
        return $query->count();
    }

    /**
     * 菜单列表
     *
     * @return array
     */
    public function getMenuList($request)
    {
        $admin_menu = config("adminmenu");

        /*获取session数据*/
        $session_data = $request->session()->get('sess_admin_user_key');
        if ($session_data->id != 1) {
            unset($admin_menu[11]);
        }

        return $admin_menu;
    }
}