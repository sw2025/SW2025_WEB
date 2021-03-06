@extends("layouts.ucenter2")
@section("content")
    <script type="text/javascript" src="{{asset('js/reply.js')}}"></script>
        <!-- 侧边栏公共部分/end -->
        <div class="main">
            <!-- 我是专家 / start -->
            <h3 class="main-top">我是专家</h3>
            <div class="ucenter-con">
                <div class="main-right">
                    <div class="myexpert">
                        <div class="myexpert-top">
                            <img src="@if(empty($datas->showimage)){{url('img/avatar.jpg')}}@else {{env('ImagePath').$datas->showimage}}@endif" class="myexpert-img" />
                            <div class="myexpert-rt">
                                <span class="myexp-name"><i class="iconfont icon-iconfonticon"></i>{{$datas->expertname}}</span>
                                <span class="myexp-best">擅长领域：<em>{{$datas->domain1}}</em></span>
                                <span class="myexp-best">咨询费用：<em>@if($datas->fee == 0) 免费 @else ￥{{$datas->fee}}/分钟 @endif</em></span>
                                <div class="myexpert-lab">
                                    @foreach(explode(',',$datas->domain2) as $do2)
                                        <span class="myexp-lab-a">&nbsp;{{$do2}}&nbsp;</span>
                                    @endforeach

                                </div>
                                <span class="myexp-time">入驻时间：<em>{{$datas->created_at}}</em></span>
                            </div>
                        </div>
                        <div class="myexpert-brief">
                            <div class="details-abs-tit">
                                <div class="details-graph forth"><span class="square"></span></div>
                                <span class="details-tit-cap forth-cap">专家简介</span>
                            </div>
                            <textarea class="myexp-brief-desc" id="textarea" style="border:none;width: 85%;">{{$datas->brief}}</textarea>
                            <a href="javascript:;" class="collect @if(in_array($datas->expertid,$collectids)) red @endif" index="{{$datas->expertid}}" title="@if(in_array($datas->expertid,$collectids)) 已收藏 @else 收藏 @endif"><i class="iconfont icon-likeo"></i></a>
                        </div>
                        <div class="message-list">
                            <div class="details-abs-tit">
                                <div class="details-graph forth"><span class="square"></span></div>
                                <span class="details-tit-cap forth-cap">留言列表</span>
                            </div>
                            <div class="all-replys">
                                @foreach($message as $v)
                                    @if(!$v->parentid)
                                        <div class="mes-list-box clearfix">
                                            <div class="floor-host">
                                                <img src="@if(empty($v->avatar)){{url('img/avatar.jpg')}}@else {{env('ImagePath').$v->avatar}}@endif" class="floor-host-ava" />
                                                <div class="floor-host-desc">
                                                    <a href="javascript:;" class="floor-host-name">{{$v->nickname or substr_replace($v->phone,'****',3,4)}} [{{$v->enterprisename or $v->expertname}}]</a><span class="floor-host-time">{{$v->messagetime}}</span>
                                                    <span class="floor-host-words">{{$v->content}}</span>
                                                </div>
                                            </div>
                                            <div class="message-reply-show">
                                                <a href="javascript:;" class="look-reply">查看回复（@if(key_exists($v->id,$msgcount)){{$msgcount[$v->id]}}@else 0 @endif）</a>
                                                <a href="javascript:;" class="message-reply">回复</a>
                                            </div>
                                            <div class="reply-list">
                                                <ul class="reply-list-ul">
                                                    @foreach($message as $reply)
                                                        @if(!$reply->use_userid && $reply->parentid == $v->id)
                                                            <li>
                                                                <img src="@if(empty($reply->avatar)){{url('img/avatar.jpg')}}@else {{env('ImagePath').$reply->avatar}}@endif" class="floor-guest-ava" />
                                                                <div class="gloor-guest-cnt">
                                                                    <a href="javascript:;" class="floor-guest-name">{{$reply->nickname or substr_replace($reply->phone,'****',3,4)}} [{{$reply->enterprisename or $reply->expertname}}]</a>
                                                                    <span class="floor-guest-words">{{$reply->content}}</span>
                                                                </div>
                                                                <div class="floor-bottom">
                                                                    <span class="floor-guest-time">{{$reply->messagetime}}</span><a href="javascript:;" class="reply-btn" userid="{{$reply->userid}}">回复</a>
                                                                </div>
                                                            </li>
                                                        @elseif($reply->parentid == $v->id)

                                                            <li>
                                                                <img src="@if(empty($reply->avatar)){{url('img/avatar.jpg')}}@else {{env('ImagePath').$reply->avatar}}@endif" class="floor-guest-ava" />
                                                                <div class="gloor-guest-cnt">
                                                                    <a href="javascript:;" class="floor-guest-name">{{$reply->nickname or substr_replace($reply->phone,'****',3,4)}} [{{$reply->enterprisename or $reply->expertname}}]</a>回复&nbsp;<a href="javascript:;" class="floor-guest-name">{{$reply->nickname2 or substr_replace($reply->phone2,'****',3,4)}}</a>
                                                                    <span class="floor-guest-words">{{$reply->content}}</span>
                                                                </div>
                                                                <div class="floor-bottom">
                                                                    <span class="floor-guest-time">{{$reply->messagetime}}</span><a href="javascript:;" userid="{{$reply->userid}}" class="reply-btn">回复</a>
                                                                </div>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                                <div class="reply-box">
                                                    <textarea class="reply-enter" index="{{$v->expertid}}" id="{{$v->id}}"></textarea>
                                                    <div class="publish-box"><button class="publish-btn" type="button">发表</button></div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            $('.bank-card').keyup(function(){
                var value=$(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1 ");
                $(this).val(value)
            })
        })
        $('.myneed-set-btn').on('click',function () {
            var mdid = $(this).attr('index');
            var supplyid = $(this).attr('supplyid');
            layer.confirm('您确定此需求已解决？', {
                btn: ['确定','摁错了~'] //按钮
            }, function(){
                $.post('{{url('uct_myneed/solveNeed')}}',{'mdid':mdid,'supplyid':supplyid},function (data) {
                    layer.msg(data.msg,{'icon':data.icon,time: 1500},function () {
                        window.location.href = document.referrer;
                    });
                });
            }, function(){
                layer.close();
            });

        });
    </script>
    <script src="{{url('js/myexpert.js')}}" type="text/javascript"></script>
    <script src="{{url('js/textareaauto.js')}}" type="text/javascript"></script>
@endsection