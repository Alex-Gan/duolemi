<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    echo 'hello，多乐米...';
    //return view('welcome');
});

Route::get('/test', 'Api\TestController@testToekn');

/*
 * 后台模块相关路由
 */
Route::get('/admin','Admin\LoginController@loginView');
Route::get('/admin/login','Admin\LoginController@loginView'); //登录页面
Route::post('/admin/login','Admin\LoginController@login'); //登录
Route::get('/admin/logout','Admin\LoginController@logout'); //退出

/*************************以下路由需要登录才能访问******************************/
Route::group(['middleware' => ['auth.admin'], 'prefix' => 'admin'], function() {

    //首页模块相关路由
    Route::get('index', 'Admin\IndexController@index'); //首页
    Route::get('home', 'Admin\IndexController@home'); //我的桌面

    //加盟课相关的路由
    Route::get('franchise_course/list','Admin\FranchiseCourseController@list'); //加盟课列表
    Route::get('franchise_course/add','Admin\FranchiseCourseController@addView'); //添加加盟课页面
    Route::post('franchise_course/add','Admin\FranchiseCourseController@add'); //添加加盟课
    Route::delete('franchise_course/delete','Admin\FranchiseCourseController@delete'); //删除加盟课
    Route::get('franchise_course/edit/{id}','Admin\FranchiseCourseController@editView'); //编辑加盟课页面
    Route::put('franchise_course/editPut/{id}','Admin\FranchiseCourseController@editPut'); //编辑加盟课

    //体验课程相关的路由
    Route::get('experience_course/list','Admin\ExperienceCourseController@list'); //体验课程列表
    Route::get('experience_course/add','Admin\ExperienceCourseController@addView'); //添加体验课程页面
    Route::post('experience_course/add','Admin\ExperienceCourseController@add'); //添加体验课程
    Route::delete('experience_course/delete','Admin\ExperienceCourseController@delete'); //删除体验课程
    Route::get('experience_course/edit/{id}','Admin\ExperienceCourseController@editView'); //编辑体验课程页面
    Route::put('experience_course/editPut/{id}','Admin\ExperienceCourseController@editPut'); //编辑体验课程
    Route::put('experience_course/change_status','Admin\ExperienceCourseController@changeStatus'); //更改体验课程上下架状态

    //购买记录相关的路由
    Route::get('purchase_history/list','Admin\PurchaseHistoryController@list'); //购买记录列表
    Route::get('purchase_history/handle/{id}','Admin\PurchaseHistoryController@handleView'); //处理购买记录页面
    Route::put('purchase_history/handle/{id}','Admin\PurchaseHistoryController@handle'); //处理购买记录

    //推广员相关的路由
    Route::get('guider/list','Admin\GuiderController@list'); //推广员列表

    //会员相关的路由
    Route::get('member/list','Admin\MemberController@list'); //会员列表

    //加盟申请记录的路由
    Route::get('franchise_apply/list','Admin\FranchiseApplyController@list'); //加盟申请记录列表
    Route::get('franchise_apply/handle/{id}','Admin\FranchiseApplyController@handleView'); //处理加盟申请页面
    Route::put('franchise_apply/handle/{id}','Admin\FranchiseApplyController@handle'); //处理加盟申请

    //提现相关的路由
    Route::get('withdraw/list','Admin\WithdrawController@list'); //提现列表
    Route::get('withdraw/detail/{id}','Admin\WithdrawController@detail'); //提现详情
    Route::put('withdraw/audit_pass/{id}','Admin\WithdrawController@auditPass'); //提现审核通过
    Route::put('withdraw/audit_reject/{id}','Admin\WithdrawController@auditReject'); //提现审核驳回

    //轮播图相关的路由
    Route::get('banner/list','Admin\BannerController@list'); //轮播图列表
    Route::get('banner/add','Admin\BannerController@addView'); //添加轮播图页面
    Route::post('banner/create','Admin\BannerController@create'); //添加轮播图页面
    Route::delete('banner/delete','Admin\BannerController@delete'); //删除轮播图

    //系统设置相关的路由
    Route::get('settings/list','Admin\SettingsController@list'); //获取系统设置
    Route::put('settings/set','Admin\SettingsController@set'); //设置系统设置

    //导航设置相关的路由
    Route::get('navigation_settings/list','Admin\NavigationSettingsController@list'); //导航设置列表
    Route::get('navigation_settings/edit/{id}','Admin\NavigationSettingsController@editView'); //导航设置编辑页
    Route::put('navigation_settings/editPut/{id}','Admin\NavigationSettingsController@editPut'); //编辑导航设置

    //文章管理相关的路由
    Route::get('article/list','Admin\ArticleController@list'); //文章列表
    Route::get('article/add','Admin\ArticleController@addView'); //添加文章视图
    Route::post('article/add','Admin\ArticleController@add'); //添加文章
    Route::get('article/edit/{id}','Admin\ArticleController@editView'); //编辑文章页面
    Route::put('article/editPut/{id}','Admin\ArticleController@editPut'); //编辑文章
    Route::delete('article/delete','Admin\ArticleController@delete'); //删除轮播图


    //图片上传相关的路由
    Route::post('upload/multi_upload','Common\UploadImageController@upload');

    //用户信息修改
    Route::get('admin/list','Admin\AdminController@list');//管理员列表
    Route::get('admin/add','Admin\AdminController@addView');//添加管理员页面
    Route::post('admin/add','Admin\AdminController@add');//添加管理员
    Route::get('admin/edit/{id}','Admin\AdminController@editView');//编辑管理员页面
    Route::put('admin/edit/{id}','Admin\AdminController@edit');//编辑管理员
    Route::get('modify','Admin\AdminController@modifyView'); //用户信息显示
    Route::put('modify','Admin\AdminController@modify');//修改用户信息
    Route::delete('admin/delete/{id}','Admin\AdminController@delete');//删除用户信息
});
