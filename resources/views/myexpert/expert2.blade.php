@extends("layouts.ucenter4")
@section("content")
    <link rel="stylesheet" type="text/css" href="{{asset('css/uctexperts.css')}}" />

    <!-- 公共header / start -->
    <script src="{{asset('./FileUpload/js/vendor/jquery.ui.widget.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload-process.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload-validate.js')}}"></script>

            <div class="main">
                <!-- 专家认证2 / start -->
                <h3 class="main-top">专家认证</h3>
                <div class="ucenter-con">
                    <div class="main-right">
                        <div class="card-step">
                            <span class="gray-circle">1</span>资料提交<span class="card-step-cap">&gt;</span>
                            <span class="green-circle">2</span>资料审核<span class="card-step-cap">&gt;</span>
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
                            <div class="datas datas-audit">
                                <div class="datas-lt">
                                    <div class="datas-lt-enter">
                                        <div class="datas-sel zindex1">
                                            <span class="datas-sel-cap">专家分类</span><a href="javascript:;" class="datas-sel-def verify-default category">{{$data->category}}</a>
                                        </div>
                                        <div class="datas-sel">
                                            <span class="datas-sel-cap">名称</span>
                                            <input class="datas-sel-name" readonly="readonly" type="text" value="{{$data->expertname}}" style="color:#666;margin-left:50px;"/>
                                        </div>
                                        <div class="datas-sel zindex2" style="overflow: hidden;">
                                            <span class="datas-sel-cap">擅长领域</span><a href="javascript:;" class="datas-sel-def verify-default">
                                                    @if($data->domain1=='找资金')
                                                        投融资
                                                    @elseif($data->domain1=='找技术')
                                                        科研技术
                                                    @elseif($data->domain1=='定战略')
                                                        战略管理
                                                    @else
                                                        市场资源
                                                    @endif
                                                -{{join('/',explode(',',$data->domain2))}}/</a>
                                        </div>
                                        <div class="datas-sel zindex3">
                                            <span class="datas-sel-cap">地区</span><a href="javascript:;" class="datas-sel-def verify-default">{{$data->address}}</a>
                                        </div>
                                    </div>
                                    <div class="datas-upload-box clearfix">
                                        <div class="datas-upload-lt">
                                            <img src="{{env('ImagePath').$data->licenceimage}}" class="photo1" />
                                            
                                        </div>
                                        <div class="datas-upload-rt">
                                            <img src="{{env('ImagePath').$data->showimage}}" class="photo1" />
                                            
                                        </div>
                                    </div>
                                    <div class="expert-certy-state tal">
                                        <i class="iconfont icon-chenggong"></i>
                                        <span class="publish-need-blue">
                                            <em>正在审核</em>IS REVIEWING
                                        </span>
                                    </div>
                                </div>
                                <div class="datas-rt htxt1 vat">
                                    <textarea placeholder="请输入专家描述" readonly="readonly" cols="30" rows="10">{{$data->brief}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    if($(".category").text()=="专家" || $(".category").text()=="企业家" ){
        $(".categoryExpert").show();
        $(".categoryNoExpert").hide();
    }else{
        $(".categoryExpert").hide();
        $(".categoryNoExpert").show();
    }
</script>
    <!-- 专家认证2 / end -->
    <!-- 公共footer / end -->
@endsection