<!DOCTYPE html>
<html lang="en">
<head>
    <!-- 百度统计代码 -->
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?6f6e01e4a95947e6714c0d5ce631597b";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
    <meta name="Keywords" content="2025,升维网,升维,对接资源,转型升级,投融资,企业服务,管理咨询">
    <meta name="description" content="升维网是一个为广大中小型企业与外部专家资源对接提供服务的大型平台。这里汇聚了国际国内大量优秀的专家和资源，通过升维网平台，企业可以向行业专家咨询在经营过程中遇到的相关问题，专家为企业提供最专业的指导服务。"/>
    <meta name="author" content="www.sw2025.com">
    <title>升维网-企业对接高端资源的平台</title>
    <link rel="stylesheet" type="text/css" href="{{asset('iconfont/iconfont.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/global.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/public.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/ucenter.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/workmanage.css')}}" />
    <!-- js / start -->
    <script src="{{asset('im/3rd/jquery-1.11.3.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/jquery.cookie.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/pagination.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/layer/layer.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/public.js')}}"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{asset('js/utils/html5shiv.js?1401441990')}}"></script>
    <script type="text/javascript" src="{{asset('js/utils/respond.min.js?1401441990')}}"></script>
    <![endif]-->
</head>
<body>
<!-- 公共header / start -->
<div class="header">
    <div class="container clearfix">
        <div class="navbar-header clearfix">
            <a href="{{asset('/')}}" class="navbar-brand navbar-link">
                <img src="{{asset('img/logo.png')}}" alt="首页">
            </a>
            <button class="navbar-toggle"><i class="iconfont icon-daohang"></i></button>
        </div>
        <div class="bars">
            <!-- 未登录 -->
                <span class="before-login" style="display:none;">
                    <a href="javascript:;" class="register header-link"><i class="iconfont icon-bianji"></i>注册</a>
                    <a href="javascript:;" class="login header-link"><i class="iconfont icon-suo"></i>登录</a>
                </span>
            <!-- 登录后 -->
                <span class="after-login" style="display:block;">
                    <a href="javascript:;" class="quit header-link">退出</a>
                    <a href="javascript:;" id="toCenter" class="log-username header-link"><i class="iconfont icon-touxiang"></i></a>
                </span>
            <div class="searchbar">
                <a href="javascript:;" class="search-cli"><i class="iconfont icon-sousuo"></i></a>
                <div class="search-sear">
                    <form name="">
                        <div class="select-box">
                            <div class="select-showbox"><span id="select-type"></span><i class="iconfont icon-xiangxiajiantou"></i></div>
                            <ul class="select-option">
                                <li>搜专家</li>
                                <li>搜需求</li>
                            </ul>
                        </div>
                        <input class="search-text" name="keyboard" placeholder="请输入关键字">
                        <span class="search-bar"><button type="button" class="search-btn"><i class="iconfont icon-sousuo"></i></button></span>
                    </form>
                </div>
            </div>
        </div>
        <div class="navbar-nav clearfix">
            <ul class="nav">
                <li id="index"><a href="{{asset('/')}}">首页</a></li>
                <li id="service" ><a href="{{asset('service')}}">服务介绍</a></li>
                <li id="expert" ><a href="{{asset('expert')}}?role=专家&ordertime=desc">专家资源</a></li>
                <li id="supply" ><a href="{{asset('supply')}}">商情信息</a></li>
                <li id="us" ><a href="{{asset('us')}}">关于我们</a></li>
            </ul>
        </div>
    </div>
</div>
<div class="pop-pay">
    <div class="payoff">
        <span class="pay-close" title="关闭"><i class="iconfont icon-chahao"></i></span>
        <div class="single">
            <div class="single-opt pay-opt been">
                <input class="rad-inp" type="radio" id="single" name="charge">
                <div class="opt-label"><span></span>单次缴费：￥500 / 次</div>
            </div>
            <div class="payoff-way">
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="payway1" name="payways">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lweixin.png"><em class="way-cap">微信支付</em></div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="payway2" name="payways">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lzhifubao.png"><em class="way-cap">支付宝支付</em></div>
                    </span>
            </div>
            <button type="button" class="pop-btn">缴 费</button>
            <div class="cub"></div>
        </div>
        <div class="single open-member">
            <div class="single-opt pay-opt">
                <input class="rad-inp" type="radio" id="open" name="charge">
                <div class="opt-label dib"><span></span>开通会员</div>
                <span class="open-right">会员权益</span>
            </div>
            <div class="years payoff-way">
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="oneyear" name="payyear">
                        <div class="opt-label"><span></span>一年&nbsp;&nbsp;￥1000</div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="twoyear" name="payyear">
                        <div class="opt-label"><span></span>两年&nbsp;&nbsp;￥2000</div>
                    </span>
            </div>
            <div class="payoff-way">
                    <span class="pay-opt focus">
                        <input class="rad-inp" type="radio" id="openway1" name="openway">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lweixin.png"><em class="way-cap">微信支付</em></div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="openway2" name="openway">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lzhifubao.png"><em class="way-cap">支付宝支付</em></div>
                    </span>
            </div>
            <button type="button" class="pop-btn">开 通</button>
            <div class="cub" style="display:block"></div>
        </div>
    </div>
