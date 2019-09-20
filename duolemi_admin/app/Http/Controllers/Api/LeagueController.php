<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\Api\LeagueService;

class LeagueController
{
    /*定义service变量*/
    protected $service;

    /**
     * 加盟课的构造方法
     *
     * IndexController constructor.
     * @param LeagueService $leagueService
     */
    public function __construct(LeagueService $leagueService)
    {
        $this->service = $leagueService;
    }

    /**
     * 加盟课程详情
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagueDetail(Request $request)
    {
        return $this->service->leagueDetail($request->input());
    }

    /**
     * 申请加盟
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function leagueApply(Request $request)
    {
        return $this->service->leagueApply($request->input());
    }

    /**
     * 我的加盟详情
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myLeagueDetail(Request $request)
    {
        return $this->service->myLeagueDetail($request->input());
    }
}