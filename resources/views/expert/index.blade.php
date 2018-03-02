@extends("layouts.master")
@section("content")

<!-- 搜索框 / start -->
<link rel="stylesheet" type="text/css" href="{{asset('css/list.css')}}" />
<script type="text/javascript" src="{{asset('js/list.js')}}"></script>
<div class="section">
    <div class="container list-bg">
        <form action="" onsubmit="return false">
            <div class="list-search">
                <input type="text" class="list-search-inp" placeholder="请输入专家姓名／机构名称／企业家姓名" value="{{$searchname or null}}" />
                <button type="button" class="list-search-btn"><i class="iconfont icon-sousuo"></i></button>
            </div>
        </form>
    </div>
</div>
<!-- 搜索框 / end -->
<!-- 筛选条件 / start -->
<div class="container list-filter">
    <div class="all-results filter-row clearfix"><span class="left-cap">全部结果：</span>
        @if(isset($role))<a href="javascript:;" class="all-results-expert all-results-opt">{{$role}}</a>@endif
        @if(isset($supply))<a href="javascript:;" class="all-results-field all-results-opt" index="{{$supply[0].'/'.$supply[1]}}">{{$supply[0].'/'.$supply[1]}}</a>@endif
        @if(isset($address))<a href="javascript:;" class="all-results-location all-results-opt">{{$address}}</a>@endif
        @if(isset($consult))<a href="javascript:;" class="all-results-video all-results-opt">{{$consult}}</a>@endif

    </div>
    <div class="experts-classify filter-row clearfix">
        <span class="left-cap">专家分类：</span>
        <a href="javascript:;" {{$role or 'class=active'}}>全部</a>
        <a href="javascript:;" @if(isset($role) && $role == '专家') class=active @endif>专家</a>
        <a href="javascript:;" @if(isset($role) && $role == '机构') class=active @endif>机构</a>
        <a href="javascript:;" @if(isset($role) && $role == '企业家') class=active @endif>企业家</a>
    </div>
    <div class="video-consult filter-row clearfix">
        <span class="left-cap">视频咨询：</span>
        <a href="javascript:;" {{$consult or 'class=active'}}>全部</a>
        <a href="javascript:;" @if(isset($consult) && $consult == '收费') class=active @endif>收费</a>
        <a href="javascript:;" @if(isset($consult) && $consult == '免费') class=active @endif>免费</a>
    </div>
    <div class="serve-field filter-row clearfix">
        <span class="left-cap">服务领域：</span>
        <a href="javascript:;" class="serve-all @if(empty($supply)) active @endif">全部</a>
        @foreach($cate as $big)
            @if($big->level == 1)
                <div class="serve-field-list">
                    <a href="javascript:;" class="serve-field-list-deft @if(isset($supply) && $supply[0] == $big->exdomainname) active @endif" index="{{$big->domainname}}">{{$big->exdomainname}}</a>
                    <ul class="serve-field-list-show" >
                        @foreach($cate as $small)
                            @if($small->level == 2 && $small->parentid == $big->domainid)
                                <li class="@if(!empty($supply) && $small->domainname == $supply[1]) active @endif">{{$small->domainname}}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        @endforeach
    </div>
    <div class="location filter-row clearfix">
        <span class="left-cap">所在地区：</span>
        <div class="location-province">
            <a href="javascript:;" @if(empty($address)) class="active" @endif">全部</a>
            <a href="javascript:;" @if(!empty($address) && $address=="北京") class="active" @endif>北京</a>
            <a href="javascript:;" @if(!empty($address) && $address=="上海") class="active" @endif>上海</a>
            <a href="javascript:;" @if(!empty($address) && $address=="天津") class="active" @endif>天津</a>
            <a href="javascript:;" @if(!empty($address) && $address=="重庆") class="active" @endif>重庆</a>
            <a href="javascript:;" @if(!empty($address) && $address=="河北") class="active" @endif>河北</a>
            <a href="javascript:;" @if(!empty($address) && $address=="山西") class="active" @endif>山西</a>
            <a href="javascript:;" @if(!empty($address) && $address=="内蒙") class="active" @endif>内蒙</a>
            <a href="javascript:;" @if(!empty($address) && $address=="辽宁") class="active" @endif>辽宁</a>
            <a href="javascript:;" @if(!empty($address) && $address=="吉林") class="active" @endif>吉林</a>
            <a href="javascript:;" @if(!empty($address) && $address=="黑龙") class="active" @endif>黑龙江</a>
            <a href="javascript:;" @if(!empty($address) && $address=="江苏") class="active" @endif>江苏</a>
            <a href="javascript:;" @if(!empty($address) && $address=="浙江") class="active" @endif>浙江</a>
            <a href="javascript:;" @if(!empty($address) && $address=="安徽") class="active" @endif>安徽</a>
            <a href="javascript:;" @if(!empty($address) && $address=="福建") class="active" @endif>福建</a>
            <a href="javascript:;" @if(!empty($address) && $address=="江西") class="active" @endif>江西</a>
            <a href="javascript:;" @if(!empty($address) && $address=="山东") class="active" @endif>山东</a>
            <a href="javascript:;" @if(!empty($address) && $address=="河南") class="active" @endif>河南</a>
            <a href="javascript:;" @if(!empty($address) && $address=="湖北") class="active" @endif>湖北</a>
            <a href="javascript:;" @if(!empty($address) && $address=="湖南") class="active" @endif>湖南</a>
            <a href="javascript:;" @if(!empty($address) && $address=="广东") class="active" @endif>广东</a>
            <a href="javascript:;" @if(!empty($address) && $address=="广西") class="active" @endif>广西</a>
            <a href="javascript:;" @if(!empty($address) && $address=="海南") class="active" @endif>海南</a>
            <a href="javascript:;" @if(!empty($address) && $address=="四川") class="active" @endif>四川</a>
            <a href="javascript:;" @if(!empty($address) && $address=="贵州") class="active" @endif>贵州</a>
            <a href="javascript:;" @if(!empty($address) && $address=="云南") class="active" @endif>云南</a>
            <a href="javascript:;" @if(!empty($address) && $address=="西藏") class="active" @endif>西藏</a>
            <a href="javascript:;" @if(!empty($address) && $address=="陕西") class="active" @endif>陕西</a>
            <a href="javascript:;" @if(!empty($address) && $address=="甘肃") class="active" @endif>甘肃</a>
            <a href="javascript:;" @if(!empty($address) && $address=="青海") class="active" @endif>青海</a>
            <a href="javascript:;" @if(!empty($address) && $address=="宁夏") class="active" @endif>宁夏</a>
            <a href="javascript:;" @if(!empty($address) && $address=="新疆") class="active" @endif>新疆</a>
            <a href="javascript:;" @if(!empty($address) && $address=="台湾") class="active" @endif>台湾</a>
            <a href="javascript:;" @if(!empty($address) && $address=="香港") class="active" @endif>香港</a>
            <a href="javascript:;" @if(!empty($address) && $address=="澳门") class="active" @endif>澳门</a>
        </div>
    </div>
