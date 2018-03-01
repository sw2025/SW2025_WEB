@extends("layouts.master")
@section("content")
<!-- banner / start -->
<link rel="stylesheet" type="text/css" href="{{asset('css/index.css')}}" />
<div class="banner">
    <ul>
        {{--<li class="img01"><img src="img/banner1.jpg" /></li>
        <li class="img02"><img src="img/banner2.jpg" /></li>
        <li class="img03"><img src="img/banner3.jpg" /></li>--}}
        <li class="img01"></li>
        <li class="img02"></li>
        <li class="img03"></li>
    </ul>
    <ol>
        <li class="cur"></li>
        <li></li>
        <li></li>
    </ol>
    <a href="javascript:;" class="leftBtn btn"><i class="iconfont icon-jiantou1"></i></a>
    <a href="javascript:;" class="rightBtn btn"><i class="iconfont icon-jiantou2"></i></a>
</div>
<!-- banner / end -->
<!-- section1 / start -->
<div class="section bg-white">
    <div class="container">
        <div class="supply-service-tit">升维网为企业提供的服务<span class="long-line"></span></div>
        <div class="supply-service-entit">{{--SERVICE PROVIDED BY SHENGWEI FOR ENTERPRISES--}}</div>
        <ul class="clearfix">
            <li class="item col-md-3 col-xs-6"><a href="javascript:;">
                    <div class="item-con dif-hexagon2">
                        <span class="hexagon"></span>
                        <h2 class="number">01</h2>
                        <span class="item-tit">创业孵化</span>
                        {{--<p class="item-desc">解决企业遇到各类问题</p>--}}
                    </div>
                    <div class="item-con-hover">
                       <p><br /><span  class="supplyShow" style="font-size: 1.5em;">项目评议</span><br /><br /><br /><span class="linemeet" style="font-size: 1.5em;">约见投资人</span><br /><br /><br /><span class="hatch" style="font-size: 1.5em;">深度孵化</span><br /><br /></p>
                    </div>
                </a></li>
            <li class="item col-md-3 col-xs-6"><a href="javascript:;">
                    <div class="item-con dif-hexagon4">
                        <span class="hexagon"></span>
                        <h2 class="number">02</h2>
                        <span class="item-tit">成长企业</span>
                        {{--<p class="item-desc">解决企业遇到各类问题</p>--}}
                    </div>
                    <div class="item-con-hover">
                        <p><br /><span style="font-size: 1.5em;">风险投资</span><br /><br /><br /><span style="font-size: 1.5em;">协同营销</span><br /><br /><br /><span style="font-size: 1.5em;">综合运营</span><br /><br /></p>
                    </div>
                </a></li>
            <li class="item col-md-3 col-xs-6"><a href="javascript:;">
                    <div class="item-con dif-hexagon3">
                        <span class="hexagon"></span>
                        <h2 class="number">03</h2>
                        <span class="item-tit">转型升级</span>
                    </div>
                    <div class="item-con-hover">
                        <p><br /><span style="font-size: 1.5em;">方向评价</span><br /><br /><br /><span style="font-size: 1.5em;">项目评价</span><br /><br /><br /><span style="font-size: 1.5em;">众筹</span><br /><br /></p>
                    </div>
                </a></li>
            <li class="item col-md-3 col-xs-6"><a href="{{'service'}}">
                    <div class="item-con dif-hexagon1">
                        <span class="hexagon"></span>
                        <h2 class="number">04</h2>
                        <span class="item-tit">更多服务</span>
                    </div>
                </a></li>
        </ul>
        <div class="listbottom-link"><a href="javascript:;" class="become-expert homepage-link">入驻平台</a></div>
    </div>
</div>
<!-- section1 / end -->
<!-- section2 / start -->
<div class="section fix-bg">
    <div class="container">
        <div class="supply-service-tit supply-demand">专家资源库<span class="middle-line"></span></div>
        <div class="supply-service-entit">{{--EXPERT RESOURCE BASE--}}</div>
        <div class="more-box"><a href="{{asset('expert')}}?role=专家&ordertime=desc" class="more">更多<i class="iconfont icon-rilijiantouyoushuang"></i></a></div>
        <div class="row tab-resources">
            <div class="tabar clearfix" id="knowExpert">
                <a href="javascript:;" style="width: 50%;" class="tabar-opt">资深专家<div class="triangle-top"></div></a>
                {{--<a href="javascript:;" class="tabar-opt">成功企业家<div class="triangle-top"></div></a>--}}
                <a href="javascript:;" style="width: 50%;" class="tabar-opt">知名机构<div class="triangle-top"></div></a>

            </div>
            <div class="tab-con">
                <ul class="tab-list clearfix" id="konwExpertList">

                </ul>
            </div>
            <div class="listbottom-link nomargin"><a href="javascript:;" class="become-expert homepage-link">成为专家</a></div>
        </div>
        <a href="javascript:;" class="tab-leftbtn"><i class="iconfont icon-jiantou1"></i></a>
        <a href="javascript:;" class="tab-rightbtn"><i class="iconfont icon-jiantou2"></i></a>
    </div>
