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


//登陆模块
Route::group(['namespace'  => "Code"], function () {

    /**
     * IndexController
     */
    Route::post('/index',                             'IndexController@index');
    Route::get('/welcome',                           'IndexController@welcome');
    //Route::post('/index/update',                      'IndexController@update');//检测更新
    Route::post('/index/notifyUrl',                   'IndexController@notifyUrl');//检测更新
    Route::get('/index/zfbtz',                        'IndexController@zfbtz');
    Route::get('/index/zfpaycheck',                   'IndexController@zfpaycheck');

    /**
     *LoginController
     */
    Route::post('/login/login',                       'LoginController@login');//码商登录
    Route::post('/login/mobile',                      'LoginController@mobile');//注册
    Route::post('/login/sendcode',                    'LoginController@sendcode');//短信登录发送验证码
    Route::post('/login/mobilelogin',                 'LoginController@mobilelogin');//手机验证码登陆
    Route::post('/login/regsendcode',                 'LoginController@regsendcode');//账户注册发送验证码

    /**
     * MycenterController
     */
    Route::post('/Mycenter/change_nickname',          'MycenterController@change_nickname');//修改昵称
    Route::post('/Mycenter/czlist',                    'MycenterController@czlist');//充值信息列表
    Route::post('/Mycenter/chongzhi',                  'MycenterController@chongzhi');//上传充值凭证
    Route::post('/Mycenter/recharge_list',            'MycenterController@recharge_list');//充值记录


    Route::post('/Mycenter/draw',                       'MycenterController@draw');//提现申请
    Route::post('/Mycenter/withdraw_list',            'MycenterController@withdraw_list');//提现记录
    Route::post('/Mycenter/bet_list',                  'MycenterController@bet_list');//下注记录


    Route::post('/Mycenter/update',                     'MycenterController@update');//检测更新
    Route::post('/Mycenter/getnotice',                  'MycenterController@getnotice');//获取通告



    /**
     * ZfnoticeController
     */
    Route::post('/Zfnotice/index',                  'ZfnoticeController@index');//获取通告
    Route::post('/Zfnotice/message',                'ZfnoticeController@message');//获取消息
    Route::post('/Zfnotice/setifread',              'ZfnoticeController@setifread');//已读消息
    Route::post('/zfnotice/kefu',                   'ZfnoticeController@kefu');//客服
    Route::post('/Zfnotice/getnotice',              'ZfnoticeController@getnotice');//邀请码记录
    /**
     * UserController
     */
    Route::post('/user/is_status',                'UserController@is_status');//判断是否在线



});