</div>
<!-- 筛选条件 / end -->
<div class="container">
    <div class="row oh">
        <!-- 排序 / start -->
        <div class="sort">
            <a href="javascript:;" class="list-time @if(!empty($ordertime)) active @endif">认证时间<span class="list-order-icon"><i class="iconfont icon-triangle-copy @if(!empty($ordertime) && $ordertime == 'asc') white-color @elseif(!empty($ordertime) && $ordertime == 'desc') blue-color  @endif"></i><i class="iconfont icon-sanjiaoxing @if(!empty($ordertime) && $ordertime == 'asc') blue-color  @elseif(!empty($ordertime) && $ordertime == 'desc') white-color  @endif"></i></span></a>
            <a href="javascript:;" class="list-collect @if(!empty($ordercollect)) active @endif">收藏数<span class="list-order-icon"><i class="iconfont icon-triangle-copy @if(!empty($ordercollect) && $ordercollect == 'asc') white-color @elseif(!empty($ordercollect) && $ordercollect == 'desc') blue-color  @endif"></i><i class="iconfont icon-sanjiaoxing @if(!empty($ordercollect) && $ordercollect == 'asc') blue-color  @elseif(!empty($ordercollect) && $ordercollect == 'desc') white-color  @endif"></i></span></a>
            {{--<a href="javascript:;" class="list-reviews @if(!empty($ordermessage)) active @endif">留言数<span class="list-order-icon"><i class="iconfont icon-triangle-copy @if(!empty($ordermessage) && $ordermessage == 'asc') white-color @elseif(!empty($ordermessage) && $ordermessage == 'desc') blue-color  @endif"></i><i class="iconfont icon-sanjiaoxing @if(!empty($ordermessage) && $ordermessage == 'asc') blue-color  @elseif(!empty($ordermessage) && $ordermessage == 'desc') white-color  @endif"></i></span></a>--}}
        </div>
        <!-- 排序 / end -->
        <!-- 专家列表 / start -->
        <ul class="expert-list clearfix">
            @foreach($datas as $v)
            <li class="col-md-4">
                <a href="{{url('expert/detail',$v->expertid)}}" class="expert-list-link">
                    <div class="exp-list-top">
                        <span class="exp-list-img"><img src="{{env('ImagePath').$v->showimage}}" /></span>
                        <div class="exp-list-brief">
                            <span class="exp-list-name" >{{$v->expertname}}</span>

                        @if($v->islinemeet==1)
                                <span class="exp-list-video"><i class="iconfont icon-shipin"></i>线下:￥{{$v->linefee}}/小时 </span>
                            @else
                                <span class="exp-list-video"><i class="iconfont icon-shipin"></i>线上:未开启</span>
                            @endif

                            @if($v->isonlinemeet==1)
                                <span class="exp-list-video"><i class="iconfont icon-shipin"></i>线上:￥{{$v->fee}}/分钟</span>
                            @else
                                <span class="exp-list-video"><i class="iconfont icon-shipin"></i>线上:未开启</span>
                            @endif