</div>
<!-- 公共header / end -->
<div class="ucenter  v-bg5" style="height:920px">
    <div class="wrap clearfix">
        <!-- 侧边栏公共部分/start -->
        <div class="v-aside beexpert">
            <div class="match-fl">
                <a href="{{asset('uct_expert')}}" class="goto-renzh v-personal" title="去认证"><img  class="v-avatar" /><i class="iconfont icon-vip havevip" title="已认证"></i><i class="iconfont icon-vip novip" title="未认证"></i></a>
                <a href="{{asset('basic')}}" class="v-personal" title="个人中心">
                    <span class="v-nick"></span>
                </a>
               {{-- <!-- 我是企业时 -->
                <div class="v-money-info iamenter">
                    <a href="{{asset('uct_recharge')}}" class="v-money" title="充值提现"><i class="iconfont icon-chongzhihetixian"></i></a>
                    <a href="{{asset('uct_myinfo')}}" class="v-info" title="我的消息"><i class="iconfont icon-xiaoxi"></i><span class="v-new-info-tip"></span></a>
                </div>--}}
                <!-- 我是专家时 -->
                <div class="v-money-info iamexpert">
                    <a href="{{asset('recharge')}}" class="v-money" title="充值提现"><i class="iconfont icon-chongzhihetixian"></i></a>
                    <a href="{{asset('uct_standard')}}" class="v-info border2" title="收费标准"><i class="iconfont icon-shenjia"></i></a>
                    <a href="{{asset('uct_myinfo')}}" class="v-info" title="我的消息"><i class="iconfont icon-xiaoxi"></i><span class="v-new-info-tip"></span></a>
                </div>
            </div>
            <div class="v-identity">
                <div class="v-identity-sel">
                    <a href="javascript:;" class="v-identity-default"><span class="v-identity-cap">我是专家</span></a>
                    <ul class="v-identity-list">
                        <li >我是企业</li>
                        <li class="active">我是专家</li>
                    </ul>
                    <div class="v-ucenter-nav">
                        <div class="v-ucenter-nav-list">
                            <div class="mainmenu">
                                <a id="uct_works" href="{{asset('uct_works')}}"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon01.png')}}" alt="办事管理" />
                                    办事管理
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon01.png')}}" alt="办事管理" />
                                    办事管理
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('uct_works').'?domain=找资金'}}">找资金</a></li>
                                    <li><a href="{{url('uct_works').'?domain=找技术'}}">找技术</a></li>
                                    <li><a href="{{url('uct_works').'?domain=定战略'}}">定战略</a></li>
                                    <li><a href="{{url('uct_works').'?domain=找市场'}}">找市场</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_video" href="{{asset('uct_video')}}" class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon02.png')}}" alt="视频会议" />
                                    视频会议
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon02.png')}}" alt="视频会议" />
                                    视频会议
                                </span>
                                <ul class="submenu">
                                    <li><a href="{{url('/uct_video/applyVideo')}}?people=1">发起一对一视频</a></li>
                                    <li><a href="{{url('/uct_video/applyVideo')}}">发起多人会议</a></li>
                                    <li><a href="{{asset('uct_video').'?consultType=单人'}}">查看一对一视频</a></li>
                                    <li><a href="{{asset('uct_video').'?consultType=多人'}}">查看多人会议</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu" >
                                <a id="myshows" href="{{asset('myshows')}}"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon03.png')}}" alt="我的评议" />
                                    我的评议
                                </a>
                                 <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon03.png')}}" alt="我的评议" />
                                    我的评议
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('/myshows').'?index=0'}}">待评议的项目</a></li>
                                    <li><a href="{{url('/myshows').'?index=1'}}">查看全部项目</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_resource" href="{{asset('uct_resource')}}"  class="v-ucenter-nav-item aa">
                                    <span id="expertmsgtome">0</span>
                                    <img src="{{asset('img/vicon03.png')}}" alt="专家资源" />
                                    专家资源
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon03.png')}}" alt="专家资源" />
                                    专家资源
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('/uct_resource').'?action=collect'}}">已收藏的专家</a></li>
                                    <li><a href="{{url('/uct_resource').'?action=message'}}">已留言的专家</a></li>
                                    <li><a href="{{url('/exttomymsg')}}">专家给我的留言</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_myneed" href="{{asset('uct_myneed')}}"   class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon04.png')}}" alt="普通商情" />
                                    普通商情
                                </a>
                                 <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon04.png')}}" alt="普通商情" />
                                    普通商情
                                 </span>
                                <ul class="submenu">
                                    <li><a href="javascript:;" onclick="putneed()">发布商情</a></li>
                                    <li><a href="{{url('/uct_myneed')}}">查看商情</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_myneed2" href="{{asset('uct_myneed2')}}?level=1"   class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon04.png')}}" alt="VIP通道" />
                                    VIP通道
                                </a>
                                 <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon04.png')}}" alt="VIP通道" />
                                    VIP通道
                                 </span>
                                <ul class="submenu">
                                    <li><a href="javascript:;" onclick="putneed()">发布商情</a></li>
                                    <li><a href="{{url('/uct_myneed2').'?level=1'}}">查看商情</a></li>
                                </ul>
                            </div>



                        </div>
                        <div class="v-ucenter-nav-list  v-default">
                            <div class="mainmenu">
                                <a id="uct_mywork" href="{{asset('uct_mywork')}}"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon02.png')}}" alt="我的办事" />
                                    我的办事
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon02.png')}}" alt="我的办事" />
                                    我的办事
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('uct_mywork').'?index=1&domain=找资金'}}">找资金</a></li>
                                    <li><a href="{{url('uct_mywork').'?index=1&domain=找技术'}}">找技术</a></li>
                                    <li><a href="{{url('uct_mywork').'?index=1&domain=定战略'}}">定战略</a></li>
                                    <li><a href="{{url('uct_mywork').'?index=1&domain=找市场'}}">找市场</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_myask" href="{{asset('uct_myask')}}"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon03.png')}}" alt="我的咨询" />
                                    我的咨询
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon03.png')}}" alt="我的咨询" />
                                    我的咨询
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('/uct_myask').'?index=0'}}">待响应的咨询</a></li>
                                    <li><a href="{{url('/uct_myask').'?index=1'}}">查看全部咨询</a></li>
                                    <li><a href="{{url('/uct_myask/uct_linemeetexpert').'?index=0'}}">查看线下约见</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="uct_entres" href="{{asset('uct_entres')}}"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/enterpriseicon.png')}}" alt="企业资源" />
                                    企业资源
                                </a>
                                <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/enterpriseicon.png')}}" alt="企业资源" />
                                    企业资源
                                 </span>
                                <ul class="submenu">
                                    <li><a href="{{url('/uct_entres')}}">已认证企业</a></li>
                                    <li><a href="{{url('/uct_entres/uct_entres2')}}">已注册企业</a></li>
                                </ul>
                            </div>
                            <div class="mainmenu">
                                <a id="myneed" href="{{asset('myneed')}}" {{--title="发布你的商情，搜索你需要的商情"--}} class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon04.png')}}" alt="需求信息" />
                                    商情信息
                                </a>
                                 <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon04.png')}}" alt="商情信息" />
                                    商情信息
                                 </span>
                                <ul class="submenu">
                                    <li><a href="javascript:;" onclick="putneed2()">发布商情</a></li>
                                    <li><a href="{{url('/myneed')}}">查看商情</a></li>
                                </ul>

                            </div>
                            <div class="mainmenu">
                                <a id="myneed2" href="{{asset('myneed2')}}?level=1"  class="v-ucenter-nav-item aa">
                                    <img src="{{asset('img/vicon04.png')}}" alt="VIP通道" />
                                    VIP通道
                                </a>
                                 <span class="v-ucenter-nav-item phone">
                                    <img src="{{asset('img/vicon04.png')}}" alt="VIP通道" />
                                    VIP通道
                                 </span>
                                <ul class="submenu" >
                                    <li><a href="javascript:;" onclick="putneed2()" >向平台提交重要需求</a></li>
                                    <li><a href="{{url('/myneed2').'?level=1'}}" >查看平台推送信息</a></li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 侧边栏公共部分/end -->
        <!-- 企业办事服务 / start -->
        <div class="vmain">
            @yield("content")
        </div>
        <!-- 企业办事服务 / start -->
    </div>
