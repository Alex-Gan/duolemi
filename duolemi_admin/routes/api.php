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
    Route::post('leagueDetail', 'IndexController@leagueDetail'); //加盟课程详情

    //个人信息相关的路由
    Route::post('login', 'PersonalDataController@getPersonalOpenIdByCode'); //微信code换取身份openid
    Route::post('sync', 'PersonalDataController@syncPersonalData'); //同步微信用户信息到服务端
});