{{--
                            <span class="exp-list-best"><i class="iconfont icon-shanchang"></i>擅长领域：<em>{{$domainselect[$v->domain1]}}</em></span>
--}}
                        </div>
                        <div class="exp-list-lab">
                            @foreach(explode(',',$v->domain2) as $do2)
                            <span class="exp-lab-a">&nbsp;{{$do2}}&nbsp;</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="exp-list-desc">
                        {{$v->brief}}
                    </div>

                </a>
                <div class="exp-list-icon">
                    <span style="position: relative;left:-52px;">
                        <button id="{{$v->userid}}" islinemeet="{{$v->islinemeet}}" name="{{$v->expertname}}" linefee="{{$v->linefee}}" expertid="{{$v->expertid}}" showimage="{{$v->showimage}}" class="linemeet" style="border-style:solid;background-color:transparent;width: 100px;height: 32px;">线下约见</button>&nbsp;&nbsp;&nbsp;&nbsp;
                        <button id="{{$v->userid}}" isonlinemeet="{{$v->isonlinemeet}}" name="{{$v->expertname}}" linefee="{{$v->linefee}}" expertid="{{$v->expertid}}" showimage="{{$v->showimage}}" class="onlinemeet hover" style=" border-style:solid;background-color:transparent;width: 120px;height: 32px;">线上约见</button>
                    </span>

                    <a href="{{url('expert/detail',$v->expertid)}}#reply" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                    <a href="javascript:;" class="collect @if(in_array($v->expertid,$collectids)) red @endif" index="{{$v->expertid}}" title="@if(in_array($v->expertid,$collectids))已收藏 @else 收藏@endif"><i class="iconfont icon-likeo"></i> <span>{{$v->collcount}}</span></a>
                </div>
            </li>
           @endforeach
        </ul>
        <!-- 专家列表 / end -->
        <!-- 分页 -->
        <div class="pages">
            <div id="Pagination"></div><span class="page-sum">共<strong class="allPage">{{$datas->lastpage()}}</strong>页</span>
        </div>
    </div>
