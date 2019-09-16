<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-10
 * Time: 10:28
 */
namespace App\Services;

use App\Models\Article;

class ArticleService extends BaseService
{
    protected $model;

    /**
     * 构造方法
     *
     * ArticleService constructor.
     * @param Article $article
     */
    public function __construct(Article $article)
    {
        $this->model = $article;
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
        $title = isset($params['title']) ? $params['title'] : '';
        $start_date = isset($params['start_date']) ? $params['start_date']." 00:00:00" : '';
        $end_date = isset($params['end_date']) ? $params['end_date']." 23:59:59" : '';

        $wheres = [
            ['column' => 'is_delete', 'value' => 0, 'operator' => '=']
        ];

        if (!empty($title)) {
            $wheres[] = ['column' => 'title', 'value' => $title, 'operator' => '='];
        }

        if (!empty($start_date)) {
            $wheres[] = ['column' => 'updated_at', 'value' => $start_date, 'operator' => '>='];
        }

        if (!empty($end_date)) {
            $wheres[] = ['column' => 'updated_at', 'value' => $end_date, 'operator' => '<='];
        }

        //排序
        $sorts = [
            ['column' => 'updated_at', 'direction' => 'desc']
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
     * 添加文章
     *
     * @param $params
     * @return array
     */
    public function add($params)
    {
        $data = [
            'title'      => $params['title'],
            'content'    => htmlspecialchars($params['content']),
            'created_at' => date("Y-m-d H:i:s", time()),
            'updated_at' => date("Y-m-d H:i:s", time())
        ];

        $res = Article::create($data);
        if ($res) {
            return [
                'code' => 0,
                'msg'  => '添加成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '添加失败'
            ];
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
        $res = Article::where('id', $id)->update(['is_delete' => 1]);

        if ($res) {
            return [
                'code' => 0,
                'msg'  => '删除成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '删除失败'
            ];
        }
    }

    /**
     * 获取文章信息
     *
     * @param $id
     * @return mixed
     */
    public function getActicle($id)
    {
        return $this->model::find($id);
    }

    /**
     * 修改文章
     *
     * @param $id
     * @param $params
     * @return array
     */
    public function editPut($id, $params)
    {
        $article_model = $this->model::find($id);
        $article_model->title = $params['title'];
        $article_model->content = htmlspecialchars($params['content']);
        $res = $article_model->save();
        if ($res) {
            return [
                'code' => 0,
                'msg'  => '修改成功'
            ];
        } else {
            return [
                'code' => 1,
                'msg'  => '修改失败'
            ];
        }
    }

    /**
     * 获取全部文章列表
     *
     * @return mixed
     */
    public function getAllArticle()
    {
        return $this->model::select(['id', 'title'])
            ->where('is_delete', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}