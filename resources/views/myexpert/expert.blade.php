@extends("layouts.ucenter4")
@section("content")
<link rel="stylesheet" type="text/css" href="{{asset('css/uctexperts.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('css/cropper.min.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('css/main.css')}}" />
    <!-- 公共header / end -->
<script src="{{asset('./FileUpload/js/vendor/jquery.ui.widget.js')}}"></script>
<script src="{{asset('./FileUpload/js/jquery.fileupload.js')}}"></script>
<script src="{{asset('./FileUpload/js/jquery.iframe-transport.js')}}"></script>
<script src="{{asset('./FileUpload/js/jquery.fileupload-process.js')}}"></script>
<script src="{{asset('./FileUpload/js/jquery.fileupload-validate.js')}}"></script>
<script type="text/javascript" src="{{asset('js/bootstrap.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/cropper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('js/main.js')}}"></script>
<div class="main">
    <!-- 发布需求 / start -->
    <h3 class="main-top">专家认证</h3>
    <div class="ucenter-con">
        <div class="main-right">
            <div class="card-step">
                <span class="green-circle">1</span>资料提交<span class="card-step-cap">&gt;</span>
                <span class="gray-circle">2</span>资料审核<span class="card-step-cap">&gt;</span>
                <span class="gray-circle">3</span>认证成功
            </div>
            <div class="expert-certy">
                <div class="expert-certy-state">
                    <div class="friendly categoryExpert">
                        <p class="center">友好提示</p>
                        <p> 1.本平台为企业提供专家咨询和重要资源。</p>
                        <p> 2.请专家实名认证，上传有效证照以便与企业深度互动。</p>
                        <p> 3.专家介绍中至少包括“职称、任职、擅长、重要经历”等内容。</p>
                    </div>
                    <div class="friendly categoryNoExpert">
                        <p class="center">友好提示</p>
                        <p>1.请实名认证，上传有效证照。</p>
                        <p> 2.机构介绍至少包括“机构业务范围、擅长领域 主要业绩”等内容。</p>
                    </div>
                    <br/>
                    @if(!empty($result) && $result->configid==3)
                        <span style="color:red">
                            <em>审核失败</em>
                            拒绝理由：{{$result->remark}}
                        </span>
                    @endif
                </div>
                <div class="datas">
                    <div class="datas-lt">
                        <div class="datas-lt-enter">
                            <div class="datas-sel zindex4">
                                <span class="datas-sel-cap">专家分类</span>
                                <a href="javascript:;" id="category" class="datas-sel-def">专家</a>
                                <ul class="datas-list">
                                    <li>专家</li>
                                    <li>机构</li>
                                    <li>企业家</li>
                                </ul>
                            </div>
                            <div class="datas-sel">
                                <span class="datas-sel-cap">输入姓名</span>
                                <input class="datas-sel-name" type="text" placeholder="" value="@if(!empty($result)){{$result->expertname}}@else @endif" style="color:#666;margin-left:50px;"/>
                            </div>
                            <div class="publish-need-sel datas-newchange zindex2">
                                <span class="datas-sel-cap">擅长领域</span>
                                <a href="javascript:;" id="industry" class="publ-need-sel-def" style="margin-left:93px;" index="@if(!empty($result->domain1)){{$result->domain1}}-{{join('/',explode(',',$result->domain2))}}/@else 请选择 @endif">
                                    @if(!empty($result->domain1))
                                        @if($result->domain1=='找资金')
                                            投融资
                                        @elseif($result->domain1=='找技术')
                                            科研技术
                                        @elseif($result->domain1=='定战略')
                                            战略管理
                                        @else
                                            市场资源
                                        @endif -{{join('/',explode(',',$result->domain2))}}/
                                    @else 请选择 @endif
                                </a>
                                <ul class="publish-need-list">
                                    @foreach($cate as $v)
                                        @if($v->level == 1)
                                            <li>
                                                <a href="javascript:;" index="{{$v->domainname}}">{{$v->exdomainname}}</a>
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
                            <div class="datas-sel zindex1">
                                <span class="datas-sel-cap">地区</span><a href="javascript:;" id="address" class="datas-sel-def">北京</a>
                                <ul class="datas-list zone-list">
                                    <li>北京</li>
                                    <li>上海</li>
                                    <li>天津</li>
                                    <li>重庆</li>
                                    <li>河北</li>
                                    <li>山西</li>
                                    <li>内蒙古</li>
                                    <li>辽宁</li>
                                    <li>吉林</li>
                                    <li>黑龙江</li>
                                    <li>江苏</li>
                                    <li>浙江</li>
                                    <li>安徽</li>
                                    <li>福建</li>
                                    <li>江西</li>
                                    <li>山东</li>
                                    <li>河南</li>
                                    <li>湖北</li>
                                    <li>湖南</li>
                                    <li>广东</li>
                                    <li>广西</li>
                                    <li>海南</li>
                                    <li>四川</li>
                                    <li>贵州</li>
                                    <li>云南</li>
                                    <li>西藏</li>
                                    <li>陕西</li>
                                    <li>甘肃</li>
                                    <li>青海</li>
                                    <li>宁夏</li>
                                    <li>新疆</li>
                                    <li>台湾</li>
                                    <li>香港</li>
                                    <li>澳门</li>
                                </ul>
                            </div>
                        </div>
                        <div class="datas-upload-box clearfix">
                            <div class="datas-upload-lt">
                                <img src="@if(!empty($result) && $result->licenceimage){{env('ImagePath').$result->licenceimage}}@else img/photo1.jpg @endif"
                                     class="photo1" id="avatar1"/>
                                <div class="photo-upload">
                                    <div class="photo-btn-box fileinput-button">
                                        <span class="photo-btn-tip">上传专家证件</span>
                                        <input id="photo1" type="file" name="files[]" data-url="{{asset('upload')}}"
                                               index="@if(!empty($result)){{$result->licenceimage}}@endif" multiple=""
                                               accept="image/png, image/gif, image/jpg, image/jpeg">
                                    </div>
                                    <p class="datas-lt-explain">专家执照仅做认证用，不用做其它用途</p>
                                </div>
                            </div>
                            <div class="datas-upload-rt">
                                <img src="@if(!empty($result) && $result->showimage){{env('ImagePath').$result->showimage}}@else img/photo2.jpg @endif"
                                     id="avatar2" class="photo1"/>
                                <div class="photo-upload" id="crop-avatar">
                                    <div class="photo-btn-box fileinput-button">
                                        <span class="photo-btn-tip avatar-view ">上传专家照片</span>
                                           {{--  <input class="avatar-input" id="avatarInput" name="avatar_file" type="file">--}}
                                       {{-- <input id="photo2" type="file" name="files[]" data-url="{{asset('upload')}}"
                                               multiple="" index="@if(!empty($result)){{$result->showimage}}@endif"
                                               accept="image/png, image/gif, image/jpg, image/jpeg">--}}
                                    </div>
                                    <div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <form class="avatar-form" action="{{asset('avatarUpload')}}" enctype="multipart/form-data" method="post">
                                                    <div class="modal-header">
                                                        <button class="close" data-dismiss="modal" type="button">&times;</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="avatar-body">

                                                            <!-- Upload image and data -->
                                                            <div class="avatar-upload">
                                                                <input class="avatar-src" name="avatar_src" type="hidden">
                                                                <input class="avatar-data" name="avatar_data" type="hidden">
                                                              {{-- <label for="avatarInput"></label>--}}
                                                                <input class="avatar-input" id="avatarInput"  name="avatar_file" type="file" >
                                                            </div>

                                                            <!-- Crop and preview -->
                                                            <div class="row">
                                                                <div class="col-md-9">
                                                                    <div class="avatar-wrapper"></div>
                                                                </div>
                                                                <div class="col-md-3">
                                                                    <div class="avatar-preview preview-lg"></div>
                                                                    <div class="avatar-preview preview-md"></div>
                                                                    <div class="avatar-preview preview-sm"></div>
                                                                </div>
                                                            </div>

                                                            <div class="row avatar-btns">
                                                                <div class="col-md-3">
                                                                    <button class="btn btn-primary btn-block avatar-save" type="submit">确定</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="modal-footer">
                                                      <button class="btn btn-default" data-dismiss="modal" type="button">Close</button>
                                                    </div> -->
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="datas-lt-explain">专家照片用于展示专家，请选择能展现专家风采的照片</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="datas-rt htxt1">
                        <textarea  placeholder="请输入专家描述（最多1000字）" id="brief" cols="30"
                                  rows="10">@if(!empty($result)){{$result->brief}}@endif</textarea>
                    </div>
                </div>
                @if(!empty($result) && $result->configid==3)
                    <div class="bottom-btn">
                        <button class="test-btn submit-audit" type="button">修改资料</button>
                    </div>
                @else
                    <div class="bottom-btn">
                        <button class="test-btn submit-audit" type="button">提交审核</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<script src="/js/layer/extend/layer.ext.js"></script>
