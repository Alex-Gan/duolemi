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
}