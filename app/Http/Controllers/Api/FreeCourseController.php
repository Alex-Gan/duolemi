<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\FreeCourseService;

class FreeCourseController
{
    /*定义service变量*/
    protected $service;

    /**
     * 体验课的构造方法
     *
     * IndexController constructor.
     * @param FreeCourseService $freeCourseService
     */
    public function __construct(FreeCourseService $freeCourseService)
    {
        $this->service = $freeCourseService;
    }

    /**
     * 体验课详情
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCourseDetail(Request $request)
    {
        return $this->service->freeCourseDetail($request->input());
    }

    /**
     * 支付体验课
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCoursePay(Request $request)
    {
        return $this->service->freeCoursePay($request->input());
    }

    /**
     * 支付回调通知
     */
    public function notifyUrl()
    {
        return $this->service->notifyUrl();
    }

    /**
     * 我的体验课列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFreeCourse(Request $request)
    {
        return $this->service->myFreeCourse($request->input());
    }

    /**
     * 我的体验课详情
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myFreeCourseDetail(Request $request)
    {
        return $this->service->myFreeCourseDetail($request->input());
    }

    /**
     * 生成体验课二维码
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function freeCourseCode(Request $request)
    {
        return $this->service->freeCourseCode($request->input());
    }
}