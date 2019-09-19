<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\IndexService;

class IndexController
{
    /*定义service变量*/
    protected $service;

    /**
     * 首页的构造方法
     *
     * IndexController constructor.
     * @param IndexService $indexService
     */
    public function __construct(IndexService $indexService)
    {
        $this->service = $indexService;
    }

    /**
     * 首页数据
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndexData()
    {
        return $this->service->getIndexData();
    }
}