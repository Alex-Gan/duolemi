<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-15
 * Time: 14:30
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\ArticleService;

class ArticleController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * ArticleController constructor.
     * @param ArticleService $articleService
     */
    public function __construct(ArticleService $articleService)
    {
        $this->service = $articleService;
    }

    /**
     * 文章列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/article_list', ['data' => $data]);
    }

    /**
     * 添加文章
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin/article_add');
    }

    /**
     * 添加文章
     *
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        return $this->service->add($request->input());
    }

    /**
     * 删除文章
     *
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        return $this->service->delete($request->post('id'));
    }

    /**
     * 编辑文章页面
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editView($id)
    {
        $data = $this->service->getActicle($id);
        return view('admin/article_edit', ['data' => $data]);
    }

    public function editPut($id, Request $request)
    {
        return $this->service->editPut($id, $request->input());
    }
}
