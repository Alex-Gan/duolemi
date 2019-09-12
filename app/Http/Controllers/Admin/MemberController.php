<?php

namespace App\Http\Controllers\Admin;

use App\Services\MemberService;
use Illuminate\Http\Request;

class MemberController extends BaseController
{
    /*定义一个*/
    protected $service;

    /**
     * 构造方法
     *
     * MemberController constructor.
     * @param MemberService $memberService
     */
    public function __construct(MemberService $memberService)
    {
        $this->service = $memberService;
    }

    /**
     * 推广员列表
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function list(Request $request)
    {
        $data = $this->service->getUserList($request->all());

        return view('admin/member_list', ['data' => $data]);
    }
}
