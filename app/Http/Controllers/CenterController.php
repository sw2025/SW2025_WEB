<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpSpec\Exception\Exception;

class CenterController extends Controller
{
    /**基本资料
     * @return mixed
     */
    public function index(){
        $userId=session("userId");
        $data=DB::table("T_U_USER")->where("userid",$userId)->first();
        return view("ucenter.index",compact("data"));
    }

    /**修改手机号
     * @return mixed
     */
    public function  changeTel(){
        return view("ucenter.changeTel");
    }

    /**修改手机号2
     * @return mixed
     */
    public function  changeTel2(Request $request){
       if(session('phoneCode')){
           $request->session()->forget('phoneCode');
            return view("ucenter.changeTel2");
        }else{
           return redirect('/uct_basic/changeTel');
        }

    }

    /**修改密码
     * @return mixed
     */
    public  function changePwd(){
        return view("ucenter.changePwd");
    }
    /**检查原密码
     * @return mixed
     */
    public function inspectPwd(Request $request){
        $res=array();
        $userId=$_POST['userId'];
        $passWord=$_POST['oldPassWord'];
        $result=DB::table("t_u_user")
            ->where("userid",$userId)
            ->where("passWord",md5($passWord))
            ->first();
        if($result){
            $res['code']="success";
            session(["phoneCode"=>38]);
            return $res;
        }else{
            $res['code']="error";
            return $res;
        }
    }