</div>
<!-- section2 / end -->
<!-- section3 / start -->
<div class="section">
    <div class="container clearfix">
        <div class="supply-service-tit supply-demand">商情信息<span class="short-line"></span></div>
        <div class="supply-service-entit">{{--SUPPLY AND DEMAND INFORMATION--}}</div>
        <div class="row supply-categary clearfix">
            <div class="col-md-6">
                <div class="demands">
                    <h2 class="category">资金需求<a href="{{asset('supply')}}" class="more">更多<i class="iconfont icon-rilijiantouyoushuang"></i></a></h2>
                    <ul class="demands-list">
                        @foreach($invests as $invest)
                        <li><i class="iconfont icon-jiantou"></i><a href="{{asset('supply/detail/'.$invest->needid)}}"><span class="dem-li-tit">{{$invest->brief}}</span><span class="dem-li-time"><i class="iconfont icon-shijian2"></i>{{$invest->created_at}}</span></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="demands">
                    <h2 class="category">战略与管理<a href="{{asset('supply')}}" class="more">更多<i class="iconfont icon-rilijiantouyoushuang"></i></a></h2>
                    <ul class="demands-list">
                        @foreach($works as $work)
                        <li><i class="iconfont icon-jiantou"></i><a href="{{asset('supply/detail/'.$work->needid)}}"><span class="dem-li-tit">{{$work->brief}}</span><span class="dem-li-time"><i class="iconfont icon-shijian2"></i>{{$work->created_at}}</span></a></li>
                       @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="demands">
                    <h2 class="category">找技术<a href="{{asset('supply')}}" class="more">更多<i class="iconfont icon-rilijiantouyoushuang"></i></a></h2>
                    <ul class="demands-list">
                        @foreach($products as $product)
                        <li><i class="iconfont icon-jiantou"></i><a href="{{asset('supply/detail/'.$product->needid)}}"><span class="dem-li-tit">{{$product->brief}}</span><span class="dem-li-time"><i class="iconfont icon-shijian2"></i>{{$product->created_at}}</span></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="demands">
                    <h2 class="category">拓市场<a href="{{asset('supply')}}" class="more">更多<i class="iconfont icon-rilijiantouyoushuang"></i></a></h2>
                    <ul class="demands-list">
                        @foreach($markets as $market)
                        <li><i class="iconfont icon-jiantou"></i><a href="{{asset('supply/detail/'.$market->needid)}}"><span class="dem-li-tit">{{$market->brief}}</span><span class="dem-li-time"><i class="iconfont icon-shijian2"></i>{{$market->created_at}}</span></a></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="listbottom-link"><a href="javascript:;" class="become-expert homepage-link">发布商情</a></div>
    </div>
