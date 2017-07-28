<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::get('/','IndexController@index');
//首页专家数据
Route::post('returnData','IndexController@returnData');
//专家列表
Route::post('collectExpert','ExpertController@collectExpert');
//登录
Route::get('login','LoginController@login');
//注册
Route::get('register','LoginController@register');
//找回密码
Route::get('forget','LoginController@forget');
//登录验证
Route::post('loginHandle','LoginController@loginHandle');
//注册验证
Route::post('registerHandle','LoginController@registerHandle');

//找回密码验证
Route::post('forgetHandle','LoginController@forgetHandle');

//退出
Route::post('quit','LoginController@quit');
//获取验证码
Route::post('getCode','LoginController@getCode');
//专家列表
Route::get('expert','ExpertController@index');
//专家详情
Route::get('expert/detail','ExpertController@detail');
//专家详情
Route::get('expert/detail/{expertId}','ExpertController@detail');
//供求信息
Route::get('supply','SupplyController@index');
//供求信息详情
Route::get('supply/detail/{supplyId}','SupplyController@detail');

/**************************************收藏留言相关路由*********************************************/
//供求收藏
Route::post('dealcollect','SupplyController@dealCollect');
//供求留言
Route::post('replymessage','SupplyController@replyMessage');
//专家收藏
Route::post('dealextcollect','ExpertController@dealCollect');
//专家留言
Route::post('replyextmessage','ExpertController@replyMessage');

/**************************************个人中心的路由***********************************************/
//基本资料
Route::get('uct_basic','CenterController@index');
//修改手机号
Route::get('uct_basic/changeTel','CenterController@changeTel');
//修改手机号2
Route::get('uct_basic/changeTel2','CenterController@changeTel2');
//修改手机号获取验证码
Route::post('getcodes','CenterController@getcodes');
//修改手机号获取验证码
Route::post('returnCode','CenterController@returnCode');
//更换手机号
Route::post('changeNewPhone','CenterController@changeNewPhone');
//更换密码
Route::get('uct_basic/changePwd','CenterController@changePwd');
//更换密码处理
Route::post('updatePwd','CenterController@updatePwd');
//公共上传图片
Route::any('upload','PublicController@upload');
//基本资料修改
Route::post('changeBasics','CenterController@changeBasics');
//充值提现
Route::get('uct_recharge','CenterController@recharge');
//充值
Route::get('uct_recharge/rechargeMoney','CenterController@rechargeMoney');
//提现
Route::get('uct_recharge/cash','CenterController@cash');
//提现添加银行卡
Route::get('uct_recharge/card','CenterController@card');
//添加银行卡处理
Route::post('cardHandle','CenterController@cardHandle');
//更换银行卡
Route::post('updateCard','CenterController@updateCard');
//验证银行卡
Route::get('uct_recharge/card2','CenterController@card2');
//获取银行卡名称
Route::post('getBankName','PublicController@getBankName');
//验证银行卡处理
Route::post('verifyCard','CenterController@verifyCard');
//提现
Route::post('applicationCashs','CenterController@applicationCashs');
//获取充值记录
Route::post('getRecord','CenterController@getRecord');
//我的信息
Route::get('uct_myinfo','CenterController@myinfo');
//我的需求
Route::get('uct_myneed','CenterController@myNeed');
//需求详情
Route::get('uct_myneed/needDetail/{needid}','CenterController@needDetail');
//发布需求
Route::get('uct_myneed/supplyNeed/{needid?}','CenterController@supplyNeed');
//新增需求
Route::post('uct_myneed/addNeed','CenterController@addNeed');
//解决需求
Route::post('uct_myneed/solveNeed','CenterController@solveNeed');
//审核需求
Route::get('uct_myneed/examineNeed/{needid?}','CenterController@examineNeed');
/************************************我是企业*********************************************************/
//专家资源
Route::get('uct_resource','MyEnterpriseController@resource');
//会员认证1
Route::get('uct_member','MyEnterpriseController@uct_member');
//会员认证2
Route::get('uct_member/member2','MyEnterpriseController@member2');
//会员认证3
Route::get('uct_member/member3','MyEnterpriseController@member3');
//会员认证4
Route::get('uct_member/member4','MyEnterpriseController@member4');
//办事服务
Route::get('uct_works','MyEnterpriseController@works');
//办事详情
Route::get('uct_works/detail/{eventId}','MyEnterpriseController@workDetail');
//申请办事服务1
Route::get('uct_works/applyWork','MyEnterpriseController@applyWork');
//视频咨询
Route::get('uct_video','MyEnterpriseController@video');
//申请视频咨询1
Route::get('uct_video/video1','MyEnterpriseController@video1');
//申请视频咨询2
Route::get('uct_video/video2','MyEnterpriseController@video2');
//申请视频咨询3
Route::get('uct_video/video3','MyEnterpriseController@video3');
//申请视频咨询4
Route::get('uct_video/video4','MyEnterpriseController@video4');

Route::get('uct_resource/resDetail/{expertid}','MyEnterpriseController@resDetail');


/************************************我是专家*********************************************************/
//专家认证
Route::get('uct_expert','MyExpertController@expert');
//专家审核
Route::get('uct_expert2','MyExpertController@expert2');
//审核成功
Route::get('uct_expert3','MyExpertController@expert3');
//专家资料提交
Route::post('uct_expertData','MyExpertController@expertData');
//我的办事
Route::get('uct_mywork','MyExpertController@mywork');
//我的办事详情
Route::get('uct_mywork/workDetail/{eventid}','MyExpertController@workDetail');
//我的咨询
Route::get('uct_myask','MyExpertController@myask');
//我的咨询详情
Route::get('uct_myask/askDetail','MyExpertController@askDetail');
//进入咨询会议
Route::get('uct_myask/myaskinvt','MyExpertController@myaskinvt');