    /**修改密码处理
     * @param Request $request
     * @return array
     */
    public function updatePwd(Request $request){
        $res=array();
        $userId=$_POST['userId'];
        $passWord=$_POST['passWord'];
        $result=DB::table("t_u_user")->where("userid",$userId)->update([
            "password"=>md5($passWord),
            "updated_at"=>date("Y-m-d H:i:s",time())
        ]);
        if($result){
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }

    /**
     * 充值提现
     * @return mixed
     */
    public function recharge(){
        $userId=session("userId");
        $enterpriseId=DB::table("t_u_enterprise")->where("userid",$userId)->pluck("enterpriseid");
        if($enterpriseId){
            $enterprise=DB::table("t_u_enterprise")
                ->leftJoin("t_u_enterpriseverify","t_u_enterprise.enterpriseid","=","t_u_enterpriseverify.enterpriseid")
                ->where("t_u_enterprise.enterpriseid",$enterpriseId)
                ->orderBy("t_u_enterpriseverify.id","desc")
                ->take(1)
                ->pluck('configid');
            if($enterprise==3){
                $datas=DB::table("t_u_enterprisemember")->leftJoin("t_u_memberright","t_u_enterprisemember.memberid","=","t_u_memberright.memberid")->where("enterpriseid",$enterpriseId)->first();
                if(!empty($datas) && $datas->endtime>=date("Y-m-d H:i:s")){
                    if($datas->memberid!=1 && $datas->memberid!=2){
                        $eventCount="无限";
                    }else{
                        $eventCount=$datas->eventcount;
                    }
                    $members=$datas->typename;
                    $consultCount=$datas->consultcount;
                }else{
                    $members="非会员";
                    $eventCount=0;
                    $consultCount=0;
                }
            }else{
                $members="非会员";
                $eventCount=0;
                $consultCount=0;
            }
        }else{
            $members="非会员";
            $eventCount=0;
            $consultCount=0;
        }
        $bankcard=DB::table("t_u_bank")->where("userid",$userId)->pluck("bankcard");
        $state=DB::table("t_u_bank")->where("userid",$userId)->pluck("state");
        return view("ucenter.recharge",compact("members","eventCount","consultCount","bankcard","state"));
    }

    /**充值
     * @return mixed
     */
    public function rechargeMoney(){
        $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();
        return view("ucenter.rechargeMoney",compact("memberrights"));
    }

    /**提现
     * @return mixed
     */
    public function cash(){
        $userId=session("userId");
        $incomes=DB::table("T_U_BILL")->where(["userid"=>$userId,"type"=>"收入"])->sum("money");
        $pays=DB::table("T_U_BILL")->where("userid",$userId)->whereIn("type",["支出","在途"])->sum("money");
        $balance=$incomes-$pays;
        return view("ucenter.cash",compact("balance"));
    }

    /**申请提现
     * @return array
     */
    public function applicationCashs(){
        $res=array();
        $money=$_POST['money'];
        $userId=$_POST['userId'];
        $payno=$this->getPayNum("提现");
        $result=DB::table("t_u_bill")->insert([
            "userid"=>$userId,
            "type"=>"在途",
            "channel"=>"提现申请",
            "money"=>$money,
            "payno"=>$payno,
            "billtime"=>date("Y-m-d H:i:s",time()),
            "brief"=>"提现",
            "created_at"=>date("Y-m-d H:i:s",time()),
            "updated_at"=>date("Y-m-d H:i:s",time()),
        ]);
        if($result){
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }


    /**我的信息
     * @return mixed
     */
    public  function  myinfo(Request $request){
        $userId = session("userId");
        $datas = DB::table('t_m_systemmessage')->where(['receiveid' => $userId])->whereIn('state',[0,1])->orderBy('id','desc')->paginate(6);
        if($request->ajax()){
            return $datas;
        }
        return view("ucenter.myinfo",compact('datas'));
    }

    /**修改信息已读或者删除
     * @param Request $request
     * @return array
     */
    public function flagRead (Request $request) {
        if($request->ajax()){
            $data = $request->input('data');
            $state = $request->input('state');
            $userId = session('userId');
            $res = DB::table('t_m_systemmessage')->whereIn('id',$data)->where('receiveid',$userId)->update([
                'state' => $state,
                'updated_at' => date("Y-m-d H:i:s",time())
            ]);
            if($state == 1){
                return ['msg' => '修改已读成功','icon' => 1];
            } elseif ($state == 2){
                return ['msg' => '删除消息成功','icon' => 1];
            }

        }
        return ['msg' => '非法请求','icon' => 2];
    }
    /**我的需求
     * @return mixed
     */
    public function  myNeed(Request $request){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        $datas = DB::table('t_n_need as need')
            ->leftJoin('view_userrole as view','view.userid', '=','need.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_user as user','need.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->leftJoin('view_needcollectcount as coll','coll.needid' ,'=' ,'need.needid')
            ->leftJoin('view_needmesscount as mess','mess.needid' ,'=' ,'need.needid')
            ->leftJoin('view_needstatus as status','status.needid' ,'=' ,'need.needid')
            ->select('need.*','view.role','ent.enterprisename','ent.showimage as entimg','status.configid as flag','coll.count as collcount','mess.count as messcount','ext.showimage as extimg','ext.expertname');
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_n_collectneed')->where(['userid' => session('userId'),'remark' => 1])->lists('needid');
        }
        //用户发布的数量
        $putcount = DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',3)->count();
        //用户回复的数量
        $msgcount = count(DB::table('t_n_messagetoneed as need')->where('userid',session('userId'))->groupBy('needid')->lists('needid'));
        //用户待审核的供求的数量
        $waitcount= DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',1)->count();
        //用户拒审核的供求的数量
        $refusecount = DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',2)->count();
        //商情的数量
        $vipneedcount = DB::table('t_n_pushneed')->where('userid',session('userId'))->count();
        //判断是否为http请求
        if(!empty($get = $request->input())){
            $levelwhere = ['need.level' => 0];
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;
            $action = ( isset($get['action']) && $get['action'] != "null") ? $get['action'] : null;
            $who = ( isset($get['who']) && $get['who'] != "null") ? $get['who'] : null;
            $level = (isset($get['level']) && $get['level'] != "null") ? $get['level'] : 0;
            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("needtype"=>$role):array();
            $supplywhere = !empty($supply)?array("need.domain1"=>$supply[0],'need.domain2' => $supply[1]):array();


            $obj = $datas->where($rolewhere)->where($supplywhere);
            if(!empty($address)){
                $obj = $obj->whereRaw('ext.address ="'.$address.'" or ent.address = "'.$address.'"');
            }
            if(!empty($action)){
                switch($action){
                    case 'collect':
                        $obj = $obj->whereRaw('need.needid in (select needid from t_n_collectneed  where userid='.session('userId').' and remark=1)');
                        //$obj = $obj->where('colneed.userid',session('userId'))->where('colneed.remark',1);
                        $action  = '已收藏';
                        break;
                    case 'myput':
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',3);
                        $action  = '已发布';
                        $who = 'my';
                        $levelwhere = [];
                        break;
                    case 'message':
                        $obj = $obj->whereRaw('need.needid in (select  needid from t_n_messagetoneed  where userid='.session('userId').' group by needid)');
                        $action = '已留言';
                        break;
                    case 'waitverify':
                        $action  = '待审核';
                        $who = 'my';
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',1);
                        break;
                    case 'refuseverify':
                        $action  = '审核失败';
                        $who = 'my';
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',2);
                        break;
                }
            } else {
                $obj = $obj->where('status.configid',3);
            }

            if(!empty($who)){
                switch($who){
                    case 'my':
                        $obj = $obj->where('need.userid',session('userId'));
                        break;
                    case 'other':
                        $obj = $obj->where('need.userid','<>',session('userId'));
                        break;
                }
            } else {
                $obj = $obj->where('need.userid','<>',session('userId'));
            }


            if($level == 1){
                $levelwhere = ['need.level' => 1];
                $pushneedlist = DB::table('t_n_pushneed')->where('userid',session('userId'))->lists('needid');
                $obj = $obj->whereIn("need.needid",$pushneedlist);
            }

            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("need.brief","like","%".$searchname."%");
            }

            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('need.needtime',$ordertime);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } else {
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->where($levelwhere)->paginate(4);
            return view("ucenter.newMyNeed",compact('vipneedcount','level','who','waitcount','refusecount','cate','searchname','msgcount','datas','role','action','collectids','putcount','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->where('need.level',0)->where('status.configid',3)->where('need.userid','<>',session('userId'))
            ->orderBy("need.needtime",'desc')
            ->paginate(4);
        $level = 0;
        $ordertime = 'desc';
        return view("ucenter.newMyNeed",compact('level','vipneedcount','waitcount','refusecount','cate','datas','ordertime','collectids','putcount','msgcount'));
    }
    /**我的需求
     * @return mixed
     */
    public function  myNeed2(Request $request){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        $datas = DB::table('t_n_need as need')
            ->leftJoin('view_userrole as view','view.userid', '=','need.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_user as user','need.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->leftJoin('view_needcollectcount as coll','coll.needid' ,'=' ,'need.needid')
            ->leftJoin('view_needmesscount as mess','mess.needid' ,'=' ,'need.needid')
            ->leftJoin('view_needstatus as status','status.needid' ,'=' ,'need.needid')
            ->select('need.*','view.role','ent.enterprisename','ent.showimage as entimg','status.configid as flag','coll.count as collcount','mess.count as messcount','ext.showimage as extimg','ext.expertname');
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_n_collectneed')->where(['userid' => session('userId'),'remark' => 1])->lists('needid');
        }
        //用户发布的数量
        $putcount = DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',3)->count();
        //用户回复的数量
        $msgcount = count(DB::table('t_n_messagetoneed as need')->where('userid',session('userId'))->groupBy('needid')->lists('needid'));
        //用户待审核的供求的数量
        $waitcount= DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',1)->count();
        //用户拒审核的供求的数量
        $refusecount = DB::table('view_needstatus as need')->where('userid',session('userId'))->where('need.configid',2)->count();
        //商情的数量
        $vipneedcount = DB::table('t_n_pushneed')->where('userid',session('userId'))->count();
        //判断是否为http请求
        if(!empty($get = $request->input())){
            $levelwhere = ['need.level' => 0];
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;
            $action = ( isset($get['action']) && $get['action'] != "null") ? $get['action'] : null;
            $who = ( isset($get['who']) && $get['who'] != "null") ? $get['who'] : null;
            $level = (isset($get['level']) && $get['level'] != "null") ? $get['level'] : 0;
            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("needtype"=>$role):array();
            $supplywhere = !empty($supply)?array("need.domain1"=>$supply[0],'need.domain2' => $supply[1]):array();


            $obj = $datas->where($rolewhere)->where($supplywhere);
            if(!empty($address)){
                $obj = $obj->whereRaw('ext.address ="'.$address.'" or ent.address = "'.$address.'"');
            }
            if(!empty($action)){
                switch($action){
                    case 'collect':
                        $obj = $obj->whereRaw('need.needid in (select needid from t_n_collectneed  where userid='.session('userId').' and remark=1)');
                        //$obj = $obj->where('colneed.userid',session('userId'))->where('colneed.remark',1);
                        $action  = '已收藏';
                        break;
                    case 'myput':
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',3);
                        $action  = '已发布';
                        $who = 'my';
                        $levelwhere = [];
                        break;
                    case 'message':
                        $obj = $obj->whereRaw('need.needid in (select  needid from t_n_messagetoneed  where userid='.session('userId').' group by needid)');
                        $action = '已留言';
                        break;
                    case 'waitverify':
                        $action  = '待审核';
                        $who = 'my';
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',1);
                        break;
                    case 'refuseverify':
                        $action  = '审核失败';
                        $who = 'my';
                        $obj = $obj->where('need.userid',session('userId'))->where('status.configid',2);
                        break;
                }
            } else {
                $obj = $obj->where('status.configid',3);
            }

            if(!empty($who)){
                switch($who){
                    case 'my':
                        $obj = $obj->where('need.userid',session('userId'));
                        break;
                    case 'other':
                        $obj = $obj->where('need.userid','<>',session('userId'));
                        break;
                }
            } else {
                $obj = $obj->where('need.userid','<>',session('userId'));
            }


            if($level == 1){
                $levelwhere = ['need.level' => 1];
                $pushneedlist = DB::table('t_n_pushneed')->where('userid',session('userId'))->lists('needid');
                $obj = $obj->whereIn("need.needid",$pushneedlist);
            }

            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("need.brief","like","%".$searchname."%");
            }

            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('need.needtime',$ordertime);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } else {
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->where($levelwhere)->paginate(4);
            return view("ucenter.newMyNeed",compact('vipneedcount','level','who','waitcount','refusecount','cate','searchname','msgcount','datas','role','action','collectids','putcount','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->where('need.level',0)->where('status.configid',3)->where('need.userid','<>',session('userId'))
            ->orderBy("need.needtime",'desc')
            ->paginate(4);
        $level = 0;
        $ordertime = 'desc';
        return view("ucenter.newMyNeed",compact('level','vipneedcount','waitcount','refusecount','cate','datas','ordertime','collectids','putcount','msgcount'));
    }

    /**需求详情
     * @return mixed
     */
    public function  needDetail($supplyId){
        //取出指定的供求信息
        $datas = DB::table('t_n_need as need')
            ->leftJoin('view_userrole as view','view.userid', '=','need.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_user as user','user.userid' ,'=' ,'need.userid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->select('ent.brief as desc1','ext.brief as desc2','need.*','ent.enterprisename','ent.address','ext.expertname','user.phone','ent.showimage as entimg','ext.showimage as extimg');
        //获取该供求的当前状态
        $configid = DB::table('t_n_needverify as need')->where('needid',$supplyId)->orderBy('id','desc')->select('configid')->first();
        $obj = clone $datas;
        $datas = $datas->where('needid',$supplyId)->first();

        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_n_collectneed')->where(['userid' => session('userId'),'remark' => 1])->lists('needid');
        }

        //查询留言的信息
        $message = DB::table('t_n_messagetoneed as msg')
            ->leftJoin('view_userrole as view','view.userid', '=','msg.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->leftJoin('t_u_user as user','user.userid' ,'=' ,'msg.userid')
            ->leftJoin('t_u_user as user2','user2.userid' ,'=' ,'msg.use_userid')
            ->where('needid',$supplyId)
            ->where('msg.isdelete',0)
            ->select('msg.*','ent.enterprisename','ext.expertname','user.avatar','user.nickname','user.phone','user2.nickname as nickname2','user2.phone as phone2')
            ->orderBy('messagetime','desc')
            ->get();
        //分组取出每个回复的数量
        $getmsgcount = DB::table('t_n_messagetoneed')->where('needid',$supplyId)->groupBy('parentid')->select(DB::raw('parentid ,count(*) as count'))->having('parentid','<>',0)->get();
        $msgcount = [];
        foreach ($getmsgcount as $k => $v) {
            $msgcount[$v->parentid] = $v->count;
        }
        //获取供求的收藏的数量
        $collcount = DB::table('view_needcollectcount')->where('needid',$supplyId)->first();
        $collcount = $collcount ? $collcount->count : 0;
        $cryptid = Crypt::encrypt(session('userId').$supplyId);
        return view("ucenter.needDetail",compact('datas','message','configid','collectids','msgcount','collcount','cryptid'));
    }

    /**
     * 解决需求
     */
    public function solveNeed (Request $request){
        //判断是否ajax请求
        if($request->ajax()){
            $data = $request->input();
            $supplyid = $data['supplyid'];
            $mdid = $data['mdid'];
            //判断你是否登陆 和 验证crypt解密对比
            if(!empty(session('userId')) && session('userId').$supplyid == Crypt::decrypt($mdid)){
                //确认这个需求是本人的
                $res = DB::table('t_n_need')->where('userid',session('userId'))->where('needid',$supplyid)->first();
                //防止连续点击触发多次插入 需要查询确定
                $res_repeat = DB::table('t_n_needverify')->where('configid',4)->where('needid',$supplyid)->first();
                if($res_repeat){
                    return ['msg' => '请勿重复提交','icon' => 2];
                }
                if($res){
                    $result = DB::table('t_n_needverify')->insert([
                        'needid' => $supplyid,
                        'configid' => 4,
                        'verifytime' => date('Y-m-d H:i:s',time()),
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    if($result){
                        return ['msg' => '处理成功','icon' => 1];
                    }
                }
                return ['msg' => '处理失败','icon' => 2];
            }
            return ['msg' => '非法访问','icon' => 2];
        }
        return ['msg' => '非法访问','icon' => 2];
    }

    /**发布需求
     * @return mixed
     */
    public function  supplyNeed($needid = null){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        if($needid){
            //验证是否该供求的状态为拒绝 否则重定向到首页
            $res = DB::table('view_needstatus')->where(['needid' => $needid,'configid' => 2])->first();
            if($res){
                $info = DB::table('t_n_need')->where('needid',$needid)->first();
                $data = DB::table('t_n_needverify')->where('needid',$needid)->orderBy('id','desc')->first();
                $info->error = $data->remark;
                return view("ucenter.supplyNeed",compact('cate','info'));
            } else {
                return redirect('/');
            }
        }
        return view("ucenter.supplyNeed",compact('cate'));
    }

    /**
     * 需求审核页
     */
    public function examineNeed ($needid = null) {
        if($needid){
            //验证是否该供求的状态为待审核 否则重定向到首页
            $res = DB::table('view_needstatus')->where(['needid' => $needid,'configid' => 1])->first();
            if($res){
                $lastdata = DB::table('t_n_need')->where(['needid' => $needid])->orderBy('needid','desc')->first();
            } else {
                return redirect('/');
            }
        } else {
            return redirect('/');
        }

        return view('ucenter.examineNeed',compact('lastdata'));
    }

    /**新增需求
     * @param Request $request
     */
    public function addNeed (Request $request) {
        //判断是否为ajax请求
        if($request->ajax()){
            //判断是否登陆
            if(!empty(session('userId'))){
                $data = $request->input();
                $level = trim($data['needlevel']) == '普通' ? 0 : 1;
                $domain1 = trim(explode('/',$data['domain'])[0]);
                $domain2 = trim(explode('/',$data['domain'])[1]);
                if($data['role'] == '企业'){
                    $verifyent = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterpriseid;
                    if($verifyent){
                        $verentconfig = DB::table('t_u_enterpriseverify')->where('enterpriseid',$verifyent)->orderBy('id','desc')->first()->configid;
                        if($verentconfig != 3){

                            return ['msg' => '您的企业未通过认证,暂不能发布','icon' => 2];
                        }
                    } else {
                        return ['msg' => '您还未成为企业~','icon' => 2];

                    }
                } elseif ($data['role'] == '专家') {
                    $verifyent = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertid;
                    if($verifyent){
                        $verentconfig = DB::table('t_u_expertverify')->where('expertid',$verifyent)->orderBy('id','desc')->first()->configid;
                        if($verentconfig != 2){

                            return ['msg' => '您的专家身份未通过认证,暂不能发布','icon' => 2];
                        }
                    } else {
                        return ['msg' => '您还未成为专家~','icon' => 2];

                    }
                } else{
                    return ['msg' => '非法操作','icon' => 2];
                }
                if(!empty($data['needid'])){

                    DB::beginTransaction();
                    try{
                        DB::table('t_n_need')->where(['needid' => $data['needid']])->update([
                            'domain1' => $domain1,
                            'domain2' => $domain2,
                            'brief' => trim($data['content']),
                            'updated_at' => date('Y-m-d H:i:s',time())
                        ]);
                        DB::table('t_n_needverify')
                            ->insert([
                                'needid' => $data['needid'],
                                'configid' => 1,
                                'verifytime' => date('Y-m-d H:i:s',time()),
                                'updated_at' => date('Y-m-d H:i:s',time())
                            ]);
                        DB::commit();
                        $res = PublicController::ValidationAudit('need',['needid' => $data['needid']]);
                        return $res;

                    }catch(Exception $e)
                    {
                        DB::rollBack();
                        return ['msg' => '处理失败','icon' => 2];
                    }


                }

                //开启事务
                DB::beginTransaction();
                try{
                    $needid = DB::table('t_n_need')->insertGetId([
                        'userid' => session('userId'),
                        'domain1' => $domain1,
                        'domain2' => $domain2,
                        'level' => $level,
                        'brief' => $data['content'],
                        'needtime' => date('Y-m-d H:i:s',time()),
                        'needtype' => $data['role'],
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    DB::table('t_n_needverify')->insert([
                        'needid' => $needid,
                        'configid' => 1,
                        'verifytime' => date('Y-m-d H:i:s',time()),
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    DB::commit();
                    $res = PublicController::ValidationAudit('need',['needid' => $needid]);
                    return $res;

                }catch(Exception $e)
                {
                    DB::rollBack();
                    return ['msg' => '处理失败','icon' => 2];
                }
            }
            return ['msg' => '请登录','icon' => 2];
        }
        return ['msg' => '非法访问','icon' => 2];
    }


    /**
     * 验证发布需求的身份是否成立
     *
     */
    public function verifyPutNeed(Request $request)
    {
        if($request->ajax()){
            if(empty(session('userId'))){
                return ['msg' => '未登录','icon' => 2,'type' => 4];
            }
            $data = $request->only('role');
            if($data['role'] == '企业'){
                $verifyent = DB::table('t_u_enterprise')->where('userid',session('userId'))->first();
                if(!empty($verifyent->enterpriseid)){
                    $verentconfig = DB::table('t_u_enterpriseverify')->where('enterpriseid',$verifyent->enterpriseid)->orderBy('id','desc')->first();
                    if(empty($verentconfig->configid) || $verentconfig->configid != 3){

                        return ['msg' => '您的企业未通过认证,暂不能发布','icon' => 2,'type' => 1];
                    }
                } else {
                    return ['msg' => '您还未成为企业~是否成为企业','icon' => 2,'type' => 2,'url' => url('uct_member')];
                }
            } elseif ($data['role'] == '专家') {
                $verifyent = DB::table('t_u_expert')->where('userid',session('userId'))->first();
                if(!empty($verifyent->expertid)){
                    $verentconfig = DB::table('t_u_expertverify')->where('expertid',$verifyent->expertid)->orderBy('id','desc')->first();
                    if(empty($verentconfig->configid) || $verentconfig->configid != 2){
                        return ['msg' => '您的专家身份未通过认证,暂不能发布','icon' => 2,'type' => 1];
                    }
                } else {
                    return ['msg' => '您还未成为专家~是否成为专家','icon' => 2,'type' => 2,'url' => url('uct_expert')];
                }
            } else {
                return ['msg' => '非法操作FF0001','icon' => 2,'type' => 3];
            }
        } else {
            return ['msg' => '非法操作FF0000','icon' => 2,'type' => 3];
        }

    }

    /**个人中心获取验证码
     * @return array
     */
    public function  getcodes(){
        $res=array();
        $userId=$_POST['userId'];
        $action =$_POST['action'];
        if($action=='change2'){
            $phone=$_POST['newPhone'];
        }else{
            $phone = DB::table("T_U_USER")->where("userid",$userId)->pluck("phone");
        }
        switch ($action){
            case "registr":
                $user = User::where('phone', $phone)->first();
                if($user) {
                    $res['code']="phone";
                    $res['msg']="该手机号已经注册!";
                    return $res;
                }
                break;
            case "change1":
                $user = User::where('phone', $phone)->first();
                if(!$user) {
                    $res['code']="phone";
                    $res['msg']="该手机号不存在!";
                    return $res;
                }
                break;
            case "change2":
                $user = User::where('phone', $phone)->first();
                if($user) {
                    $res['code']="phone";
                    $res['msg']="该手机号已经注册!";
                    return $res;
                }
                break;
        }
        // 获取验证码
        $randNum = $this->__randStr(6, 'NUMBER');

        // 验证码存入缓存 10 分钟
        $expiresAt = 20;

        Cache::put($phone, $randNum, $expiresAt);

        // // 短信内容
        // $smsTxt = '验证码为：' . $randNum . '，请在 10 分钟内使用！';

        // 发送验证码短信
        $res = $this->_sendSms($phone, $randNum, $action);

        return $res;
    }

    /**修改手机号验证验证码
     * @return array
     */
    public function  returnCode(){
        $userId=$_POST['userId'];
        $code=$_POST['code'];
        $str=array();
        $phone=DB::table("T_U_USER")->where("userid",$userId)->pluck("phone");
        if(Cache::has($phone)){
            $smsCode=Cache::get($phone);
            if($smsCode!=$code){
                $str['code']="code";
                $str['msg']="验证码输入错误!";
                return $str;
            }else{
                $str['code']="success";
                return $str;
            }
        }else{
            $str['code']="code";
            $str['msg']="没有生成验证码,稍后重试!";
            return $str;
        }
    }

    /**
     * 修改手机号2
     * @return array
     */
    public function changeNewPhone(Request $request){

        $userId=$_POST['userId'];
        $newPhone=$_POST['phone'];
        $code=$_POST['code'];
        $str=array();
        $phone=DB::table("T_U_USER")->where("userid",$userId)->pluck("phone");
        if(Cache::has($newPhone)){
            $smsCode=Cache::get($newPhone);
            if($smsCode!=$code){
                $str['code']="code";
                $str['msg']="验证码输入错误!";
                return $str;
            }else{
                $str=$this->verifyPhones($newPhone,$userId,$request);
                return $str;
            }
        }else{
            $str['code']="code";
            $str['msg']="没有生成验证码,稍后重试!";
            return $str;
        }

    }

    /**验证新的手机号
     * @param $newPhone
     * @param $userId
     * @return array
     */
    public  function verifyPhones($newPhone,$userId,$request){
        $result=array();
        $counts=DB::table("T_U_USER")->where("phone",$newPhone)->count();
        if($counts){
            $result['code']="phone";
            $result['msg']="该手机号已经注册!";
            return $result;
        }
        $updates=DB::table("T_U_USER")->where("userid",$userId)->update([
            "phone"=>$newPhone,
            "updated_at"=>date("Y-m-d H:i:s",time()),
        ]);
        if($updates){
            $request->session()->flush();
            $result['code']="success";
            return $result;
        }else{
            $result['code']="phone";
            $result['msg']="修改失败,重新修改";
            return $result;
        }
    }

    /**修改基本资料
     * @return array
     */
    public function changeBasics(){
        $nickName=!empty($_POST['nickName'])?$_POST['nickName']:"";
        $avatar=!empty($_POST['myAvatar'])?$_POST['myAvatar']:"/images/avatar.jpg";
        $userId=$_POST["userId"];
        $res=array();
        $result=DB::table("T_U_USER")->where("userid",$userId)->update([
            "nickname"=>$nickName,
            "avatar"=>$avatar,
            "updated_at"=>date("Y-m-d H:i:s",time())
        ]);
        if($result){
            $accid=DB::table("t_u_user")->where("userid",$userId)->pluck("accid");
            $avatars=env('ImagePath').$avatar;
            \UserClass::updateName($accid,$nickName,$avatars);
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }

    /**添加银行卡
     * @return mixed
     */
    public  function  card(){
        return view("ucenter.card");
    }

    /**更改银行卡号
     * @return array
     */
    public  function updateCard(){
        $res=array();
        $result=DB::table("t_u_bank")->where("userid",$_POST['userId'])->update([
            "state"=>1,
            "updated_at"=>date("Y-m-d H:i:s",time()),
        ]);
        if($result){
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }
    /**银行卡处理
 * @return mixed
 */
    public  function  cardHandle(){
        $res=array();
        $userId=session("userId");
        $counts=DB::table("t_u_bank")->where("userid",$userId)->count();
        if($counts){
            $result=DB::table("t_u_bank")->where("userid",$userId)->update([
                "userid"=>$userId,
                "bankname"=>$_POST['bankName'],
                "account"=>$_POST['account'],
                "bankcard"=>$_POST['bankCard'],
                "bankfullname"=>$_POST['bankFullName'],
                "state"=>2,
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
        }else{
            $result=DB::table("t_u_bank")->insert([
                "userid"=>$userId,
                "bankname"=>$_POST['bankName'],
                "account"=>$_POST['account'],
                "bankcard"=>$_POST['bankCard'],
                "bankfullname"=>$_POST['bankFullName'],
                "state"=>2,
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
        }
        if($result){
           $res['code']="success";
        }else{
           $res['code']="error";
        }
        return  $res;

    }
    /**银行卡处理
     * @return mixed
     */
    public  function  card2(){
        return view("ucenter.card2");
    }
    /**银行卡处理
     * @return mixed
     */
    public  function  verifyCard(Request $request){
        $res=array();
        $data=$request->all();
        $money=DB::table("t_u_bank")->where("userId",$data['userId'])->pluck("money");
        if($money==$data['money']){
            DB::table('t_u_bank')->where('userId',$data['userId'])->update([
                "state"=>0,
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;

    }

    /**个人中心首页充值，消费记录
     * @return array
     */
    public  function getRecord(){
        $type=$_POST['type'];
        $role=$_POST['role'];
        $startPage=isset($_POST['startPage'])?$_POST['startPage']:1;
        $offset=($startPage-1)*10;
        $userId=session("userId");
        $result=array();
        if($role=="企业"){
            $counts=DB::table("T_U_BILL")->where("userid",$userId)->where("type",$type)->where("payflag",1)->count();
            $datas=DB::table("T_U_BILL")->select("brief","payno","money","created_at","type")->where("userid",$userId)->where("type",$type)->where("payflag",1)->skip($offset)->take(10)->get();
        }else{
            $counts=DB::table("T_U_BILL")->where("userid",$userId)->where("type",$type)->where("payflag",1)->count();
            $datas=DB::table("T_U_BILL")->select("brief","payno","money","created_at","type")->where("userid",$userId)->where("type",$type)->where("payflag",1)->skip($offset)->take(10)->get();
        }
        $counts=!empty(ceil($counts/10))?ceil($counts/10):0;
        foreach ($datas as $data){
            $data->created_at=date("Y-m-d",strtotime($data->created_at));
            if($data->type=="收入"){
                $data->money="+".$data->money;
            }else{
                $data->money="-".$data->money;
            }
        }
        if($datas){
            $result['code']="success";
            $result['counts']=$counts;
            $result['startPage']=$startPage;
            $result['msg']=$datas;
        }else{
            $result['code']="error";
        }
        return $result;

    }

    public  function haveCard(){
        $res=array();
        $userId=session('userId');
        $counts=DB::table('t_u_bank')->where('userid',$userId)->count();
        if($counts){
            $states=DB::table('t_u_bank')->where('userid',$userId)->pluck('state');
            if($states==1 ){
                $res['code']='0';
            }elseif($states==0){
                $res['code']='1';
            }else{
                $res['code']='2';
            }
        }else{
            $res['code']='0';
        }

        return $res;
    }

    /**我的项目评议
     * @return mixed
     */
    public function  myShow(Request $request){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        $datas = DB::table('t_s_show as show')
            ->leftJoin('view_userrole as view','view.userid', '=','show.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_user as user','show.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->leftJoin('view_showmesscount as mess','mess.showid' ,'=' ,'show.showid')
            ->leftJoin('view_showstatus as status','status.showid' ,'=' ,'show.showid')
            ->select('show.*','view.role','ent.enterprisename','ent.showimage as entimg','status.configid as flag','mess.count as messcount','ext.showimage as extimg','ext.expertname');

        //用户发布的数量
        $putcount = DB::table('view_showstatus as show')->where('userid',session('userId'))->where('show.configid',1)->count();
        //用户回复的数量
        $msgcount = count(DB::table('t_s_messagetoshow as show')->where('userid',session('userId'))->groupBy('showid')->lists('showid'));
        //用户待审核的供求的数量
        $waitcount= DB::table('view_showstatus as show')->where('userid',session('userId'))->whereIn('show.configid',[3,4])->count();
        //用户拒审核的供求的数量
        $refusecount = DB::table('view_showstatus as show')->where('userid',session('userId'))->where('show.configid',5)->count();
        /*//商情的数量
        $vipneedcount = DB::table('t_s_pushshow')->where('userid',session('userId'))->count();*/
        //判断是否为http请求
        if(!empty($get = $request->input())){
            //获取到get中的数据并处理
            $action = ( isset($get['action']) && $get['action'] != "null") ? $get['action'] : null;
            //设置where条件生成where数组


            $obj = $datas;

            if(!empty($action)){
                switch($action){

                    case 'myput':
                        $obj = $obj->where('status.configid',1);
                        $action  = '已发布';
                        break;

                    case 'waitverify':
                        $action  = '评议中';
                        $obj = $obj->whereIn('status.configid',[3,4]);
                        break;
                    case 'refuseverify':
                        $action  = '已完成';
                        $obj = $obj->where('status.configid',5);
                        break;
                }
            } else {

            }


            $datas = $obj->where('show.userid',session('userId'))->orderBy("show.showtime",'desc')->paginate(4);

            foreach($datas as $k => $v){
                if($v->flag == 1){
                    $v->flag2 = '已发布';
                } elseif($v->flag == 3 || $v->flag == 4){
                    $v->flag2 = '评议中';
                } elseif($v->flag == 5){
                    $v->flag2 = '已完成';
                }
            }
            return view("ucenter.myshow",compact('vipneedcount','waitcount','refusecount','cate','msgcount','datas','action','collectids','putcount','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->where('show.userid',session('userId'))
            ->orderBy("show.showtime",'desc')
            ->paginate(4);
        $ordertime = 'desc';
        foreach($datas as $k => $v){
            if($v->flag == 1){
                $v->flag2 = '已发布';
            } elseif($v->flag == 3 || $v->flag == 4){
                $v->flag2 = '评议中';
            } elseif($v->flag == 5){
                $v->flag2 = '已完成';
            }
        }
        return view("ucenter.myshow",compact('level','vipneedcount','waitcount','refusecount','cate','datas','ordertime','collectids','putcount','msgcount'));
    }


    //发布路演（项目评议）
    public function  supplyShow($needid = null){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        if($needid){
            //验证是否该供求的状态为拒绝 否则重定向到首页
            $res = DB::table('view_needstatus')->where(['needid' => $needid,'configid' => 2])->first();
            if($res){
                $info = DB::table('t_n_need')->where('needid',$needid)->first();
                $data = DB::table('t_n_needverify')->where('needid',$needid)->orderBy('id','desc')->first();
                $info->error = $data->remark;
                return view("ucenter.supplyNeed",compact('cate','info'));
            } else {
                return redirect('/');
            }
        }
        return view("ucenter.supplyshow",compact('cate'));
    }

    /**新增项目评议
     * @param Request $request
     */
    public function addShow (Request $request) {
        //判断是否为ajax请求
        if($request->ajax()){
            //判断是否登陆
            if(!empty(session('userId'))){
                $data = $request->input();
                $file = $request->file('file');
                $showpeoples= intval(trim($data['showpeoples']));
                $content = $data['content'];
                $title = $data['title'];
                $userId=session('userId');
                $enterprise=DB::table("t_u_enterprise")
                    ->leftJoin("t_u_enterpriseverify","t_u_enterprise.enterpriseid","=","t_u_enterpriseverify.enterpriseid")
                    ->where("t_u_enterprise.userid",$userId)
                    ->orderBy("t_u_enterpriseverify.id","desc")
                    ->first();
                if(empty($enterprise) || $enterprise->configid != 3){
                    return ['icon'=>4,'code' => 2,'msg' => '企业不存在或者未通过认证','url' => url('uct_member')];
                }
                $enterpriseid = DB::table('t_u_enterprise')
                    ->where('userid', $userId)
                    ->pluck('enterpriseid') ;
                $results = DB::table('t_u_enterprisemember')
                    ->where('enterpriseid', $enterpriseid)
                    ->first() ;
                if(!$results){
                    //不存在记录 202 开通会员操作
                    $res = DB::table('t_u_enterprisemember')->insert([
                        'memberid' => 1,
                        'enterpriseid' => $enterpriseid,
                        'starttime' => date('Y-m-d H:i:s',time()),
                        'endtime' => date('Y-m-d H:i:s',time()+60*60*24*365)
                    ]);
                    return ['icon' => 5,'code' => $showpeoples,'userid' => $userId];
                } else {
                    if($results->onlineshow < $showpeoples){
                        return ['icon' => 5,'code' => $showpeoples,'userid' => $userId];
                    }
                }


                if ($file->isValid()) {

                    // 获取文件相关信息
                    $originalName = $file->getClientOriginalName(); // 文件原名
                    $ext = $file->getClientOriginalExtension();     // 扩展名
                    $realPath = $file->getRealPath();   //临时文件的绝对路径
                    $type = $file->getClientMimeType();     // image/jpeg

                    // 上传文件
                    $filename = date('YmdHis'). uniqid() . '.' . $ext;
                    // 使用我们新建的uploads本地存储空间（目录）
                    $bool = Storage::disk('uploads')->put($filename, file_get_contents($realPath));

                } else {
                    return ['msg' => '上传失败~','icon' => 2];
                }
                $domain1 = trim(explode('/',$data['domain'])[0]);
                $domain2 = trim(explode('/',$data['domain'])[1]);

                    $verifyent = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterpriseid;
                    if($verifyent){
                        $verentconfig = DB::table('t_u_enterpriseverify')->where('enterpriseid',$verifyent)->orderBy('id','desc')->first()->configid;
                        if($verentconfig != 3){

                            return ['msg' => '您的企业未通过认证,暂不能发布','icon' => 2];
                        }
                    } else {
                        return ['msg' => '您还未成为企业~','icon' => 2];

                    }



                //开启事务
                DB::beginTransaction();
                try{
                    $showid = DB::table('t_s_show')->insertGetId([
                        'userid' => session('userId'),
                        'domain1' => $domain1,
                        'domain2' => $domain2,
                        'level' => 1,
                        'brief' => $content,
                        'bpurl' => $filename,
                        'title' => $title,
                        'bpname' => $originalName,
                        'showtime' => date('Y-m-d H:i:s',time()),
                        'basicdata' => $showpeoples,
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    DB::table('t_s_showverify')->insert([
                        'showid' => $showid,
                        'configid' => 1,
                        'verifytime' => date('Y-m-d H:i:s',time()),
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    DB::table('t_u_enterprisemember')->where('enterpriseid',$enterpriseid)->decrement('onlineshow',intval($showpeoples));
                    DB::commit();
                    $res = ['msg' => '该路演已通过,已为您提交到后台，请等待后台为您精准推送评议人.','icon' => 1];
                    return $res;

                }catch(Exception $e)
                {
                    DB::rollBack();
                    return ['msg' => '处理失败','icon' => 2];
                }
            }
            return ['msg' => '请登录','icon' => 2];
        }
        return ['msg' => '非法访问','icon' => 2];
    }

    /**项目评议详情
     * @return mixed
     */
    public function  showDetail($showId){
        //取出指定的供求信息
        $datas = DB::table('t_s_show as show')
            ->leftJoin('view_userrole as view','view.userid', '=','show.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_user as user','user.userid' ,'=' ,'show.userid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->select('ent.brief as desc1','ext.brief as desc2','show.*','ent.enterprisename','ent.address','ext.expertname','user.phone','ent.showimage as entimg','ext.showimage as extimg');
        //获取该供求的当前状态
        $configid = DB::table('t_s_showverify as need')->where('showid',$showId)->orderBy('id','desc')->select('configid')->first();
        $obj = clone $datas;
        $datas = $datas->where('showid',$showId)->first();



        //查询留言的信息
        $message = DB::table('t_s_messagetoshow as msg')
            ->leftJoin('t_u_expert as ext','ext.userid' ,'=' ,'msg.userid')
            ->where('showid',$showId)
            ->where('msg.isdelete',0)
            ->select('msg.*','ext.expertname','ext.showimage','ext.expertid')
            ->orderBy('messagetime','desc')
            ->get();
        $cryptid = Crypt::encrypt(session('userId').$showId);
        return view("ucenter.showDetail",compact('datas','message','configid','msgcount','cryptid'));
    }
    /**
     * 解决项目评议
     */
    public function solveShow (Request $request){
        //判断是否ajax请求
        if($request->ajax()){
            $data = $request->input();
            $showid = $data['showid'];
            $mdid = $data['mdid'];
            //判断你是否登陆 和 验证crypt解密对比
            if(!empty(session('userId')) && session('userId').$showid == Crypt::decrypt($mdid)){
                //确认这个需求是本人的
                $res = DB::table('t_s_show')->where('userid',session('userId'))->where('showid',$showid)->first();
                //防止连续点击触发多次插入 需要查询确定
                $res_repeat = DB::table('t_s_showverify')->where('configid',5)->where('showid',$showid)->first();
                if($res_repeat){
                    return ['msg' => '请勿重复提交','icon' => 2];
                }
                if($res){
                    $result = DB::table('t_s_showverify')->insert([
                        'showid' => $showid,
                        'configid' => 5,
                        'verifytime' => date('Y-m-d H:i:s',time()),
                        'updated_at' => date('Y-m-d H:i:s',time())
                    ]);
                    if($result){
                        return ['msg' => '处理成功','icon' => 1];
                    }
                }
                return ['msg' => '处理失败','icon' => 2];
            }
            return ['msg' => '非法访问','icon' => 2];
        }
        return ['msg' => '非法访问','icon' => 2];
    }
}
