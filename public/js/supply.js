$(function(){
    var select=new Array();

    //选择专家
    $('.experts-classify').on('click', 'a', function(event) {
        var cliHtml = $(this).html();
        select[0] = 'role';
        select[1] = cliHtml;
        getCondition(select);
    });

    // 选择服务领域
    $('.serve-field-list-show').on('click', 'li', function(event) {
<<<<<<< HEAD
        var serveLi = $(this).html();
=======

        var serveLi = $(this).parent().siblings().html();
>>>>>>> origin/lds
        select[0] = 'supply';
        select[1] = serveLi;
        getCondition(select);
    });
<<<<<<< HEAD
=======

    $('.serve-field .serve-all').on('click', function(event) {
        select[0] = 'supply';
        select[1] = '全部';
        getCondition(select);
    });
>>>>>>> origin/lds

    // 选择地区
    $('.location').on('click', 'a', function(event) {
        var cliHtml = $(this).html();
        select[0] = 'address';
        select[1] = cliHtml;
        getCondition(select);
    });

<<<<<<< HEAD
=======
    //搜索
    $('.list-search .list-search-btn').on('click',function () {
        var searchName = $(this).siblings().val();
        select[0] = 'serveName';
        select[1] = searchName;
        getCondition(select);
    });
    $('.list-search-inp').keydown(function (evnet) {
        if (evnet.keyCode == '13') {
            var searchName = $(this).val();
            select[0] = 'serveName';
            select[1] = searchName;
            getCondition(select);
        }
    });

    //删除
    $('.all-results').on('click', '.all-results-opt', function(event) {
        var key = $(this).text();
        if ($(this).hasClass('all-results-expert')){
            select[0] = 'role';
            select[1] = null;
        } else if ($(this).hasClass('all-results-field')){
            select[0] = 'supply';
            select[1] = null;
        } else if ($(this).hasClass('all-results-location')) {
            select[0] = 'address';
            select[1] = null;
        }
        getCondition(select);
    })

>>>>>>> origin/lds
    // 排序
    $('.sort').on('click', 'a', function(event) {
        var ordername = $(this).text();
        var firsticon = ($('.sort .active span .icon-triangle-copy').hasClass('blue-color'))?'desc':'asc';
        switch (ordername){
            case '发布时间':
                select[0] = 'ordertime';
                select[1] = ordername;
                getCondition(select);
                break;
            case '收藏数':
                select[0] = 'ordercollect';
                select[1] = ordername;
                getCondition(select);
                break;
            case '留言数':
                select[0] = 'ordermessage';
                select[1] = ordername;
                getCondition(select);
                break;
        }

    });


    var getCondition= function(select){
        var Condition=select;
        var role=$(".all-results-expert").text();
        var supply=$(".all-results-field").text();
        var address=$(".all-results-location").text();
        role=(role)?role:null;
        supply=(supply)?supply:null;
        address=(address)?address:null;
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
                    role=(Condition[1]!="全部")?Condition[1]:null;
                    break;
                case "supply":
                    supply=(Condition[1]!="全部")?Condition[1]:null;
                    break;
                case "address":
                    address=(Condition[1]!="全部")?Condition[1]:null;
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

            }
        }
        ordertime=(ordertime)?ordertime:null;
        ordercollect=(ordercollect)?ordercollect:null;
        ordermessage=(ordermessage)?ordermessage:null;
        window.location.href="?role="+role+"&supply="+supply+"&address="+address+"&ordertime="+ordertime+"&ordercollect="+ordercollect+"&ordermessage="+ordermessage;
    }
})