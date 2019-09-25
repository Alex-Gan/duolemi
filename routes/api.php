<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/


/*
 * 接口模块相关路由
 */
Route::group(['namespace' => 'Api'], function() {

    //首页页面对应的数据
    Route::post('index', 'IndexController@getIndexData'); //首页页面对应的数据
    Route::post('leagueDetail', 'LeagueController@leagueDetail'); //加盟课程详情
    Route::post('leagueApply', 'LeagueController@leagueApply'); //申请加盟
    Route::post('freeCourseDetail', 'FreeCourseController@freeCourseDetail'); //体验课详情
    Route::post('freeCourseCode', 'FreeCourseController@freeCourseCode'); //生成体验课二维码
    Route::post('freeCoursePay', 'FreeCourseController@freeCoursePay'); //支付体验课
    Route::post('notify_url', 'FreeCourseController@notifyUrl'); //支付结果通知
    Route::post('myFreeCourse', 'FreeCourseController@myFreeCourse'); //我的体验课列表
    Route::post('myFreeCourseDetail', 'FreeCourseController@myFreeCourseDetail'); //我的体验课详情
    Route::post('myLeagueDetail', 'LeagueController@myLeagueDetail'); //我的加盟详情


    Route::post('myCommission', 'CommissionController@myCommission'); //我的佣金
    Route::post('CashWithdraw', 'CommissionController@CashWithdraw'); //提现申请
    Route::post('withdrawList', 'CommissionController@withdrawList'); //获取佣金提现列表
    Route::post('getArticleDetails', 'CommissionController@getArticleDetails'); //获取文章内容


    //个人信息相关的路由
    Route::post('checkLogin', 'PersonalDataController@checkLogin'); //检查用户是否登录过
    Route::post('login', 'PersonalDataController@getPersonalOpenIdByCode'); //微信code换取身份openid
    Route::post('uploadUserInfo', 'PersonalDataController@syncPersonalData'); //同步微信用户信息到服务端（保存用户头像昵称）
    Route::post('getPhoneNumber', 'PersonalDataController@getPhoneNumber'); //获取微信授权的手机号
    Route::post('register', 'PersonalDataController@register'); //绑定手机号
    Route::post('getUserInfo', 'PersonalDataController@getUserInfo'); //个人信息


    //图片合成
    Route::get('image', 'TestController@image');
});