<script type="text/javascript">
    $(function () {
        if($("#category").text()=="专家" || $("#category").text()=="企业家" ){
            $(".categoryExpert").show();
            $(".categoryNoExpert").hide();
        }else{
            $(".categoryExpert").hide();
            $(".categoryNoExpert").show();
        }
        $('.datas-sel-def').click(function () {
            $(this).next('ul').stop().slideToggle();
            $(this).parent().siblings().children('ul').hide();
        });
        $('.datas-list li').click(function () {
            var publishHtml = $(this).html();
            if(publishHtml=="专家" || publishHtml=="企业家"){
                $(".categoryExpert").show();
                $(".categoryNoExpert").hide();
            }else{
                $(".categoryExpert").hide();
                $(".categoryNoExpert").show();
            }
            $(this).parent().prev('.datas-sel-def').html(publishHtml);
            $(this).parent().hide();
        });

        $('.publ-need-sel-def').click(function (e) {
            e.stopPropagation();
            $(this).next('ul').stop().slideToggle();
        });

        $('.publish-need-list li').hover(function () {
            $(this).children('ul').stop().show();}, function () {
            $(this).children('ul').stop().hide();
        });
        $('.publish-need-list li a').click(function (e) {
            e.stopPropagation();
            if ($(this).next('ul').children('li').length == 0) {
                var m = $(this).html();
                $(this).closest('.publish-need-list').prev().html(m);
            }
        })
        $(document).click(function (event) {
            $('.publish-need-list').hide();
        });
        $('.publ-sub-list li').click(function (e) {
            e.stopPropagation();
            $(this).toggleClass('on');
            $(this).closest('.publish-need-list>li').siblings().find('li').removeClass('on');
            var y = $(this).parent('ul').prev('a').html();
            var y2 = $(this).parent('ul').prev('a').attr('index');
            var x = y + '-';
            var x2 = y2 + '-';
            $('.publ-sub-list li').each(function (index, el) {
                if ($(el).hasClass('on')) {
                    // x = $('.on').html();
                    x += $(el).html() + '/';
                    x2 += $(el).html() + '/';
                    $('.publ-need-sel-def').html(x);
                    $('.publ-need-sel-def').attr('index',x2);

                } else {
                    $('.publ-need-sel-def').html(x);
                    $('.publ-need-sel-def').attr('index',x2);
                }

            });
        });    })

    $(function () {
        var token = $.cookie('token');
        $('#photo1').fileupload({
            dataType: 'json',
            maxFileSize: 1 * 1024 * 1024,
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    // console.log(file.name);
                    $("#avatar1").attr('src', '{{env('ImagePath')}}/images/' + file.name).show();
                    $('#photo1').attr('index', '/images/' + file.name);
                });
            }
        });    });

    $(function () {
        var token = $.cookie('token');
        $('#photo2').fileupload({
            dataType: 'json',
            maxFileSize: 1 * 1024 * 1024,
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    // console.log(file.name);
                    $("#avatar2").attr('src', '{{env('ImagePath')}}/images/' + file.name).show();
                    $('#avatar2').attr('index', '/images/' + file.name);
                });
            }
        });
    });
    $(function () {
        $('.submit-audit').click(function () {
            var abc = $('.submit-audit').html();
            /* if(abc=='重新审核'){
             layer.alert('的确很重要', {icon: 2});
             //询问框
             /!*    layer.confirm('您已重新审核？', {
             btn: ['确定','取消'] //按钮
             }, function(){
             layer.msg('的确很重要', {icon: 1});
             }, function(){
             });*!/
             }else{
             return false;
             }*/

            $('.submit-audit').attr('disabled', 'disabled');
            var category = $('#category').html();
            var name = $('.datas-sel-name').val();
            var industry = $('#industry').attr('index') ? $('#industry').attr('index') : '';
            if(industry.indexOf("/")==-1)
            {
                layer.msg('请完善你的擅长领域');
                return false;
            }
            var address = $('#address').html();
            var photo1 = $('#photo1').attr('index');
            var photo2 = $('#avatar2').attr('index');
            if(photo2 == undefined){
                photo2 = 'img/photo2.jpg';
            }

            var brief = $('#brief').val();

            if (name == '' || photo1 == '' || industry == '' || address == '' || brief == '') {
                layer.msg('请把信息填写完整');
                $('.submit-audit').attr('disabled', false);
                return false;
            } else {
                if(brief.length>30 && brief.length<1000){
                }else{
                    $('.submit-audit').attr('disabled', false);
                    $(this).html('提交认证');
                    layer.msg('专家简介字数不符（30-1000字符）',{'icon':5});
                    return false;
                }
                $.ajax({
                    url: "{{asset('/uct_expertData')}}",
                    data: {
                        "category": category,
                        "name": name,
                        "industry": industry,
                        "address": address,
                        "photo1": photo1,
                        "photo2": photo2,
                        "brief": brief
                    },
                    dataType: "json",
                    type: "POST",
                    success: function (data) {
                        if (data.icon == 1) {
                            layer.msg(data.msg, {'time': 2000, 'icon': data.icon}, function () {
                                window.location = '{{asset('/uct_expert2')}}';
                            });
                        } else {
                            layer.msg(data.msg, {'time': 2000, 'icon': data.icon});
                            $('.submit-audit').attr('disabled', false);
                        }
                    }
                })
            }

        });
    })
</script>
@endsection