</div>
<!-- section3 / end -->
<script>

    $(function(){
        var type=$("#knowExpert a:first-child").text();
        $("#knowExpert a:first-child").addClass("current")
        getType(type)
    })
    $("#knowExpert a").on("click",function(){
        $("#konwExpertList").empty();
        $("#konwExpertList").css('left','0%');
        var expertType= $(this).text();
        getType(expertType);
    })
    var getType=function(type){
        $.ajax({
            url:"{{asset('returnData')}}",
            data:{"type":type},
            dateType:"json",
            type:"POST",
            success:function(res){
                if(res['code']=="success"){
                    $.each(res['msg'],function(key,value){
                        var str=" <li class=''>";
                            str+="<a href='{{asset('expert/detail/')}}"+"/"+value.expertid+"'class='tab-list-link'>";
                            str+="<span class='ex-res-img'><img src={{env('ImagePath')}}"+value.showimage+" /></span>";
                            str+="<div class='ex-res-con'>";
                            str+="<div class='triangle-bottom'></div>"
                            str+="<div class='ex-res-con-tit'>"
                            str+="<span class='expert-name'>"+value.expertname+"</span>"
                            str+="<span class='expert-consult'>视频咨询："+value.fee +"</span></div><div class='ex-res-con-desc'>"
                            str+="<span class='expert-field'><strong>擅长领域：</strong>"+value.domain1+"</span>"
                            str+="<p class='expert-field-desc'>"+value.brief+"</p>"
                            str+="</div> </div> </a><div class='ex-res-icon'>"
                            str+="<a href='javascript:;' class='review' id='"+value.expertid+"' onclick='toMessage(this)'><i class='iconfont icon-pinglun1'></i></a>"
                            if(value.collect==0){
                                str+="<a href='javascript:;' class='collect ' title='收藏' id='"+value.expertid+"' onclick='collect(this)'><i class='iconfont icon-likeo'></i></a> </div></li>";
                            }else{
                                str+="<a href='javascript:;' class='collect red ' title='已收藏' id='"+value.expertid+"' onclick='collect(this)'><i class='iconfont icon-likeo'></i></a> </div></li>";
                            }
                        $("#konwExpertList").append(str);
                    })
                }else{
                   /* $("#konwExpertList").text("暂无数据");*/
                }
            }

        })
    }
   function collect(e){
        var expertId=$(e).attr('id');
        var remark;
       if(!$.cookie("userId")){
           window.location.href="{{asset('login')}}"
           return false;
       }else{
           if($(e).attr('title') == '已收藏'){
               remark="0";
               collectHndle(expertId,remark);
               $(e).attr("title","收藏");
               $(e).removeClass('red');
           }else{
               remark="1";
               collectHndle(expertId,remark);
               $(e).attr("title","已收藏");
               $(e).addClass('red');
           }
       }
   }
    var collectHndle=function(expertId,remark){
            $.ajax({
                url:"{{asset('collectExpert')}}",
                data:{"remark":remark,"expertId":expertId},
                dateType:"json",
                type:"POST",
                success:function(res){
                    if(res['code']=="error"){
                        alert("收藏失败!")
                        return false;
                    }
                }
            })
        }
    function toMessage(e){
        var expertId=$(e).attr('id');
        if(!$.cookie('userId')){
            window.location.href="{{asset('login')}}"
            return false;
        }else{
            window.location.href="{{asset('expert/detail')}}"+"/"+expertId;
        }
    }
    var  getPath=function(type){
        if(!$.cookie('userId')){
            window.location.href="/login"
            return false;
        }
        switch(type){
            case "入驻平台":
                    window.location.href="{{asset('uct_works')}}";
            break;
            case "成为专家":
                window.location.href="{{asset('uct_expert')}}";
                break;
            case "发布商情":

                layer.confirm('请问您以什么身份发起商情？', {
                    btn: ['以企业身份发起','以专家身份发起','取消'], //按钮
                    yes: function(index, layero){
                        $.post('{{url('myneed/verifyputneed')}}',{'role':'企业'},function (data) {
                            if(data.type == 3 || data.type == 1){
                                layer.msg(data.msg,{'icon':data.icon});
                            } else if(data.type == 2){
                                layer.confirm(data.msg, {
                                    btn: ['去认证','取消'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href=data.url;
                                }, function(){
                                    layer.closeAll();
                                });
                            } else if(data.type == 4){
                                layer.confirm('您还未登陆是否去登陆？', {
                                    btn: ['去登陆','暂不需要'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href='/login';
                                }, function(){
                                    layer.close();
                                    /*$(obj).attr("title","收藏");
                                     $(obj).removeClass('red');
                                     if($(obj).hasClass('done')){
                                     $(obj).removeClass('done');
                                     }*/
                                });
                            } else {
                                window.location = '{{asset('uct_myneed/supplyNeed')}}';
                            }
                        });
                    },
                    btn2: function(index, layero){
                        $.post('{{url('myneed/verifyputneed')}}',{'role':'专家'},function (data) {
                            if(data.type == 3 || data.type == 1){
                                layer.msg(data.msg,{'icon':data.icon});
                            } else if(data.type == 2){
                                layer.confirm(data.msg, {
                                    btn: ['去认证','取消'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href=data.url;
                                }, function(){
                                    layer.closeAll();
                                });
                            } else if(data.type == 4){
                                layer.confirm('您还未登陆是否去登陆？', {
                                    btn: ['去登陆','暂不需要'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href='/login';
                                }, function(){
                                    layer.close();
                                    /*$(obj).attr("title","收藏");
                                     $(obj).removeClass('red');
                                     if($(obj).hasClass('done')){
                                     $(obj).removeClass('done');
                                     }*/
                                });
                            } else {
                                window.location = '{{asset('myneed/supplyNeed')}}';
                            }
                        });
                    },
                    btn3: function(index, layero){
                        //按钮【按钮三】的回调
                        layer.close(index);
                        //return false 开启该代码可禁止点击该按钮关闭
                    }
                });
                    /*if($.cookie('role')=="企业" || $.cookie('role')=="专家企业"){

                        $.post('{{url('myneed/verifyputneed')}}',{'role':'企业'},function (data) {
                            if(data.type == 3){
                                layer.msg(data.msg,{'icon':data.icon});
                            } else if(data.type == 2){
                                layer.confirm(data.msg, {
                                    btn: ['去认证','以专家身份发起需求'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href=data.url;
                                }, function(){
                                    expertputneed();
                                });
                            } else if (data.type == 1){
                                layer.confirm(data.msg+', 您是否以专家身份发起需求？', {
                                    btn: ['是','否'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    expertputneed();
                                }, function(){
                                    layer.close();
                                });
                            } else {
                                window.location = '{{asset('uct_myneed/supplyNeed')}}';
                            }
                        });
                    }else{
                        $.post('{{url('myneed/verifyputneed')}}',{'role':'专家'},function (data) {
                            if(data.type == 3){
                                layer.msg(data.msg,{'icon':data.icon});
                            } else if(data.type == 2){
                                layer.confirm(data.msg, {
                                    btn: ['去认证','以企业身份发起需求'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    window.location.href=data.url;
                                }, function(){
                                    enterpriseputneed();
                                });
                            } else if (data.type == 1){
                                layer.confirm(data.msg+', 您是否以企业身份发起需求？', {
                                    btn: ['是','否'], //按钮
                                    skin:'layui-layer-molv'
                                }, function(){
                                    enterpriseputneed();
                                }, function(){
                                    layer.close();
                                });
                            } else {
                                window.location = '{{asset('myneed/supplyNeed')}}';
                            }
                        });

                    }*/
            break;
        }
    }
    $(".homepage-link").on("click",function(){
        var type=$(this).text();
        getPath(type);
    })

    $('.item-con-hover span').on('click',function () {
        var aa = $(this).parent('p').parent('div').prev('div').children('h2').html();
        switch(aa)
        {
            case '01':
               var domain='找资金';
               return false;
                break;
            case '02':
                var domain='找市场';
                break;
            case '03':
               var domain='找技术';
                break;
            case '04':
                var domain='定战略';
                break;
            default:
                var domain='全部';
        }

        if(!$.cookie('userId')){
            window.location.href="/login"
            return false;
        } else {
            window.location.href="{{asset('uct_works').'?domain='}}"+domain;
        }
    });

    $('.linemeet').click(function () {
        window.location.href="{{asset('expert').'?role=专家&ordertime=desc'}}";
    })
    $('.supplyShow').click(function () {
        var aa = $(this).parent('p').parent('div').prev('div').children('h2').html();
        switch(aa)
        {
            case '01':
                var domain='找资金';
                break;
            case '02':
                var domain='找市场';
                break;
            case '03':
                var domain='找技术';
                break;
            case '04':
                var domain='定战略';
                break;
            default:
                var domain='全部';
        }
        if(!$.cookie('userId')){
            window.location.href="/login"
            return false;
        } else {
            window.location.href="{{asset('uct_myshow')}}";
        }
    })

    $('.hatch').click(function () {
        var aa = $(this).parent('p').parent('div').prev('div').children('h2').html();
        switch(aa)
        {
            case '01':
                var domain='找资金';
                break;
            case '02':
                var domain='找市场';
                break;
            case '03':
                var domain='找技术';
                break;
            case '04':
                var domain='定战略';
                break;
            default:
                var domain='全部';
        }
        if(!$.cookie('userId')){
            window.location.href="/login"
            return false;
        } else {
            window.location.href = "{{asset('uct_works').'?domain='}}" + domain;
        }
    })


    function expertputneed () {
        $.post('{{url('myneed/verifyputneed')}}',{'role':'专家'},function (data) {
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
                window.location = '{{asset('myneed/supplyNeed')}}';
            }
        });
    }

    function enterpriseputneed () {
        $.post('{{url('myneed/verifyputneed')}}',{'role':'企业'},function (data) {
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
                window.location = '{{asset('uct_myneed/supplyNeed')}}';
            }
        });
    }


</script>

@endsection