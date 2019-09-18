<?php
namespace App\Http\Controllers\Api;

use App\Services\Api\PersonalDataService;
use Illuminate\Http\Request;

class PersonalDataController
{
    /*定义service变量*/
    protected $service;

    /**
     * 个人信息构造方法
     *
     * PersonalDataController constructor.
     * @param PersonalDataService $personalDataService
     */
    public function __construct(PersonalDataService $personalDataService)
    {
        $this->service = $personalDataService;
    }

    /**
     * 通过code微信换取身份openid
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPersonalOpenIdByCode(Request $request)
    {
        return $this->service->getPersonalOpenIdByCode($request->input('code'));
    }

    /**
     * 同步微信用户信息到服务端
     *
     * @param Request $request
     */
    public function syncPersonalData(Request $request)
    {
        return $this->service->syncPersonalData($request->input());
    }
}