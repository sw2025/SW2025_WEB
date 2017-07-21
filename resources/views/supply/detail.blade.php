@extends("layouts.master")
@section("content")
<script type="text/javascript" src="{{asset('js/reply.js')}}"></script>
<link rel="stylesheet" type="text/css" href="{{asset('css/details.css')}}" />
<div class="container section">
    <div class="row clearfix">
        <div class="main-content col-md-8">
            <div class="details-top clearfix">
                <div class="details-bg">
                    <span class="blue-circle"><i class="iconfont icon-jianjie1"></i></span>
                    <span class="details-ch-tit">供求信息</span>
                </div>
                <span class="details-en-tit">SUPPLY AND DEMAND INFORMATION</span>
            </div>
            <div class="supp-details-con">
                <div class="supp-det-con-top">
                    <img src="{{asset('img/avatar1.jpg')}}" class="supp-details-img" />
                    <div class="supp-details-brief">
                        <span class="supp-details-name"><i class="iconfont icon-gongsi"></i>某公司</span>
                        <a href="javascript:;" class="collect-state done">已收藏</a>
                        <span class="supp-details-time">发布时间：<em>2017年7月12日</em></span>
                        <span class="supp-details-zone">地<b class="wem2"></b>区：<em>北京</em></span>
                        <span class="supp-details-categary">需求分类：<em>销售</em></span>

                    </div>
                </div>
                <div class="details-abs">
                    <div class="details-abs-tit">
                        <div class="details-graph"><span class="square"></span></div>
                        <span class="details-tit-cap">简介</span>
                    </div>
                    <div class="details-abs-desc">
                        北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。
                    </div>
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
                <form action="">
                    <div class="message-write">
                        <textarea name="" id="" cols="30" rows="10" class="message-txt" placeholder="请输入留言"></textarea>
                        <div class="message-btn"><button class="submit" type="button">提交</button></div>
                    </div>
                </form>
                <div class="message-list">
                    <div class="details-abs-tit">
                        <div class="details-graph forth"><span class="square"></span></div>
                        <span class="details-tit-cap forth-cap">留言列表</span>
                    </div>
                    <div class="all-replys">
                        <div class="mes-list-box clearfix">
                            <div class="floor-host">
                                <img src="{{asset('img/avatar1.jpg')}}" class="floor-host-ava" />
                                <div class="floor-host-desc">
                                    <a href="javascript:;" class="floor-host-name">李道山</a><span class="floor-host-time">2017-7-8  17：25</span>
                                    <span class="floor-host-words">你好</span>
                                </div>
                            </div>
                            <div class="message-reply-show">
                                <a href="javascript:;" class="look-reply">查看回复（2）</a>
                                <a href="javascript:;" class="message-reply">回复</a>
                            </div>
                            <div class="reply-list">
                                <ul class="reply-list-ul">
                                    <li>
                                        <img src="{{asset('img/avatar2.jpg')}}" class="floor-guest-ava" />
                                        <div class="gloor-guest-cnt">
                                            <a href="javascript:;" class="floor-guest-name">牛犇犇</a>
                                            <span class="floor-guest-words">你好</span>
                                        </div>
                                        <div class="floor-bottom">
                                            <span class="floor-guest-time">2017-7-8  17：25</span><a href="javascript:;" class="reply-btn">回复</a>
                                        </div>
                                    </li>
                                    <li>
                                        <img src="{{asset('img/avatar3.jpg')}}" class="floor-guest-ava" />
                                        <div class="gloor-guest-cnt">
                                            <a href="javascript:;" class="floor-guest-name">李道山</a>回复&nbsp;<a href="javascript:;" class="floor-guest-name">牛犇犇</a>
                                            <span class="floor-guest-words">你好</span>
                                        </div>
                                        <div class="floor-bottom">
                                            <span class="floor-guest-time">2017-7-8  17：25</span><a href="javascript:;" class="reply-btn">回复</a>
                                        </div>
                                    </li>
                                </ul>
                                <div class="reply-box">
                                    <textarea class="reply-enter"></textarea>
                                    <div class="publish-box"><button class="publish-btn" type="button">发表</button></div>
                                </div>
                            </div>
                        </div>
                        <div class="mes-list-box clearfix">
                            <div class="floor-host">
                                <img src="{{asset('img/avatar1.jpg')}}" class="floor-host-ava" />
                                <div class="floor-host-desc">
                                    <a href="javascript:;" class="floor-host-name">李道山</a><span class="floor-host-time">2017-7-8  17：25</span>
                                    <span class="floor-host-words">你好</span>
                                </div>
                            </div>
                            <div class="message-reply-show">
                                <a href="javascript:;" class="look-reply">查看回复（0）</a>
                                <a href="javascript:;" class="message-reply">回复</a>
                            </div>
                            <div class="reply-list">
                                <ul class="reply-list-ul">

                                </ul>
                                <div class="reply-box">
                                    <textarea class="reply-enter"></textarea>
                                    <div class="publish-box"><button class="publish-btn" type="button">发表</button></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 det-aside">
            <div class="aside-top">
                <span class="aside-top-icon"><i class="iconfont icon-tuijian"></i></span>
                <span class="width2"></span>
                <span class="aside-top-tit">推荐供求信息</span>
            </div>
            <div class="ad-box">
                <span class="ad-cap">更多种类</span>
                多种选择<span class="ad-ct">更好的服务</span>选择在升维
            </div>
            <ul class="supp-recom-list">
                <li>
                    <a href="javascript:;" class="supp-rec-link">
                        <div class="supp-rec-top">
                            <img src="{{asset('img/avatar1.jpg')}}" class="supp-rec-img" />
                            <div class="supp-rec-com">
                                <span class="supp-rec-name">资芽（北京）网络科技有限公司</span>
                                <p class="supp-rec-category">需求分类：<span>销售</span></p>
                            </div>
                        </div>
                        <span class="supp-rec-time">2017-07-12</span>
                        <div class="supp-rec-brief">
                            北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。
                        </div>
                    </a>
                    <div class="exp-rec-icon supp-rec-icon">
                        <a href="javascript:;" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                        <a href="javascript:;" class="collect" title="收藏"><i class="iconfont icon-likeo"></i></a>
                    </div>
                </li>
                <li>
                    <a href="javascript:;" class="supp-rec-link">
                        <div class="supp-rec-top">
                            <img src="{{asset('img/avatar1.jpg')}}" class="supp-rec-img" />
                            <div class="supp-rec-com">
                                <span class="supp-rec-name">资芽（北京）网络科技有限公司</span>
                                <p class="supp-rec-category">需求分类：<span>销售</span></p>
                            </div>
                        </div>
                        <span class="supp-rec-time">2017-07-12</span>
                        <div class="supp-rec-brief">
                            北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。
                        </div>
                    </a>
                    <div class="exp-rec-icon supp-rec-icon">
                        <a href="javascript:;" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                        <a href="javascript:;" class="collect" title="收藏"><i class="iconfont icon-likeo"></i></a>
                    </div>
                </li>
                <li>
                    <a href="javascript:;" class="supp-rec-link">
                        <div class="supp-rec-top">
                            <img src="{{asset('img/avatar1.jpg')}}" class="supp-rec-img" />
                            <div class="supp-rec-com">
                                <span class="supp-rec-name">资芽（北京）网络科技有限公司</span>
                                <p class="supp-rec-category">需求分类：<span>销售</span></p>
                            </div>
                        </div>
                        <span class="supp-rec-time">2017-07-12</span>
                        <div class="supp-rec-brief">
                            北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。
                        </div>
                    </a>
                    <div class="exp-rec-icon supp-rec-icon">
                        <a href="javascript:;" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                        <a href="javascript:;" class="collect" title="收藏"><i class="iconfont icon-likeo"></i></a>
                    </div>
                </li>
                <li>
                    <a href="javascript:;" class="supp-rec-link">
                        <div class="supp-rec-top">
                            <img src="{{asset('img/avatar1.jpg')}}" class="supp-rec-img" />
                            <div class="supp-rec-com">
                                <span class="supp-rec-name">资芽（北京）网络科技有限公司</span>
                                <p class="supp-rec-category">需求分类：<span>销售</span></p>
                            </div>
                        </div>
                        <span class="supp-rec-time">2017-07-12</span>
                        <div class="supp-rec-brief">
                            北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。距离湖人送给勇士开局第16场连胜仅过去5天，那场比赛科比全场14投仅1中。他说：“这个赛季结束后，我已经离开了”。他曾经说：“当我退役时，那是因为我自己选择离开”。北京时间11月30日，科比在《球员论坛网》发表文章，宣布本赛季结束后正式退役。
                        </div>
                    </a>
                    <div class="exp-rec-icon supp-rec-icon">
                        <a href="javascript:;" class="review" title="留言"><i class="iconfont icon-pinglun1"></i></a>
                        <a href="javascript:;" class="collect" title="收藏"><i class="iconfont icon-likeo"></i></a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection