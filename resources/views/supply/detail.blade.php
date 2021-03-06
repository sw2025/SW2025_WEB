@extends("layouts.master")
@section("content")
<script type="text/javascript" src="{{asset('js/reply.js')}}"></script>
<style>
    #selectexpert{
        float: right;
        border: 1px solid #000;
        padding: 4px;
        background: #fff;
        border-radius: 5px;
        margin-top: 24%;
    }
    #selectexpert:hover{
        background: #3daff3;
        color: #fff;
    }
    .textareaspan{
        width:99%;
        font-size: 15px;
        border:none;
    }
</style>
<link rel="stylesheet" type="text/css" href="{{asset('css/details.css')}}" />
<div class="container section">
    <div class="row clearfix">
        <div class="main-content col-md-8">
            <div class="details-top clearfix">
                <div class="details-bg">
                    <span class="blue-circle"><i class="iconfont icon-jianjie1"></i></span>
                    <span class="details-ch-tit">商情信息</span>
                </div>
                <span class="details-en-tit">SUPPLY AND DEMAND INFORMATION</span>
            </div>

            <div class="supp-details-con">
                <div class="supp-det-con-top">
                    @if(!empty($datas->documenturl))<button id="selectexpert" style="font-size: 15px;" onclick="window.open('{{url("/showfile").'?path='.$datas->documenturl}}')">点击查看详细PDF</button>@endif

                    <img src="@if(empty($datas->entimg)) {{env('ImagePath').$datas->extimg}} @else {{env('ImagePath').$datas->entimg}}  @endif" class="supp-details-img" />
                    <div class="supp-details-brief">
                        <span class="supp-details-name"><i class="iconfont icon-gongsi"></i>【{{$datas->needtype}}】@if($datas->needtype=="专家") {{$datas->expertname}} @else {{$datas->enterprisename}} @endif</span>
                        <a href="javascript:;" index="{{$datas->needid}}" class="collect-state @if(in_array($datas->needid,$collectids)) done @endif">@if(in_array($datas->needid,$collectids))已收藏 @else 收藏 @endif</a>
                        <span class="supp-details-time">发布时间：<em>{{$datas->needtime}}</em></span>
                        <span class="supp-details-zone">地<b class="wem2"></b>区：<em>{{$datas->address}}</em></span>
                        <span class="supp-details-categary">商情分类：<em>{{$datas->domain1}} / {{$datas->domain2}}</em></span>

                    </div>
                </div><a name="reply">
                <div class="details-abs">
                    <div class="details-abs-tit">
                        <div class="details-graph"><span class="square"></span></div>
                        <span class="details-tit-cap">商情描述</span>
                    </div>
                    <textarea class="details-abs-desc" disabled id="textarea" style="font-size: 15px;">{{trim($datas->brief)}}</textarea>
                </div>
            </div>

            <div class="details-top clearfix">
                <div class="details-bg">
                    <span class="blue-circle"><i class="iconfont icon-liuyan"></i></span>
                    <span class="details-ch-tit">我的留言</span>
                </div>
                <span class="details-en-tit">COMMENT THREADS</span>
            </div>
            <div class="details-message">
                <form action="" method="post">
                    <div class="message-write">
                        <textarea name="content" id="{{$datas->needid}}" cols="30" rows="10" class="message-txt" placeholder="请输入留言"></textarea>
                        <div class="message-btn"><button class="submit" type="button">提交</button></div>
                    </div>
                </form>
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
                                        <textarea class="floor-host-words textareaspan" readonly>{{$v->content}}</textarea>
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
                                        <textarea class="reply-enter" index="{{$v->needid}}" id="{{$v->id}}"  ></textarea>
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
        <div class="col-md-4 det-aside">
            <div class="aside-top">
                <span class="aside-top-icon"><i class="iconfont icon-tuijian"></i></span>
                <span class="width2"></span>
                <span class="aside-top-tit">推荐商情信息</span>
            </div>
            <div class="ad-box">
                <span class="ad-cap">更多种类</span>
                多种选择<span class="ad-ct">更好的服务</span>选择在升维
            </div>
            <ul class="supp-recom-list">
                @foreach($recommendNeed as $v)
                <li>
                    <a href="{{url('supply/detail',$v->needid)}}" class="supp-rec-link">
                        <div class="supp-rec-top">
                            <img src="@if($v->needtype == '专家')) {{env('ImagePath').$v->extimg}} @else {{env('ImagePath').$v->entimg}}  @endif" class="supp-rec-img" />
                            <div class="supp-rec-com">
                                <span class="supp-rec-name">【{{$v->needtype}}】@if(!empty($v->needtype == '专家')) {{$v->expertname}} @else {{$v->enterprisename}} @endif</span>
                                <p class="supp-rec-category">商情分类：<span><em>{{$v->domain1}} / {{$v->domain2}}</em></span></p>
                            </div>
                        </div>
                        <span class="supp-rec-time">{{$v->needtime}}</span>
                        <div class="supp-rec-brief">
                            {{$v->brief}}
                        </div>
                    </a>
                    <div class="exp-rec-icon supp-rec-icon">
                        <a href="{{url('supply/detail',$v->needid)}}#reply" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                        <a href="javascript:;" class="collect @if(in_array($v->needid,$collectids)) red @endif" index="{{$v->needid}}" title="@if(in_array($v->needid,$collectids)) 已收藏 @else 收藏 @endif"><i class="iconfont icon-likeo"></i></a>
                    </div>
                </li>
               @endforeach
            </ul>
        </div>
    </div>

</div>

<script src="{{url('js/supply.js')}}" type="text/javascript"></script>
<script src="{{url('js/textareaauto.js')}}" type="text/javascript"></script>
<script>
    $('.textareaspan').each(function () {
        autoTextarea($(this)[0]);
    });
</script>
@endsection
