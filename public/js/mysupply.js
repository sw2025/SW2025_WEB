$(function() {
    var select = new Array();

    //选择专家
    $('.experts-classify').on('click', 'a', function (event) {
        var cliHtml = $(this).html();
        select[0] = 'role';
        select[1] = cliHtml;
        getCondition(select);
    });

    // 选择服务领域
    $('.serve-field-list-show').on('click', 'li', function (event) {

        var serveLi = $(this).parent().siblings().html();
        select[0] = 'supply';
        select[1] = serveLi;
        getCondition(select);
    });

    $('#vipneed').on('click', function (event) {
        select[0] = 'level';
        select[1] = 1;
        $(this).attr('index',1);
        getCondition(select);
    });

    $('.serve-field .serve-all').on('click', function (event) {
        select[0] = 'supply';
        select[1] = '不限';
        getCondition(select);
    });

    $('.results-vip').on('click', function (event) {
        select[0] = 'level';
        select[1] = 0;
        $(this).attr('index',0);
        getCondition(select);
    });

    // 选择地区
    $('.location').on('click', 'a', function (event) {
        var cliHtml = $(this).html();
        select[0] = 'address';
        select[1] = cliHtml;
        getCondition(select);
    });

    //搜索
    $('.uct-list-search .uct-list-search-btn').on('click', function () {
        var searchName = $(this).siblings().val();
        select[0] = 'serveName';
        select[1] = searchName;
        getCondition(select);
    });

    $('.uct-list-search-inp').keydown(function (evnet) {
        if (evnet.keyCode == '13') {
            var searchName = $(this).val();
            select[0] = 'serveName';
            select[1] = searchName;
            getCondition(select);
        }
    });

    $('.v-condition a').on('click',function () {
        var action = $(this).attr('index');
        select[0] = 'action';
        select[1] = action;
        getCondition(select);
    });

    $('.my-trace').on('click', 'a', function(event) {
        var action = $(this).attr('index');
        select[0] = 'action';
        select[1] = action;
        getCondition(select);
    });

    /*//动作
    $('.three-icon .icon-row').on('click',function () {
        var action = $(this).attr('index');
        select[0] = 'action';
        /!*switch (action) {
         case '收藏':
         select[1] = 'collect';
         break;
         case '发布':
         select[1] = 'myput';
         break;
         case '留言':
         select[1] = 'message';
         break;
         case '待审核':
         select[1] = 'waitverify';
         break;
         case '拒审核':
         select[1] = 'refuseverify';
         break;
         }*!/
        select[1] = action;
        getCondition(select);
    });
*/
    $('.myask-tabar-a').on('click',function () {
        var cliHtml = $(this).attr('index');
        select[0] = 'who';
        select[1] = cliHtml;
        getCondition(select);
    });


    //删除
    $('.all-results').on('click', '.all-results-opt', function (event) {
        var key = $(this).text();
        if ($(this).hasClass('all-results-expert')) {
            select[0] = 'role';
            select[1] = null;
        } else if ($(this).hasClass('all-results-field')) {
            select[0] = 'supply';
            select[1] = null;
        } else if ($(this).hasClass('all-results-location')) {
            select[0] = 'address';
            select[1] = null;
        }else if ($(this).hasClass('all-results-trace')) {
            select[0] = 'action';
            select[1] = null;
        }else if ($(this).hasClass('all-results-vip')) {
            select[0] = 'level';
            select[1] = 0;
        }
        getCondition(select);
    })


    // 排序
    $('.sort').on('click', 'a', function (event) {
        var ordername = $(this).text();
        var firsticon = ($('.sort .active span .icon-triangle-copy').hasClass('blue-color')) ? 'desc' : 'asc';
        switch (ordername) {
            case '发布时间':
                select[0] = 'ordertime';
                select[1] = firsticon;
                getCondition(select);
                break;
            case '收藏数':
                select[0] = 'ordercollect';
                select[1] = firsticon;
                getCondition(select);
                break;
            case '留言数':
                select[0] = 'ordermessage';
                select[1] = firsticon;
                getCondition(select);
                break;
        }

    });


    var getCondition= function(select){
        var Condition=select;
        var searchName=$(".uct-list-search-inp").val();
        var role=$(".all-results-expert").text();
        var supply=$(".all-results-field").text();
        var address=$(".all-results-location").text();
        var action=$(".my-trace .active").attr('index');
        if(!action){
            action = $('.v-condition .active').attr('index');
        }
        var level=$('#vipneed').attr('index');
        var who=$(".myask-tabar .active").attr('index');

        if(searchName == '请输入要搜索的供求信息关键字'){
            searchName = '';
        }
        searchName=(searchName)?searchName:null;
        role=(role)?role:null;
        who=(who)?who:null;
        supply=(supply)?supply:null;
        address=(address)?address:null;
        action=(action != '不限')?action:null;
        if( $(".sort").children('a').hasClass('active')){
            var ordername = $('.sort .active ').text();
            var firsticon = ($('.sort .active span .icon-triangle-copy').hasClass('blue-color'))?'desc':'asc';
            switch (ordername){
                case '发布时间':
                    var ordertime = firsticon;
                    break;
                case '收藏数':
                    var ordercollect = firsticon;
                    break;
                case '留言数':
                    var ordermessage = firsticon;
                    break;
            }
        }

        if(Condition.length!=0){
            switch(Condition[0]){
                case "role":
                    role=(Condition[1]!="不限")?Condition[1]:null;
                    break;
                case "serveName":
                    searchName=Condition[1];
                    break;
                case "who":
                    who=Condition[1];
                    break;
                case "supply":
                    supply=(Condition[1]!="不限")?Condition[1]:null;
                    break;
                case "address":
                    address=(Condition[1]!="不限")?Condition[1]:null;
                    break;
                case "action":
                    action=(Condition[1]!="不限")?Condition[1]:null;
                    break;
                case "ordertime":
                    ordertime=Condition[1];
                    break;
                case "ordercollect":
                    ordercollect=Condition[1];
                    break;
                case "ordermessage":
                    ordermessage=Condition[1];
                    break;
                case 'level':
                    level = Condition[1];
                    break;
            }
        }
        ordertime=(ordertime)?ordertime:null;
        ordercollect=(ordercollect)?ordercollect:null;
        ordermessage=(ordermessage)?ordermessage:null;
        window.location.href="?level="+level+"&who="+who+"&searchname="+searchName+'&action='+action+"&role="+role+"&supply="+supply+"&address="+address+"&ordertime="+ordertime+"&ordercollect="+ordercollect+"&ordermessage="+ordermessage;
    }
})