</div>
<!-- 公共footer / end -->
<script type="text/javascript">
    $(function(){
        $("#Pagination").pagination("{{$datas->lastpage()}}",{'callback':pageselectCallback,'current_page':{{$datas->currentPage()-1}}});
        var height = $('.location-province').height();
        $('.location').css('height', height+'px');
        function pageselectCallback(page_index, jq){
            // 从表单获取每页的显示的列表项数目
            var current = parseInt(page_index)+1;
            var url = window.location.href;
            url = url.replace(/(\?|\&)?page=\d+/,'');
            var isexist = url.indexOf("?");
            if(isexist == -1){
                url += '?ordertime=desc&page='+current;
            } else {
                url += '&page='+current;
            }
            window.location=url;
            //阻止单击事件
            return false;
        }
    })
    $('.linemeet').click(function () {
        if(!$.cookie('userId')){
            layer.confirm('您还未登录是否去登录？', {
                btn: ['去登录','暂不需要'], //按钮
                skin:'layui-layer-molv'
            }, function(){
                window.location.href='/login';
            }, function(){
                layer.close();
            });
            return false;
        }

        if($.cookie('userId')==userid){
            layer.msg('不可以约见自己哦',{'icon':5});
            return false;
        }
        var userid=$(this).attr("id");
        var expertname =$(this).attr("name");
        var linefee =$(this).attr("linefee");
        var expertid =$(this).attr("expertid");
        var showimage =$(this).attr("showimage");
        var islinemeet =$(this).attr("islinemeet");

        if(islinemeet!=1){
            layer.msg('专家还没开启此功能',{'icon':7});
            return false;
        }

        $.cookie("videoisAppoint",1,{path:'/',domain:'sw2025.com'});
        $.cookie("videoreselect",expertname+'/'+linefee+'/'+expertid+showimage,{path:'/',domain:'sw2025.com'});

        window.location.href="{{url('/uct_video/lineMeet')}}";
    })

    $('.onlinemeet').click(function () {

        if(!$.cookie('userId')){
            layer.confirm('您还未登录是否去登录？', {
                btn: ['去登录','暂不需要'], //按钮
                skin:'layui-layer-molv'
            }, function(){
                window.location.href='/login';
            }, function(){
                layer.close();
            });
            return false;
        }


        if($.cookie('userId')==userid){
            layer.msg('不可以约见自己哦',{'icon':5});
            return false;
        }
        var userid=$(this).attr("id");
        var expertname =$(this).attr("name");
        var linefee =$(this).attr("linefee");
        var expertid =$(this).attr("expertid");
        var showimage =$(this).attr("showimage");
        var isonlinemeet =$(this).attr("isonlinemeet");

        if(isonlinemeet!=1){
            layer.msg('嘻嘻，专家还没开启此功能',{'icon':5});
            return false;
        }

        $.cookie("videoisAppoint",1,{path:'/',domain:'sw2025.com'});
        $.cookie("videoreselect",expertid+showimage,{path:'/',domain:'sw2025.com'});
        /*if(obj != null){
         var ss = $(obj).val().split(/【(.*)】/i);
         $.cookie("videodomain",ss[1],{path:'/',domain:'sw2025.com'});
         $.cookie("videodescribe", $.trim(ss[2]),{path:'/',domain:'sw2025.com'});
         }*/
        window.location.href="{{url('/uct_video/applyVideo')}}";

    })
</script>
<script src="{{url('js/expert.js')}}" type="text/javascript"></script>
@endsection