</div>
<!-- 企业办事服务 / end -->
<!-- 公共footer / end -->
<div class="footer">
    <div class="container clearfix">
        <!-- <div class="row"> -->
        <div class="col-md-6 two-pic">
            <!-- <div class="container-fluid"> -->
            <div class="weixin"><span class="two-code"><img src="{{asset('img/weixin.jpg')}}" style="width: 140px;height: 150px;"/></span>关注升维公众号</div>
            <div class="app"><span class="two-code"><img src="{{asset('img/android.png')}}"  style="width: 140px;height: 150px;"/></span>升维APP(安卓)</div>
            <div class="aapp"><span class="two-code"><img src="{{asset('img/ios.png')}}"  style="width: 140px;height: 150px;"/></span>升维APP（IOS）</div>
            <!-- </div> -->
        </div>
        <div class="col-md-6 contacts">
            <p class="footer-title">联系升维<span id="help" style="margin-left: 15px;font-size: 12px">使用帮助</span></p>
            <p class="contact-pub contact-telephone"><i class="iconfont icon-dianhua"></i>Tel：010-64430881&nbsp;&nbsp;/&nbsp;&nbsp;68985908</p>
            <p class="contact-pub contact-email"><i class="iconfont icon-youxiang"></i>E-Mail：shengwei2025@163.com</p>
            <p class="contact-pub contact-addr"><i class="iconfont icon-dizhi"></i>Add：北京市朝阳区安贞里街道浙江大厦</p>
            <p class="copyright">京ICP备17053834号<span></span>copyright &copy; 2017 swchina.com</p>
        </div>
        <!-- </div> -->
    </div>
