<?php
/**
 * Created by PhpStorm.
 * User: gcs
 * Date: 2019-09-07
 * Time: 13:21
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Services\ExperienceCourseService;

class ExperienceCourseController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * FranchiseCourseController constructor.
     * @param ExperienceCourseService $experienceCourseService
     */
    public function __construct(ExperienceCourseService $experienceCourseService)
    {
        $this->service = $experienceCourseService;
    }

    /**
     * 体验课程列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/experience_course_list', ['data' => $data]);
    }

    /**
     * 显示添加加盟课页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addView()
    {
        return view('admin/experience_course_add');
    }

    /**
     * 添加加盟课
     *
     * @param Request $request
     * @return array
     */
    public function add(Request $request)
    {
        return $this->service->add($request->post());
    }

    /**
     * 删除加盟课
     *
     * @param Request $request
     * @return array
     */
    public function delete(Request $request)
    {
        return $this->service->delete($request->post('id'));
    }

    /**
     * 显示编辑加盟课页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editView($id)
    {
        $edit_data = $this->service->editView($id);
        return view('admin/experience_course_edit')->with('edit_data', $edit_data);
    }

    /**
     * 编辑加盟课
     *
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function editPut($id, Request $request)
    {
        return $this->service->editPut($id, $request->post());
    }

    /**
     * 更改体验课程上下架状态
     *
     * @param Request $request
     * @return mixed
     */
    public function changeStatus(Request $request)
    {
        return $this->service->changeStatus($request->post('id'));
    }
}