function fnc_collect (supplyid,action,obj) {

    $.post('/dealcollect',{'supplyid':supplyid,'action':action},function (data) {
        if(data == 'nologin'){
            layer.confirm('您还未登陆是否去登陆？', {
                btn: ['去登陆','暂不需要'], //按钮
                skin:'layui-layer-molv'
            }, function(){
                window.location.href='/login';
            }, function(){
                layer.close();
                $(obj).attr("title","收藏");
                $(obj).removeClass('red');
                if($(obj).hasClass('done')){
                    $(obj).text("收藏");
                    $(obj).removeClass('done');
                }
                $(obj).attr('disabled',false);
            });
        } else if(data == 'success') {
            if(action == 'collect'){
                var number = $(obj).text().trim() == '' ? 0:$(obj).text();
                $(obj).children('span').text(parseInt(number)+1);
                if($(obj).hasClass('collect-state')){
                    $(obj).html('已收藏');
                    $(obj).addClass('done');
                } else {
                    $(obj).attr("title","已收藏");
                    $(obj).addClass('red');
                }
                layer.msg('收藏成功');
                $(obj).attr('disabled',false);
            } else {
                var number = $(obj).text() == 1 ? ' ' : parseInt($(obj).text())-1;
                $(obj).children('span').text(number);
                if($(obj).hasClass('collect')){
                    $(obj).attr("title","收藏");
                    $(obj).removeClass('red');
                } else {
                    $(obj).html('收藏');
                    $(obj).removeClass('done');
                }
                layer.msg('取消收藏成功');
                $(obj).attr('disabled',false);
            }
        } else {
            $(obj).removeClass('red');
            layer.msg('收藏失败请您登陆或者这是一个异常需求',{'icon':0});

        }
    });
}

$('.details-message .submit').on('click',function () {
    var textarea = $(this).parent().siblings('textarea');
    var needid = textarea.attr('id');
    var content = textarea.val();
    $(this).attr('disabled',"true");
    replymessage({'needid':needid,'content':content,'parentid':0},this);
});

function replymessage (datas,obj) {
    if(!datas.content.trim().length){
        layer.msg('请输入留言内容');
        $(obj).attr('disabled',false);
        return false;
    }
    $.post('/replymessage',datas,function (data) {
        if(data == 'success'){
            layer.msg('回复成功',{time:2000},function () {
                var url = window.location.href;
                url = url.replace(/\#reply/,'');
                window.location = url;
            });
        }else if(data == 'nologin') {
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
                $(obj).attr('disabled',false);
            });
        } else {
            layer.msg('处理失败');
        }
    });
}