</div>
<div class="pop-pay">
    <div class="payoff">
        <span class="pay-close" title="关闭"><i class="iconfont icon-chahao"></i></span>
        <div class="pay-tit">咯咯咯咯咯咯咯咯咯咯嘎嘎嘎嘎嘎嘎嘎嘎嘎咕咕咕咕</div>
        <div class="single">
            <div class="single-opt pay-opt been">
                <input class="rad-inp" type="radio" id="single" name="charge">
                <div class="opt-label"><span></span>单次缴费：￥500 / 次</div>
            </div>
            <div class="payoff-way">
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="payway1" name="payways">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lweixin.png"><em class="way-cap">微信支付</em></div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="payway2" name="payways">
                        <div class="opt-label"><span></span><img class="way-img" src="img/lzhifubao.png"><em class="way-cap">支付宝支付</em></div>
                    </span>
            </div>
            <button type="button" class="pop-btn">缴 费</button>
            <div class="cub"></div>
        </div>
        <div class="single open-member">
            <div class="single-opt pay-opt">
                <input class="rad-inp" type="radio" id="open" name="charge">
                <div class="opt-label dibs"><span></span>开通会员</div>
                <span class="open-right">会员权益</span>
            </div>
            <div class="years payoff-way">
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="oneyear" name="payyear">
                        <div class="opt-label"><span></span>一年&nbsp;&nbsp;￥1000</div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="twoyear" name="payyear">
                        <div class="opt-label"><span></span>两年&nbsp;&nbsp;￥2000</div>
                    </span>
            </div>
            <div class="payoff-way">
                    <span class="pay-opt focus">
                        <input class="rad-inp" type="radio" id="openway1" name="openway">
                        <div class="opt-label"><span></span><img class="way-img" src="{{asset('img/lweixin.png')}}"><em class="way-cap">微信支付</em></div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio" id="openway2" name="openway">
                        <div class="opt-label"><span></span><img class="way-img" src="{{asset('img/lzhifubao.png')}}"><em class="way-cap">支付宝支付</em></div>
                    </span>
            </div>
            <button type="button" class="pop-btn">开 通</button>
            <div class="cub" style="display:block"></div>
        </div>
    </div>
