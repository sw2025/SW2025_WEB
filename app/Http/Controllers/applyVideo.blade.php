@extends("layouts.ucenter")
@section("content")
    <link rel="stylesheet" href="{{asset('css/events.css')}}">
    <link rel="stylesheet" href="{{asset('css/publishneed.css')}}">
    <link rel="stylesheet" href="{{asset('css/videoconsult.css')}}">
    <script type="text/javascript" src="{{asset('js/laydate/laydate.js')}}"></script>
    <div class="main">
        <!-- 专家视频咨询 / start -->
        <h3 class="main-top">专家视频咨询</h3>
        <div class="ucenter-con">
            <div class="main-right">
                <div class="card-step works-step">
                    <span class="green-circle">1</span>会议申请<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">2</span>邀请专家<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">3</span>专家响应<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">4</span>会议管理<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">5</span>完成
                </div>
                <div class="invite-experts apply-video-wrapper">
                    <table class="invite-table">
                        <tr>
                            <td>问题分类</td>
                            <td>
                                <div class="publish-need-sel zindex4">
                                    {{--<span class="publ-need-sel-cap">问题分类</span>--}}<a href="javascript:;" class="publ-need-sel-def" id="select1">请选择</a>
                                    <ul class="publish-need-list" style="display: none;">
                                        @foreach($cate as $v)
                                            @if($v->level == 1)
                                                <li>
                                                    <a href="javascript:;">{{$v->domainname}}</a>
                                                    <ul class="publ-sub-list">
                                                        @foreach($cate as $small)
                                                            @if($small->parentid == $v->domainid && $small->level == 2)
                                                                <li>{{$small->domainname}}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr>
                        <td>会议模式</td>
                            <td>
                                <div class="modals publish-need-sel">
                                    <a href="javascript:;" class="modal-choose" id="videoType">多人</a>
                                    <ul class="modals-list">
                                        <li>单人</li>
                                        <li>多人</li>
                                    </ul>
                                </div>
                            </td>
                        <tr>
                        <tr>
                            <td>会议议题</td>
                            <td>
                                <textarea name="" class="publish-need-txt uct-works-txt" cols="30" rows="10" placeholder="请输入会议议题"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td>起止时间</td>
                            <td>
                                <div class="calendar">
                                    <div class="calendar-start clearfix">
                                        <span>开始时间</span><span class="calendar-date laydate-icon start" id="start"></span>
                                    </div>
                                    <div class="calendar-end clearfix">
                                        <span>会议时间</span>
                                        <div class="modals publish-need-sel" style="margin-left:15px;width: 237px">
                                            <a href="javascript:;" class="modal-choose" id="endTime">20</a>
                                            <ul class="modals-list">
                                                <li>20</li>
                                                <li>30</li>
                                            </ul>
                                        </div>
                                        <span style="margin-left: 16px;float: right;">分钟</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>专家</td>
                            <td>
                                <div class="uct-works-exp">
                                    <a href="javascript:;" class="system-btn uct-works-btn active" id="random" style="padding:0 10px;">系统分配专家</a>
                                    <a href="javascript:;" class="uct-works-btn" id="appoint">指定专家</a>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>专家头像</td>
                            <td>
                                <ul class="uct-works-expava">
                                </ul>
                            </td>
                        </tr>
                    </table>
                    <div class="uct-works-tips">
                        <b>提示</b><br />
                        尊敬的用户您好
                        <p class="uct-works-tips-para light-color">近期，网监部门查敏感类信息比较严格，所以内容中多加了一些类似“共产党”等政治性文字的敏感词语类或其它敏感词汇信息需要验证，请您按照文明规范填写办事内容。</p>
                    </div>
                    <div class="uct-works-con">
                        <button class="test-btn submit-audit" type="button">请专家开会</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .layer_notice {
            float: left;
            overflow: hidden;
            background: #1e8e8e;
            padding: 10px;
        }
        .layer_image {
            float: left;
            overflow: hidden;
            background: #1e8e8e;
            padding: 10px;
        }
        .layer_notice a {
            color: #fff;
        }

        .layer_image a {

            float: left;
            margin: 0 5px;
        }
        .layer_image img {
            border: 2px solid #ccc;
            width: 100px;
            height: 100px;
            border-radius: 65px;
        }
        .layer_image span {
            color:#fff;
        }
        .changeWeixin img{
            margin:0 auto;
        }
    </style>
    <ul class="layer_notice" style="display: none;">
        <li><a>近期，网监部门查敏感类信息比较严格，所以内容中多加了一些类似“共产党”等政治性文字的敏感词语类或其它敏感词汇信息需要验证，请您按照文明规范填写办事内容。</a></li>
        <li><a>感谢您的合作</a></li>
        <li><a style="margin-left: 80%;">升维网</a></li>
    </ul>
    <ul class="layer_image" style="display: none;">
        <p style="color: #fff;padding: 5px 20px;"></p>
        <li>
        </li>
    </ul>
    <div class="pop-pay">
        <div class="payoff">
            <span class="pay-close"  title="关闭"><i class="iconfont icon-chahao"></i></span>
            <div class="single">
                <div class="single-two">
                <span class="single-opt pay-opt" id="singlePay">
                    <input class="rad-inp" type="radio" style="width: 12%;">
                    <div class="opt-label normal "></div>
                </span>
                </div>
                <div class="cub" style="display:block"></div>

            </div>
            <div class="single open-member">
                <div class="single-opt pay-opt been">
                    <input class="rad-inp" type="radio">
                    <div class="opt-label dibs"><span></span>开通会员</div>
                    <span class="open-right">会员权益</span>
                </div>
                <div class="years">
                    @foreach($memberrights as $memberright)
                        <span class="pay-opt" memberId="{{$memberright->memberid}}">
                        <input class="rad-inp" type="radio" >
                        <div class="opt-label"><span></span>{{$memberright->termtime}}年&nbsp;&nbsp;￥{{$memberright->cost}}<em class="benifit">优惠次数 {{$memberright->eventcounts}} 次</em><em class="benifit">优惠时间 {{$memberright->consultcounts}} 分钟</em></div>
                    </span>
                    @endforeach
                </div>

                <div class="cub"></div>
            </div>
            <div class="paytype payoff-way">
                    <span class="pay-opt focus been">
                        <input class="rad-inp" type="radio"  value="wx_pub_qr">
                        <div class="opt-label"><span></span><img class="way-img" src="{{asset('img/lweixin.png')}}"><em class="way-cap">微信支付</em></div>
                    </span>
                    <span class="pay-opt">
                        <input class="rad-inp" type="radio"  value="alipay_pc_direct">
                        <div class="opt-label"><span></span><img class="way-img" src="{{asset('img/lzhifubao.png')}}"><em class="way-cap">支付宝支付</em></div>
                    </span>
            </div>
            <div style="text-align: center;padding: 0 0 20px;"><button type="button" class="pop-btn vip" id="vip">付费</button></div>
        </div>
    </div>
    <div class="layer-pop" style="position:fixed;background: rgba(0,0,0,0.3);top: 0;left: 0;width: 100%;height: 100%;z-index: 1000;display: none;">
        <div class="popWx" style="position: absolute;top: 10%;width: 285px;border: 2px solid #ccc;left: 50%;top: 50%;margin: -160px 0 0 -145px;background: #fff;text-align: center;border-radius: 3px;font-size: 14px;padding: 30px 0 27px;">
            <div class="changeWeixin">
                <div class="popWeixin" id="code">
                </div>
            </div>
            <span class="weixinLittle"></span>
            <div class="weixinTips" style="display: none"><strong>扫瞄二维码完成支付</strong><br>支付完成后请关闭二维码</div>
            <a href="javascript:;" class="closePop" title="关闭" style="position: absolute;top: 0;right: 0;"><i class="iconfont icon-chahao"></i></a>
        </div>
    </div>
    <script type="text/javascript" src="{{url('/js/jquery.qrcode.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/qrcode.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/js/pingpp.js')}}"></script>
    <script type="text/javascript">

            var a = window.location.search;
            if(a=='?people=1'){
                $('#videoType').html('单人');
            }

        $(function(){
            $('.closePop').click(function () {
                $(this).closest('.layer-pop').hide();
                $('.pop-pay').hide();
                $(".submit-audit").attr('disabled',false);
                $(".submit-audit").html('请专家开会');
            })
            $('.datas-sel-def').click(function () {
                $(this).next('ul').stop().slideToggle();
                $(this).parent().siblings().children('ul').hide();
            });
            $('.datas-list li').click(function () {
                var publishHtml = $(this).html();
                $(this).parent().prev('.datas-sel-def').html(publishHtml);
                $(this).parent().hide();
            });
            if($.cookie("videodomain") != "请选择" && $.cookie("videodomain") != ""){
                $(".publ-need-sel-def").text($.cookie("videodomain"));
            }
            if($.cookie("videodescribe")){
                $(".uct-works-txt").val($.cookie("videodescribe"));
            }
            if($.cookie("videoType")){
                $("#videoType").text($.cookie("videoType"));
            }
            if($.cookie("videodateStart")){
                $("#start").text($.cookie("videodateStart"));
            }
            if($.cookie("videodateEnd")){
                $("#endTime").text($.cookie("videodateEnd"));
            }
            $('.modal-choose').click(function(){
                $(this).next().stop().slideToggle();
            })
            $('.modals-list li').click(function(){
                var liHtml = $(this).html();
                $(this).parent().hide();
                $(this).parent().prev().html(liHtml);
            })
            if($.cookie("videoreselect")){
                $(".uct-works-expava").show();
                var expertChecked=$.cookie('videoreselect').split(",");
                for(var i=0; i<expertChecked.length; i++) {
                    var checked=expertChecked[i];
                    var end=checked.indexOf("/");
                    var id=checked.substring(0,end);
                    var img=checked.substring(end);
                    var str="<input type='hidden' name=expertId[] value="+id+"><img src={{env('ImagePath')}}"+img+" class='uct-works-exp-img' id="+id+" />"
                    $(".uct-works-expava").append(str);
                }
                $("#appoint").addClass('active');
                $("#random").removeClass('active')
            }
            if($.cookie('state')){
                if($.cookie('state')==1){
                    $("#appoint").removeClass('active');
                    $("#random").addClass('active')
                }else{
                    $("#appoint").addClass('active');
                    $("#random").removeClass('active')
                }
            }
            $('.publ-need-sel-def').click(function() {
                $(this).next('ul').stop().slideToggle();
            });
            $('.publish-need-list li').hover(function() {
                $(this).children('ul').stop().show();
            }, function() {
                $(this).children('ul').stop().hide();
            });

            $('.publ-sub-list li').click(function() {
                var publishHtml = $(this).html();
                var parentHtml = $(this).parent().siblings('a').text();
                $('.publ-need-sel-def').html(parentHtml+'/'+publishHtml);
                $('.publish-need-list').hide();
            });

            $('.uct-works-exp a').click(function(event) {
                if($('#select1').text().trim() == '请选择'/* || $('#industrys').text().trim() == '请选择'*/){
                    layer.msg('请选择问题分类');
                    return false;
                }
                $(this).addClass('active').siblings().removeClass('active');
                var domain=$(".publ-need-sel-def").text();
                var describe=$(".uct-works-txt").val();
                var videoType=$("#videoType").text();
                /*var industry=$("#industrys").text();*/
                var dateStart=$('#start').text();
                var dateEnd=$("#endTime").text();
                var text=$(this).text().trim();
                var date = new Date();
                date.setTime(date.getTime() + (120 * 60 * 1000));
                if(text=="系统分配专家"){
                    layer.msg('系统为您检索专家中', {
                        icon: 16
                        ,shade: 0.01
                    });
                    $(".uct-works-expava").hide();
                    $(".submit-audit").attr('disabled',true);
                    $(".submit-audit").css('background-color','#ccc');
                    $.post('{{url('matchingexpert')}}',{'domain':$('#select1').text().trim()},function (data) {
                        if(data.type == 4){
                            layer.msg(data.msg,{'icon':2},function () {
                                window.location.href="/"
                            });
                        } else if (data.type == 1){
                            layer.alert(data.msg,{'title':'尊敬的用户您好'});
                            $(".submit-audit").attr('disabled',false);
                            $(".submit-audit").css('background-color','#ed0021');
                        } else if (data.type == 2){
                            layer.open({
                                type: 1,
                                skin: 'layui-layer-rim', //加上边框
                                area: ['500px', '260px'],
                                shadeClose: false, //开启遮罩关闭
                                content: '<div style="padding:10px;">'+data.msg+'</div>',
                                btn: ['自选专家','继续操作','重新咨询'],
                                yes: function(index, layero){
                                    if($.cookie('videoreselect')){
                                        var selected=$.cookie('videoreselect').split(",");
                                        if(selected.length==5){
                                            $(".uct-works-expava").show();
                                        }else{
                                            $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                                            $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                                            $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                                            $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                                            $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                                            window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                                        }
                                    }else{
                                        $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                                        window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                                    }
                                },btn2: function(index, layero){
                                    $(".submit-audit").attr('disabled',false);
                                    $(".submit-audit").css('background-color','#ed0021');
                                    layer.close(index);
                                },btn3: function(index, layero){
                                    $.cookie("videoreselect","",{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videodomain","",{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videoType","",{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videodescribe","",{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videoindustry","",{expires:date,path:'/',domain:'swchina.com'});
                                    window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                                }
                            });

                        } else if (data.type == 3){
                            layer.confirm(data.msg, {
                                btn: ['自选专家','取消该办事'] //按钮
                            }, function(){
                                if($.cookie('videoreselect')){
                                    var selected=$.cookie('videoreselect').split(",");
                                    if(selected.length==5){
                                        $(".uct-works-expava").show();
                                    }else{
                                        $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                                        $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                                        window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                                    }
                                }else{
                                    $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                                    $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                                    window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                                }
                            }, function(){
                                $.cookie("videoreselect","",{expires:date,path:'/',domain:'swchina.com'});
                                $.cookie("videodomain","",{expires:date,path:'/',domain:'swchina.com'});
                                $.cookie("videoType","",{expires:date,path:'/',domain:'swchina.com'});
                                $.cookie("videodescribe","",{expires:date,path:'/',domain:'swchina.com'});
                                $.cookie("videodateStart","",{expires:date,path:'/',domain:'swchina.com'});
                                $.cookie("videodateEnd","",{expires:date,path:'/',domain:'swchina.com'});
                                /*$.cookie("videoindustry","",{expires:date,path:'/',domain:'swchina.com'});*/
                                window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd;
                                return false;

                            });
                        }
                    });
                }else{
                    if($.cookie('videoreselect')){
                        var selected=$.cookie('videoreselect').split(",");
                        if(selected.length==5){
                            $(".uct-works-expava").show();
                        }else{
                            $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                            $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                            $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                            $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                            $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                            /*$.cookie("videoindustry",industry,{expires:date,path:'/',domain:'swchina.com'});*/
                            window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                        }
                    }else{
                        $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
                        /*$.cookie("videoindustry",industry,{expires:date,path:'/',domain:'swchina.com'});*/
                        window.location.href="/uct_video/videoSelect?start="+dateStart+"&end="+dateEnd
                    }
                }

            });
        })

        $(".submit-audit").on("click",function(){
            var that=this;
            var domain=$(".publ-need-sel-def").text().trim();
            var describe=$(".uct-works-txt").val();
            /*var industry=$("#industrys").text().trim();*/
            var dateStart=$('#start').text().trim();
            var dateEnd=$("#endTime").text().trim();
            var isAppoint=($.cookie("videoisAppoint"))?$.cookie("videoisAppoint"):1;
            var expertIds= $("input[name='expertId[]']").map(function(){return $(this).val()}).get().join(",");
            var videoType=$("#videoType").text();
            if(videoType=="单人"){
                videoType="0";
            }else{
                videoType="1";
            }
            // console.log(expertIds);
            if(describe.length>30 && describe.length<500){
            }else{
                $(this).attr('disabled',false);
                $(this).html('请专家开会');
                layer.msg('企业简介字数不符',{'icon':5});
                return false;
            }
            if($("#random").hasClass('active')){
                var state=1;
            }else{
                var state=0;
            }
            if(domain=="请选择"){
                layer.tips("问题分类不能为空", '.publ-need-sel-def', {
                    tips: [2, '#00a7ed'],
                    time: 4000
                });
                return false;
            }
            if(!describe){
                layer.tips("会议议题不能为空", '.uct-works-txt', {
                    tips: [2, '#00a7ed'],
                    time: 4000
                });
                return false;
            }
            if(!dateStart){
                layer.tips("开始时间必填", '.start', {
                    tips: [2, '#00a7ed'],
                    time: 4000
                });
                return false;
            }
            if(!dateEnd){
                layer.tips("结束时间必填", '.end ',{
                    tips: [2, '#00a7ed'],
                    time: 4000
                });
                return false;
            }
            if(!$('.uct-works-exp a').hasClass('active')){
                layer.tips("请选择系统匹配还是自选专家", '.uct-works-exp', {
                    tips: [2, '#00a7ed'],
                    time: 4000
                });
                return false;
            }
            var date = new Date();
            date.setTime(date.getTime() + (120 * 60 * 1000));
            $.cookie("videodomain",domain,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("videodescribe",describe,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("videoType",videoType,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("videodateStart",dateStart,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("videodateEnd",dateEnd,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("videoreselect",expertIds,{expires:date,path:'/',domain:'swchina.com'});
            $.cookie("state",state,{expires:date,path:'/',domain:'swchina.com'});
            $(this).attr('disabled',true);
            $(this).html('正在提交');
            $.ajax({
                url:"{{asset('consultCharge')}}",
                data:{"domain":domain,"describe":describe,"isAppoint":isAppoint,"expertIds":expertIds,"state":state,"dateStart":dateStart,"dateEnd":dateEnd,"videoType":videoType},
                dateType:"json",
                type:"POST",
                success:function(res){
                    var date = new Date();
                    date.setTime(date.getTime() + (120 * 60 * 1000));
                    if(res['icon'] == 1){
                        $.cookie("videoreselect","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodomain","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videoType","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodescribe","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodateStart","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("videodateEnd","",{expires:date,path:'/',domain:'swchina.com'});
                        $.cookie("state","",{expires:date,path:'/',domain:'swchina.com'});
                        /*$.cookie("videoindustry","",{expires:date,path:'/',domain:'swchina.com'});*/
                        if(state == 0){
                            /*layer.msg(res.msg,{'icon':6,'time':5000},function () {
                                window.location = '{{url('uct_video')}}';
                            });*/
                            layer.open({
                                type: 1,
                                shade: 0.6,
                                title: '已为您推送到指定专家,30秒后自动跳转', //不显示标题
                                content: '<div style=padding:10px;background:#5FB878;color:#fff;>'+res.msg+'</div>', //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
                                time:30000,
                                cancel: function(index,layero){
                                    window.location = '{{url('uct_video')}}';
                                },
                                end:function () {
                                    window.location = '{{url('uct_video')}}';
                                }

                            });
                        } else {
                            var str = '';
                            var obj = res.expertsinfo;
                            for(var i=0;i<obj.length;i++){
                                str += '<a href={{url("expert/detail")}}/'+obj[i]['expertid']+' target="_blank"><img src="{{env('ImagePath')}}'+obj[i]['showimage']+'"><span>'+obj[i]['expertname']+'</span></a>';
                            }
                            $('.layer_image li').append(str);
                            $('.layer_image p').append(res.msg);
                            layer.open({
                                type: 1,
                                shade: false,
                                area: ['695px', '240px'], //宽高
                                title: '恭喜您，以为您推送到专家，以下的是专家的相关信息', //不显示标题
                                content: $('.layer_image'), //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
                                cancel: function(){
                                    window.location.href="{{asset('uct_video')}}";
                                }
                            });
                        }
                    }else if(res['icon'] == 2){
                        $(".publ-need-sel-def").text(domain);
                        $(".uct-works-txt").val(describe);
                        $("#start").text(dateStart);
                        $("#endTime").text(dateEnd);
                        layer.confirm(res.msg+'申请失败,请重新申请', {
                            btn: ['确定'] //按钮
                        });
                        $(that).removeAttr('disabled');
                        $(that).html('请专家开会');
                    }else if(res['icon'] == 3){
                        layer.confirm(res.msg, {
                            btn: ['确定','取消'] ,
                            skin: 'layer-ext-moon',
                            icon:0,
                        }, function(index){
                            var str;
                            if(res['code']==2){
                                window.location.href="/"+res['url'];
                            }
                            if(res['code']==6){
                                str="<span></span>单次缴费：￥<b class='money'>{{env('consultMemberMoney')}}</b>/{{env('Time')}}分钟 &nbsp;&nbsp;&nbsp;&nbsp;充值时间 <input type='number' class='re-counts times'  min='1' style='border: 1px solid #ccc;padding-left: 10px;box-sizing:border-box;width: 140px;'>"
                            }else{
                                str="<span></span>单次缴费：￥<b class='money'>{{env('Money')}}</b>/{{env('Time')}}分钟 &nbsp;&nbsp;&nbsp;&nbsp;充值时间 <input type='number' class='re-counts times'  min='1' style='border: 1px solid #ccc;padding-left: 10px;box-sizing:border-box;width: 140px;'>"
                            }
                            pop(str);
                            layer.close(index);
                        }, function(index){
                            $(that).attr('disabled',false);
                            $(that).html('请专家开会');
                            layer.close(index);
                        });
                    }
                }
            })
        })

        $("#vip").on("click",function(){
            var payType;
            var memberId;
            var channel;
            var amount;
            var consultCount;
            var urlType=window.location.href;
            var res=$("#singlePay").hasClass("been");
            if(res){
                payType="payMoney";
                var money=$("#singlePay").find(".money:first").text();
                consultCount=$("#singlePay").find(".times").val();
                if(consultCount<1){
                    alert("请填写充值时间");
                    return false;
                }
                amount=amounts*consultCount;
                memberId=0;
            }else{
                payType="member";
                $(".years").children().each(function(){
                    if($(this).hasClass('juniorbe')){
                        memberId=$(this).attr("memberId");
                    }
                })
                amount=0;
                consultCount=0;
            }
            $(".payoff-way").children().each(function(){
                if($(this).hasClass('been')){
                    channel=$(this).children(":first").attr("value");
                }
            });
            $.ajax({
                url:"{{url('charge')}}",
                data:{"payType":payType,"memberId":memberId,"channel":channel,"amount":amount,"type":"consult","consultCount":consultCount,"urlType":urlType},
                dateType:"json",
                type:"POST",
                success:function(res){
                    var charge =JSON.parse(res);
                    console.log(charge);
                    $('#code').empty();
                    if(charge.credential.wx_pub_qr){
                        var qrcode = new QRCode('code', {
                            text: charge.credential.wx_pub_qr,
                            width: 200,
                            height: 200,
                            colorDark : '#000000',
                            colorLight : '#ffffff',
                            correctLevel : QRCode.CorrectLevel.H
                        });
                        console.log(qrcode);
                        if(channel=="wx_pub_qr"){
                            $('.poplayer').show();
                            $('.layer-pop').show();
                            $(".weixinTips").show();
                        }
                        return;
                    }
                    pingpp.createPayment(charge, function(result, err){
                        // console.log(result);
                        // console.log(err.msg);
                        // console.log(err.extra);
                        if (result == "success") {
                            // 只有微信公众账号 wx_pub 支付成功的结果会在这里返回，其他的支付结果都会跳转到 extra 中对应的 URL。
                        } else if (result == "fail") {
                            // charge 不正确或者微信公众账号支付失败时会在此处返回
                        } else if (result == "cancel") {
                            // 微信公众账号支付取消支付
                        }
                    });
                }
            })

        })
        // =========日期插件使用方法======>start
        !function(){
            laydate.skin('danlan');//切换皮肤，请查看skins下面皮肤库
        }();
        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY-MM-DD hh:mm',
            min: '{{date('Y-m-d H:i')}}', //设定最小日期为当前日期
            max: '2066-12-31 23:59', //最大日期
            istime: true,
            istoday: false,
            choose: function(datas){
               // end.min = datas; //开始日选好后，重置结束日的最小日期
               // end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY-MM-DD hh:mm',
            min: '{{date('Y-m-d H:i')}}',
            max: '2066-12-31 23:59',
            istime: true,
            istoday: false,
            choose: function(datas){
                start.max = datas; //结束日选好后，充值开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);
        // ========日期插件使用方法======>end
    </script>
@endsection