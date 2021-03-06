@extends("layouts.ucenter")
@section("content")
    <script src="{{asset('./FileUpload/js/vendor/jquery.ui.widget.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.iframe-transport.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload-process.js')}}"></script>
    <script src="{{asset('./FileUpload/js/jquery.fileupload-validate.js')}}"></script>
    <div class="main">
        <!-- 会员认证1 / start -->
        <h3 class="main-top">企业认证</h3>
        <div class="ucenter-con">
            <div class="main-right">
                <div class="card-step">
                    <span class="green-circle">1</span>资料提交<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">2</span>资料审核<span class="card-step-cap">&gt;</span>
                    <span class="gray-circle">3</span>认证成功
                </div>
                <div class="expert-certy">
                    <div class="expert-certy-state">
                        <div class="friendly">
                            <p class="center">友好提示</p>
                            <p>1.本平台为企业提供高端专家和重要资源。</p>
                            <p>2.要求企业实名认证、上传有效证照，以便于专家、机构深度互动。</p>
                            <p>3.企业简介中至少包括“主要产品与服务、成立日期、基本规模、行业优势”等内容。</p>
                        </div>
                    </div>
                    <div class="datas datas-member-audit clearfix">
                        <div class="datas-lt">
                            <div class="datas-lt-enter">
                                <div class="datas-sel">
                                    <input class="enterprise-inp" type="text" id="entname" placeholder="请输入企业全称" value="@if(!empty($data)){{$data->enterprisename }}@endif"/>
                                </div>
                                <div class="datas-sel zindex4">
                                    <span class="datas-sel-cap">企业规模</span><a href="javascript:;" class="datas-sel-def" id="size">@if(!empty($data)){{$data->size}} @else 不限@endif</a>
                                    <ul class="datas-list">
                                        <li>不限</li>
                                        <li>20人以下</li>
                                        <li>20-99人</li>
                                        <li>100-499人</li>
                                        <li>500-999人</li>
                                        <li>1000-9999人</li>
                                        <li>10000人以上</li>
                                    </ul>
                                </div>
                                <div class="datas-sel zindex3">
                                    <span class="datas-sel-cap">所在行业</span><a href="javascript:;" class="datas-sel-def" id="industry">@if(!empty($data)){{$data->industry }}@else 不限 @endif</a>
                                    <ul class="datas-list">
                                        <li>不限</li>
                                        <li>IT|通信|电子|互联网</li>
                                        <li>金融业</li>
                                        <li>房地产|建筑业</li>
                                        <li>商业服务</li>
                                        <li>贸易|批发|零售|租赁业</li>
                                        <li>文体教育|工艺美术</li>
                                        <li>生产|加工|制造</li>
                                        <li>交通|运输|物流|仓储</li>
                                        <li>服务业</li>
                                        <li>文化|传媒|娱乐|体育</li>
                                        <li>能源|矿产|环保</li>
                                        <li>政府|非盈利机构</li>
                                        <li>农|林|牧|渔|其他</li>
                                    </ul>
                                </div>
                                <div class="datas-sel zindex2">
                                    <span class="datas-sel-cap">地区</span><a href="javascript:;" class="datas-sel-def" id="address">{{$data->address or '北京'}}</a>
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
                                    <img src="@if(!empty($data)){{env('ImagePath').$data->licenceimage}}@else img/photo1.jpg @endif" class="photo1" id="photo1"/>
                                    <div class="photo-upload">
                                        <div class="photo-btn-box fileinput-button">
                                            <span class="photo-btn-tip">上传营业执照</span>
                                            <input class="fileupload1" type="file" name="files[]" data-url="{{asset('upload')}}" index="@if(!empty($data)){{$data->licenceimage}}@endif" multiple="" accept="image/png, image/gif, image/jpg, image/jpeg" >
                                        </div>
                                        <p class="datas-lt-explain">营业执照仅做认证用，不用做其它用途</p>
                                    </div>
                                </div>
                                <div class="datas-upload-rt">
                                    <img src="@if(!empty($data)){{env('ImagePath').$data->showimage}}@else img/photo2.jpg @endif" class="photo1" id="photo2"/>
                                    <div class="photo-upload">
                                        <div class="photo-btn-box fileinput-button">
                                            <span class="photo-btn-tip">上传宣传照片</span>
                                            <input class="fileupload2" type="file" name="files[]" data-url="{{asset('upload')}}" index="@if(!empty($data)){{$data->showimage}}@endif" multiple="" accept="image/png, image/gif, image/jpg, image/jpeg" >
                                        </div>
                                        <p class="datas-lt-explain">宣传照片用于展示企业，请选择企业Logo或展现企业风采的照片</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="datas-rt  mydata-new">

                            <textarea onkeyup="checkLength(this);"  placeholder="请输入企业简介（30-500字）" cols="30" rows="10" id="content">@if(!empty($data)){{$data->brief}}@endif</textarea>

                        </div>
                    </div>
                    <div class="bottom-btn"><button class="test-btn submit-audit nomt" type="button"  id="submit" ><a href="javascript:;"style="color:#fff;">@if(!empty($data)) 修改资料 @else 提交认证 @endif</a></button></div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(function(){

            $('.fileupload1').fileupload({
                dataType: 'json',
                maxFileSize: 1 * 1024 * 1024,
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        // console.log(file.name);
                        $("#photo1").attr('src','{{env('ImagePath')}}/images/'+file.name).show();
                        $('.fileupload1').attr('index','/images/'+file.name);
                    });
                }
            });

            $('.fileupload2').fileupload({
                dataType: 'json',
                maxFileSize: 1 * 1024 * 1024,
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        // console.log(file.name);
                        $("#photo2").attr('src','{{env('ImagePath')}}/images/'+file.name).show();
                        $('.fileupload2').attr('index','/images/'+file.name);
                    });
                }
            });

            $('.datas-sel-def').click(function() {
                $(this).next('ul').stop().slideToggle();
                $(this).parent().siblings().children('ul').hide();
            });
            $('.datas-list li').click(function() {
                var publishHtml = $(this).html();
                $(this).parent().prev('.datas-sel-def').html(publishHtml);
                $(this).parent().hide();
            });
            $('#submit').on('click',function () {
                $(this).attr('disabled',true);
                $(this).html('正在提交');
                var entname = $('#entname').val();
                var size = $('#size').text();
                var industry = $('#industry').text();
                var address = $('#address').text();
                var content = $('#content').val();
                var img1 = $('.fileupload1').attr('index');
                var img2 = $('.fileupload2').attr('index');
                var id = '{{$data->enterpriseid or null}}';
                if(content.length>30 && content.length<500){
                }else{
                    $('#submit').attr('disabled',false);
                    $(this).html('提交认证');
                    layer.msg('企业简介字数不符',{'icon':5});
                    return false;
                }
                if(entname == '' || size == '不限' ||  industry == '不限' || address == '全国' || content == '' || img1 == '' || img2 == ''){
                    layer.msg('请填写完整的资料',{'icon':0});
                    $('#submit').attr('disabled',false);
                    $(this).html('提交认证');
                } else {
                    $.post('{{asset("uct_member/entverify")}}',{'entid':id,'enterprisename':entname,'size':size,'industry':industry,'address':address,'brief':content,'licenceimage':img1,'showimage':img2},function (data) {
                        if(data.icon == 1){
                            layer.msg(data.msg,{'icon':1,'time':2000},function () {
                                window.location = '{{url('uct_member/member2')}}'+'/'+data.id;
                            });
                        } else {
                            $('#submit').removeAttr('disabled');
                            $('#submit').html('提交认证');
                            layer.msg(data.msg,{'icon':data.icon,'time':2000});

                        }
                    });
                }

            });

        })
    </script>
@endsection