</div>
<!-- 公共footer / end -->
<script type="text/javascript">
    if(typeof($.cookie('userId'))=="undefined"){
        window.location.href="{{url('login')}}";
    }
    function putneed2 (){
        $.post('/myneed/verifyputneed',{'role':'专家'},function (data) {
            if(data.type == 3){
                layer.msg(data.msg,{'icon':data.icon});
            } else if(data.type == 2){
                layer.confirm(data.msg, {
                    btn: ['去认证','暂不需要'], //按钮
                    skin:'layui-layer-molv'
                }, function(){
                    window.location.href=data.url;
                }, function(){
                    layer.close();
                });
            } else if (data.type == 1){
                layer.alert(data.msg,{'icon':data.icon});
            } else {
                window.location = '/myneed/supplyNeed';
            }
        });

    }
    $(function(){
        var str=window.location.pathname;
        var num1=str.indexOf('/');
        var find = '/';//表示要找的字符
        var flag = 2;//表示第几次出现
        var num2=0;
        for(var i=0;i<str.length;i++){
            if(str.charAt(i)==find)
                flag--;
            if(flag==0){
                num2=i;
                break;
            }
        }
        if(num2){
            var string=str.substring(num1+1,num2);
        }else{
            var string=str.substring(num1+1);
        }
        $("#"+string).parent().addClass('active');
        if($.cookie('userId')){
            var name=$.cookie("name");
            $(".before-login").hide();
            $(".after-login").show();
            $(".after-login").children(":last").text(name);
        }else{
            $(".before-login").show();
            $(".after-login").hide();
        }
        if($(window).width() > 750){
            $('.mainmenu').hover(function() {
                $(this).children('.submenu').stop().toggle(500).siblings().children('.submenu').hide();
            })
        }else{
            $('.mainmenu').click(function() {
                $('.mainmenu').css('width','42%');
                $(this).addClass('active').siblings().removeClass('active');
                $(this).children('.submenu').stop().show(500);
                $(this).siblings().children('.submenu').hide();
            })
        }
        $.ajax({
            url:"{{asset('getAvatar')}}",
            data:{userId:$.cookie('userId'),type:"expert"},
            dateType:"json",
            type:"POST",
            success:function(res){
                $(".v-avatar").attr('src',"{{env('ImagePath')}}"+res['expertAvatar']);
                $(".v-nick").html(res['phone']);
                if(res['expertRemark']=="success"){
                    $(".havevip").show();
                    $(".novip").hide();
                    $(".goto-renzh").attr("title","已认证");
                }else{
                    $(".havevip").hide();
                    $(".novip").show();
                }
                var date = new Date();
                date.setTime(date.getTime() + (120 * 60 * 1000));
                $.cookie("expertAvatar",res['expertAvatar'],{expires:date,path:'/',domain:'sw2025.com'});
                $.cookie("phone",res['phone'],{expires:date,path:'/',domain:'sw2025.com'});
                $.cookie("expertRemark",res['remark'],{expires:date,path:'/',domain:'sw2025.com'});
            }
        })

        $.ajax({
            url:"{{url('getMessage')}}",
            data:{userId:$.cookie('userId')},
            dateType:"json",
            type:"POST",
            success:function(res){
                if(res['code']=="success"){
                    $(".v-new-info-tip").hide()
                }else{
                    $(".v-new-info-tip").show()
                }
            }
        })
    })
    $(".quit").on("click",function(){
        $.ajax({
            url:"{{asset("quit")}}",
            dateType:"json",
            type:"POST",
            success:function(res){
                if(res['code']=="success"){
                    $.cookie("userId",'',{path:'/',domain:'sw2025.com'});
                    $.cookie("name",'',{path:'/',domain:'sw2025.com'});
                    $.cookie("expertAvatar",'',{path:'/',domain:'sw2025.com'});
                    $.cookie("phone",'',{path:'/',domain:'sw2025.com'});
                    $.cookie("expertRemark",'',{path:'/',domain:'sw2025.com'});
                    window.location.href="{{asset('/')}}"
                }else{
                    window.location.href="{{asset('/')}}"
                }
            }
        })
    })
    $(".search-btn").on("click",function(){
        var type=$("#select-type").text();
        var content=$(".search-text").val();
        if(!type || !content){
            return false;
        }
        if(type=="搜专家"){
            window.location.href="/expert?searchname="+content+"&role=null&supply=null&consult=null&address=null&ordertime=desc&ordercollect=null&ordermessage=null"
        }else{
            window.location.href="/supply?searchname="+content+"&role=null&supply=null&address=null&ordertime=desc&ordercollect=null&ordermessage=null"
        }

    })
    $("#toCenter").on("click",function(){
        if($.cookie('role')=="专家"){
            window.location.href="{{asset('basic')}}"
        }else{
            window.location.href="{{asset('uct_basic')}}"
        }
    })
    $("#help").on("click",function(){
        window.location.href="{{asset('help')}}";
    })
</script>
</body>
</html>

<script src="{{asset('js/notification.js')}}"></script>
<script>createNotification('type')</script>

