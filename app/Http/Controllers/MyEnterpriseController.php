<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Mockery\CountValidator\Exception;

class MyEnterpriseController extends Controller
{
    /**专家资源库
     * @return mixed
     */
    public function  resource(Request $request){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        $datas = DB::table('t_u_expert as ext')
            ->leftJoin('t_u_user as user','ext.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expertfee as fee','ext.expertid' ,'=' ,'fee.expertid')
            ->leftJoin('view_expertcollectcount as coll','ext.expertid' ,'=' ,'coll.expertid')
            ->leftJoin('view_expertmesscount as mess','ext.expertid' ,'=' ,'mess.expertid')
            ->leftJoin('view_expertstatus as status','ext.expertid' ,'=' ,'status.expertid')
            ->select('ext.*','user.phone','fee.fee','fee.state','coll.count as collcount','mess.count as messcount')
            ->where('status.configid','2');
        //获得用户的收藏
        $collectids = [];
        if(!empty(session('userId'))){
            $collectids = DB::table('t_u_collectexpert')->where(['userid' => session('userId'),'remark' => 1])->lists('expertid');
        }
        //获取到专家给用户留言的数量
        $tomymsgcount = 0;
        if(!empty(session('userId'))){
            $enterinfo = DB::table('t_u_enterprise')->where('userid',session('userId'))->first();
            if(!empty($enterinfo)){
                $tomymsgcount = DB::table('t_u_messagetoenterprise')->whereRaw('(use_userid=0 or use_userid ='.session('userId').')')->where('userid','<>',session('userId'))->where(['enterpriseid' => $enterinfo->enterpriseid,'state' => 0,'isdelete' => 0])->count();
            }

        }
        //用户回复的数量
        $msgcount = count(DB::table('t_u_messagetoexpert')->where('userid',session('userId'))->groupBy('expertid')->lists('expertid'));
        $domainselect = ['找资金' => '投融资','找技术' => '科研技术', '定战略' => '战略管理', '找市场' => '市场资源'];
        $domainselect2 = ['投融资' => '找资金','科研技术' => '找技术', '战略管理' => '定战略', '市场资源' => '找市场'];
        //判断是否为http请求
        if(!empty($get = $request->input())){
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $consult=(isset($get['consult']) && $get['consult'] != "null") ? $get['consult'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;
            $action = ( isset($get['action']) && $get['action'] != "null") ? $get['action'] : null;
            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("category"=>$role):array();
            $addresswhere = !empty($address)?array("ext.address"=>$address):array();
            if(!empty($consult) && $consult == '收费'){
                $consultwhere = ['fee.state' => 1];
            } elseif(!empty($consult) && $consult == '免费'){
                $consultwhere = ['fee.state' => 0];
            } else {
                $consultwhere = [];
            }
            if(!empty($supply)){
                $supply[0] = $domainselect2[$supply[0]];
                $obj = $datas->where($rolewhere)->where('ext.domain1',$supply[0])->where('ext.domain2','like','%'.$supply[1].'%')->where($addresswhere)->where($consultwhere);
                $supply[0] = $domainselect[$supply[0]];
            } else {
                $obj = $datas->where($rolewhere)->where($addresswhere)->where($consultwhere);
            }
            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("ext.expertname","like","%".$searchname."%");
            }
            if(!empty($action)){
                switch($action){
                    case 'collect':
                        $obj = $obj->whereRaw('ext.expertid in (select  expertid from t_u_collectexpert  where userid='.session('userId').' and remark=1)');
                        $action = '已收藏';
                        //$obj = $obj->where('colneed.userid',session('userId'))->where('colneed.remark',1);
                        break;
                    case 'message':
                        $obj = $obj->whereRaw('ext.expertid in (select  expertid from t_u_messagetoexpert  where userid='.session('userId').' group by expertid)');
                        $action = '已留言';
                        break;
                }
            } else {
                $obj = $obj->whereIn('status.configid',[2,4]);
            }
            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('ext.expertid',$ordertime);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } elseif(!empty($ordermessage)) {
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->paginate(4);
            return view("myenterprise.newExResource",compact('cate','msgcount','domainselect','searchname','datas','role','tomymsgcount','collectids','consult','action','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->orderBy("ext.expertid",'desc')->paginate(4);
        $ordertime = 'desc';
        return view("myenterprise.newExResource",compact('cate','datas','domainselect','ordertime','collectids','msgcount','tomymsgcount'));
    }

    /**展示用户的留言
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function showMyReply(Request $request)
    {
        if(empty(session('userId'))){
            return redirect('/uct_resource');
        }
        $entinfo = DB::table('t_u_enterprise')->where('userid',session('userId'))->first();
        if(empty($entinfo)){
            return redirect('/uct_resource');
        }
        $datas = DB::table('t_u_messagetoenterprise')
                ->where('enterpriseid',$entinfo->enterpriseid)
                ->where('isdelete',0)
                ->whereRaw('(use_userid=0 or use_userid ='.session('userId').')')
                ->where('userid','<>',session('userId'))
                ->orderBy('id','desc')
                ->paginate(10);
        foreach($datas as $k => $v){


            $expert = DB::table('t_u_expert')->where('userid',$v->userid)->first();
            $v->expertname = $expert->expertname;
            $v->expertid = $expert->expertid;
            $v->expertimg = $expert->showimage;
        }
        if($request->ajax()){
            return $datas;
        }

        return view('myenterprise.myreply',compact('datas','entinfo'));
    }

    /**标记留言已读
     * @param Request $request
     * @return array
     */
    public function flagRead (Request $request) {
        if($request->ajax()){
            $data = $request->input('data');
            $state = $request->input('state');
            $userId = session('userId');
            $enterpriseid = DB::table('t_u_enterprise')->where('userid',$userId)->pluck('enterpriseid');
            if($state != 2){
                $where = [ 'state' => $state];
            } else {
                $where = ['isdelete' => 1];
            }
            $res = DB::table('t_u_messagetoenterprise')
                ->whereIn('id',$data)
                ->where('enterpriseid',$enterpriseid)
                ->update($where);
            if($state == 1){
                return ['msg' => '已标记留言为已读','icon' => 1];
            } elseif ($state == 2){
                return ['msg' => '删除留言成功','icon' => 1];
            }

        }
        return ['msg' => '非法请求','icon' => 2];
    }

    /** 给用户回复留言
     * @param Request $request
     */
    public function msgReply(Request $request)
    {
        if($request->ajax()){
            $data = $request->input();
            if(empty(session('userId'))){
                return ['msg' => '未登录','icon' => 2];
            }
            $userid = session('userId');
            $res = DB::table('t_u_messagetoenterprise')->insert([
                'userid' => $userid,
                'state' => 0,
                'enterpriseid' => $data['entid'],
                'content' => $data['content'],
                'messagetime' => date('Y-m-d H:i:s',time()),
                'isdelete' => 0,
                'parentid' => $data['id'],
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ]);
            $parid = DB::table('t_u_messagetoenterprise')->where('id',$data['id'])->first();
            $expertuserid = DB::table('t_u_enterprise')->where('enterpriseid',$data['entid'])->first();
            $msg = DB::table('t_m_systemmessage')->insert([
                'sendid' => 0,
                'receiveid' => $parid->userid,
                'sendtime' => date('Y-m-d H:i:s',time()),
                'title' => '企业'.$expertuserid->enterprisename.'回复了您的留言',
                'content' => '企业【'.$expertuserid->enterprisename.'】回复了您的留言：'.$data['content'].'  。[更多详细请到企业资源页查找企业查看]',
                'state' => 0
            ]);
            if($res){
                return ['msg' => '回复成功','icon' => 1];
            } else {
                return ['msg' => '回复失败','icon' => 2];
            }
        }
    }

    /**专家资源详情
     * @return mixed
     */
    public  function resDetail($expertid){
        //取出指定的供求信息
        $datas = DB::table('t_u_expert as ext')
            ->leftJoin('t_u_user as user','ext.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expertfee as fee','ext.expertid' ,'=' ,'fee.expertid')
            ->leftJoin('view_expertcollectcount as coll','ext.expertid' ,'=' ,'coll.expertid')
            ->leftJoin('view_expertmesscount as mess','ext.expertid' ,'=' ,'mess.expertid')
            ->select('ext.*','user.phone','fee.fee','fee.state');
        $obj = clone $datas;
        $datas = $datas->where('ext.expertid',$expertid)->first();
        //取出同类下推荐的供求
        $info = ['domain1' => $datas->domain1,'domain2' =>$datas->domain2,'expertid' => $datas->expertid];
        $recommendNeed = $obj->where('ext.expertid','<>',$info['expertid'])->orderBy('expertid','desc');
        $obj2 = clone $recommendNeed;
        //取出相同二级类下面的供求
        $recommendNeed = $recommendNeed->where(['ext.domain2' => $info['domain2'],'ext.domain1' => $info['domain1']])->take(5)->get();
        //不足5条时 在一级类下面查找供求
        if(count($recommendNeed) < 5){
            $commedomain1 = $obj2->where('ext.domain1',$info['domain1'])->where('ext.domain2','<>',$info['domain2'])->take(5-count($recommendNeed))->get();
            $recommendNeed = array_merge($recommendNeed,$commedomain1);
        }
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_u_collectexpert')->where(['userid' => session('userId'),'remark' => 1])->lists('expertid');
        }

        //查询留言的信息
        $message = DB::table('t_u_messagetoexpert as msg')
            ->leftJoin('view_userrole as view','view.userid', '=','msg.userid')
            ->leftJoin('t_u_enterprise as ent','ent.enterpriseid', '=','view.enterpriseid')
            ->leftJoin('t_u_expert as ext','ext.expertid' ,'=' ,'view.expertid')
            ->leftJoin('t_u_user as user','user.userid' ,'=' ,'msg.userid')
            ->leftJoin('t_u_user as user2','user2.userid' ,'=' ,'msg.use_userid')
            ->where('msg.expertid',$expertid)
            ->where('msg.isdelete',0)
            ->select('msg.*','ent.enterprisename','ext.expertname','user.avatar','user.nickname','user.phone','user2.nickname as nickname2','user2.phone as phone2')
            ->orderBy('messagetime','desc')
            ->get();
        //分组取出每个回复的数量
        $getmsgcount = DB::table('t_u_messagetoexpert')->where('expertid',$expertid)->groupBy('parentid')->select(DB::raw('parentid ,count(*) as count'))->having('parentid','<>',0)->get();
        $msgcount = [];
        foreach ($getmsgcount as $k => $v) {
            $msgcount[$v->parentid] = $v->count;
        }
        return view("myenterprise.resDetail",compact('datas','recommendNeed','message','collectids','msgcount'));
    }

    /**会员认证
     * @return mixed
     */
    public  function uct_member(){
        $enterprise = DB::table('t_u_enterprise')->where(['userid' => session('userId')])->first();
        $data = null;
        $configids = null;
        if(!empty($enterprise)){
            $enterpriseid = $enterprise->enterpriseid;
            $configids = DB::table('t_u_enterpriseverify')->where('enterpriseid',$enterpriseid)->orderBy('id','desc')->first();
            if($configids){
                if($configids->configid == 3){
                    return redirect('uct_member/member4/'.$enterpriseid);
                } elseif ($configids->configid == 1){
                    return redirect('uct_member/member2/'.$enterpriseid);
                } elseif ($configids->configid == 2){
                    $data = DB::table('t_u_enterprise')->where('enterpriseid',$enterpriseid)->first();
                }
            }
        }
        return view("myenterprise.member",compact('data','configids'));
    }

    /**ajax处理会员认证
     * @param Request $request
     * @return array
     */
    public function entVerify (Request $request) {
        if($request->ajax()){
            $data = $request->only(['entid','brief','enterprisename','licenceimage','showimage','size','industry','address']);
            $data['showimage']=(!isset($data['showimage']) || empty($data['showimage']) || $data['showimage']=='img/photo2.jpg')?'/images/enterprise.jpg':$data['showimage'];
            $data['userid'] = session('userId');
            $data['updated_at'] = date('Y-m-d H:i:s',time());
            $info = DB::table('t_u_enterprise')->where('userid',session('userId'))->first();
            if(!empty($info)){
                $verify = DB::table('t_u_enterpriseverify')->where('enterpriseid',$info->enterpriseid)->orderBy('id','desc')->first();
                if(empty($verify)){
                    $data['entid'] = $info->enterpriseid;
                } 
            }
            /*$verifyname = DB::table('t_u_enterprise')->where('enterprisename',$data['enterprisename'])->first();
            if($verifyname){
                if(empty($info) || $info->enterprisename != $data['enterprisename']){
                    return ['msg' => '该企业已认证','icon' => 2];
                }
            }*/
            if(!empty($data['entid'])){
                $res = $data['entid'];
                unset($data['entid']);
                DB::table('t_u_enterprise')->where('enterpriseid',$res)->update($data);
            } else {
                unset($data['entid']);
                $res = DB::table('t_u_enterprise')->insertGetId($data);
            }

            if($res){
                $res2 = DB::table('t_u_enterpriseverify')->insert([
                    'enterpriseid' => $res,
                    'configid' => 1,
                    'verifytime' => date('Y-m-d H:i:s',time()),
                    'updated_at' => date('Y-m-d H:i:s',time())

                ]);
                if($res2){
                    return ['msg' => '提交成功，进入审核阶段','icon' => 1,'id' => $res];
                }
                return ['msg' => '提交失败，请重新提交','icon' => 2];
            } else {
                return ['msg' => '提交失败，请重新提交','icon' => 2];
            }
        }
        return ['msg' => '非法请求' ,'icon' => 2];
    }

    /**会员认证2
 * @return mixed
 */
    public  function member2($entid){
        $data = DB::table('t_u_enterprise')->where(['enterpriseid' => $entid,'userid' => session('userId')])->first();
        if(!$data){
           return redirect('/');
        }
        $configid = DB::table('t_u_enterpriseverify')->where('enterpriseid',$entid)->orderBy('id','desc')->first();
        if(empty($configid->configid) || $configid->configid == 2){
            return redirect('uct_member');
        }elseif ($configid->configid == 3){
            return redirect('uct_member/member4/'.$entid);
        }
        return view("myenterprise.member2",compact('data'));
    }

    /**会员认证3
     * @return mixed
     */
    public  function member3($entid){
        $data = DB::table('t_u_enterprise')->where(['enterpriseid' => $entid,'userid' => session('userId')])->first();
        $member = DB::table('t_u_memberright')->get();
        if(!$data || empty(session('userId'))){
            return redirect('/');
        }
        $configid = DB::table('t_u_enterpriseverify')->where('enterpriseid',$entid)->orderBy('id','desc')->first();
        if(empty($configid->configid) || $configid->configid == 1){
            return redirect('uct_member');
        } elseif ($configid->configid ==3){
            return redirect('uct_member/member4/'.$entid);
        } elseif ($configid->configid == 2){
            return redirect('uct_member');
        }
        //进行加密验证
        $token = Crypt::encrypt($entid.'|'.session('userId').'|'.time());
        return view("myenterprise.member3",compact('member','token','entid'));
    }
    /**会员认证4
     * @return mixed
     */
    public  function member4($entid){
        $configid = DB::table('t_u_enterpriseverify')->where('enterpriseid',$entid)->orderBy('id','desc')->first();
        if(empty($configid->configid) || $configid->configid == 1){
            return redirect('uct_member');
        }elseif ($configid->configid == 2){
            return redirect('uct_member');
        }
        $data = DB::table('t_u_enterprise')->where(['enterpriseid' => $entid,'userid' => session('userId')])->first();
        return view("myenterprise.member4",compact('data'));
    }

    /**会员缴费
     * @param $entid
     */
    public function memberPay($entid,Request $request)
    {
        if(!empty($entid)){
            if($request->ajax()){
                if(!empty(session('userId'))){
                    $data = $request->only('token','type','time','cost');
                    $enterpriseinfo = DB::table('t_u_enterprise')->where('enterpriseid',$entid)->first();
                    if($enterpriseinfo->userid != session('userId')){
                        return ['msg' => '非本人操作','icon' => 2];
                    }
                    $token = explode('|',Crypt::decrypt($data['token']));
                    if($entid != $token[0] || session('userId') != $token[1]){
                        return ['msg' => '非法操作FF00002','icon' => 2];
                    }
                    if($token[2]+7200 < time() || $token[2] > time()){
                        return ['msg' => '该缴费已失效，请在两个小时内完成操作','icon' => 2];
                    }
                    $verify = DB::table('t_u_memberright')->where([
                        'termtime' => trim($data['time']),
                        'cost' => trim($data['cost']),
                        'typename' => trim($data['type'])
                    ])->first();
                    if(empty($verify)){
                        return ['msg' => '会员类型错误,请重试','icon' => 2];
                    }
                    DB::beginTransaction();
                    try{
                        DB::table('t_u_enterprisemember')->insert([
                            'memberid' => $verify->memberid,
                            'enterpriseid' => $entid,
                            'starttime' => date('Y-m-d H:i:s',time()),
                            'endtime' => date('Y',time()) + $data['time'] . '-' . date('m-d H:i:s')
                        ]);
                        DB::table('t_u_enterpriseverify')->insert([
                            'enterpriseid' => $entid,
                            'configid' => 4,
                            'verifytime' => date('Y-m-d H:i:s',time())
                        ]);

                        DB::commit();
                        return ['msg' => '缴费成功，企业认证成功','icon' => 1];
                    }catch(Exception $e){
                        DB::rollback();
                        throw $e;
                        return ['msg' => '缴费失败,请重试','icon' => 2];
                    }

                 }
                return ['msg' => '请登录','icon' => 2];
            }
            return ['msg' => '非法操作FF00001','icon' => 2];
        }
        return ['msg' => '非法操作FF00000','icon' => 2];
    }
    /**办事服务
     * @return mixed
     */
    public  function works(){
        $userId=session('userId');
        $type=isset($_GET['type'])?$_GET['type']:0;
        $typeWhere=($type!=0)?array("configid"=>$type):array();
        $result=DB::table("t_e_event")
                ->leftJoin("t_e_eventverify","t_e_eventverify.eventid","=","t_e_event.eventid")
                ->select("t_e_event.eventid",'t_e_eventverify.configid',"t_e_event.domain1","t_e_event.domain2","t_e_event.created_at","t_e_event.brief")
                ->whereRaw('t_e_eventverify.id in (select max(id) from t_e_eventverify group by eventid)')
                ->where("t_e_event.userid",$userId)
                ->where($typeWhere);
        $count=clone $result;
        $datas=$result->orderBy("t_e_event.created_at","desc")->paginate(2);
        $counts=$count->count();
        foreach ($datas as $data){
            $data->work=$data->domain1."/".$data->domain2;
            $data->created_at=date("Y-m-d",strtotime($data->created_at));
            $totals=DB::table("t_e_eventresponse")->where("eventid",$data->eventid)->count();
            if($totals!=0){
                $data->state="指定专家";
            }else{
                $data->state="匹配专家";
            }
            $configname = DB::table('t_e_eventverifyconfig')->where('configid',$data->configid)->first()->name;
            $data->configname = $configname;
        }
        switch($type){
            case 0:
                $type="全部";
                break;
            case 1:
                $type="办事审核";
                break;
            case 3:
                $type="审核失败";
                break;
            case 4:
                $type="邀请专家";
                break;
            case 5:
                $type="专家响应";
                break;
            case 6:
                $type="正在办事";
                break;
            case 7:
                $type="已完成";
            break;
            case 9:
                $type="异常终止";
            break;
        }
        return view("myenterprise.works",compact("datas","type","counts"));
    }

    public function verifyEnterprise()
    {
        $userid = session('userId');
        $verify = DB::table('t_u_enterprise')->where('userid',$userid)->first();
        if(!empty($verify)){
           $verifyconfigid = DB::table('t_u_enterpriseverify')->where('enterpriseid',$verify->enterpriseid)->orderBy('id','desc')->first();
            if(!empty($verifyconfigid->configid)){
                switch($verifyconfigid->configid){
                    case 1:
                        $return = 1;
                        break;
                    case 2:
                        $return = 2;
                        break;
                    case 3:
                        $return = 3;
                        break;
                    case 4:
                        $return = 4;
                        break;
                }
                return ['no' => $return,'url' => 'uct_member'];
            }
            return ['no' => 0,'url' => 'uct_member'];
        }
        return ['no' => 0,'url' => 'uct_member'];
    }

    /**办事详情
     * @param $eventId
     * @return mixed
     */
    public function workDetail($eventId,Request $request){
        //获取到这个办事的发起人
        $eventuserid = DB::table('t_e_event')->where('eventid',$eventId)->first();
        if(empty($eventuserid) || $eventuserid->userid != session('userId')){
            return redirect('/');
        }
        $datas=DB::table("t_e_event")
                    ->leftJoin("t_e_eventverify","t_e_eventverify.eventid","=","t_e_event.eventid")
                    ->where("t_e_event.eventid",$eventId)
                    ->whereRaw('t_e_eventverify.id in (select max(id) from t_e_eventverify group by eventid)')
                    ->get();
        $counts=DB::table("t_e_eventresponse")->where("eventid",$eventId)->where('state',1)->count();
        $counts2=DB::table("t_e_eventresponse")->where("eventid",$eventId)->where('state',0)->count();
        DB::table('t_e_event')->where('eventid',$eventId)->update(['entislook' => 1]);
        foreach ($datas as $data){
           $configId=$data->configid;
            if(!$counts){
                $data->state="指定专家";
            }else{
                $counts = $counts2;
                $data->state="系统分配";
            }
        }
        if($request->ajax()){

            $data = $request->input();
            $res = DB::table('t_e_eventprocessremark')->where('epid',$data['epid'])->orderBy('id','desc')->paginate(5);
            return $res;
        }
        $selExperts = null;
        switch($configId){
            case 4:
                $selExperts=DB::table("t_e_eventresponse")
                    ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                    ->whereIn("t_e_eventresponse.state",[0,1])
                    ->where("eventid",$eventId)
                    ->get();
                $selected=count($selExperts);
                break;
            case 5:
                $selExperts=DB::table("t_e_eventresponse")
                    ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_e_eventresponse.state",2)
                    ->where("eventid",$eventId)
                    ->get();
                $selected=count($selExperts);
            break;
            case 7:
                $selExperts=DB::table("t_e_eventresponse")
                    ->leftJoin("t_e_eventtcomment","t_e_eventresponse.eventid","=","t_e_eventtcomment.eventid" )
                    ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_e_eventresponse.state",3)
                    ->where("t_e_eventresponse.eventid",$eventId)
                    ->get();
                break;
            case 8:
                $selExperts=DB::table("t_e_eventresponse")
                    ->leftJoin("t_e_eventtcomment","t_e_eventresponse.eventid","=","t_e_eventtcomment.eventid" )
                    ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_e_eventresponse.state",3)
                    ->where("t_e_eventresponse.eventid",$eventId)
                    ->get();
                $configId = 7;
                break;
            case 9:
                $selExperts=DB::table("t_u_enterprise")
                    ->where('userid',$datas[0]->userid)
                    ->first();
                $selExperts2=DB::table("t_e_eventresponse")
                    ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_e_eventresponse.state",3)
                    ->where("eventid",$eventId)
                    ->first();
                $configId = 9;
                break;
            case 6:
                //当config为6正在办事的状态的时候
                //获取到被选择的专家的信息
                $info = DB::table('t_e_eventresponse as res')
                    ->leftJoin('t_u_expert as ext','ext.expertid','=','res.expertid')
                    ->leftJoin('t_u_expertfee as fee','fee.expertid','=','res.expertid')
                    ->where(['eventid' => $eventId,'res.state' => 3])
                    ->select('ext.expertname','ext.userid','fee.fee','ext.showimage')
                    ->first();
                //获取到该办事的相关信息
                $datas = DB::table("t_e_event")
                    ->leftJoin("view_eventstatus as status","status.eventid","=","t_e_event.eventid")
                    ->leftJoin('t_u_enterprise as ent','ent.userid','=','t_e_event.userid')
                    ->where("t_e_event.eventid",$eventId)
                    ->select('t_e_event.*','ent.*','status.*','t_e_event.brief')
                    ->first();
                //验证是否喂新手
                $verifyfirstevent = DB::table('t_e_event')
                    ->leftJoin('t_e_eventverify as ver','ver.eventid','=','t_e_event.eventid')
                    ->where('t_e_event.userid',session('userId'))
                    ->where('ver.configid','>','6')
                    ->first();
                $verifyfirstevent2 = DB::table('t_e_eventprocess as ver')
                    ->leftJoin('t_e_event','ver.eventid','=','t_e_event.eventid')
                    ->where('t_e_event.userid',session('userId'))
                    ->select('ver.epid')
                    ->first();
                $isfirstevent = 0;
                if(empty($verifyfirstevent) && empty($verifyfirstevent2)){
                    $isfirstevent = 1;
                }
                //获取到办事的流程的信息
                $configinfo = DB::table('t_e_eventprocessconfig as con')
                    ->where('con.domain',$datas->domain1)
                    ->orderBy('con.step')
                    ->get();
                foreach ($configinfo as $v){
                    $proinfo = DB::table('t_e_eventprocess')->where(['pid' => $v->pid,'eventid' => $eventId])->first();
                    $v->ppid = $v->pid;
                    $v->epid = !empty($proinfo->epid) ? $proinfo->epid : null;
                    $v->eventid = !empty($proinfo->eventid) ? $proinfo->eventid : null;
                    $v->startuserid = !empty($proinfo->startuserid) ? $proinfo->startuserid : null;
                    $v->acceptuserid = !empty($proinfo->acceptuserid) ? $proinfo->acceptuserid : null;
                    $v->documenturl = !empty($proinfo->documenturl) ? $proinfo->documenturl : null;
                    $v->state = !empty($proinfo->state) || (!empty($proinfo) && $proinfo->state === 0)  ? $proinfo->state : null;
                }
                //对信息进行封装
                $configinfo = \EnterpriseClass::processInsert($configinfo);
                //获取到该办事的所有的过程id
                $epids = DB::table('t_e_eventprocess')->where('eventid',$eventId)->lists('epid');
                //获取到办事进行到的过程
                $stepepid = !empty($_GET['step']) && in_array($_GET['step'],$epids) ? ['t_e_eventprocess.epid' => $_GET['step']] : [];
                $lastpid = DB::table('t_e_eventprocess')
                    ->leftJoin('t_e_eventprocessconfig as con','con.pid','=','t_e_eventprocess.pid')
                    ->where($stepepid)
                    ->where('eventid',$eventId)
                    ->orderBy('epid','desc')
                    ->first();
                //生成办事对应的状态
                $stmpstate = DB::table('t_e_eventprocess')->leftJoin('t_e_eventprocessconfig as con','con.pid','=','t_e_eventprocess.pid')->where('eventid',$eventId)->orderBy('epid','desc')->first();
                if(!empty($stmpstate) && $stmpstate->state == 2){
                    $stmpstate->step = $stmpstate->step+1;
                }
                if(empty($stmpstate)){
                    $stmpstate = (object)$stmpstate;
                    $stmpstate->step = 1;
                }
                //若不存在状态 为1
                if(empty($lastpid)){
                    $lastpid = (object)$lastpid;
                    $lastpid->step = 1;
                } elseif ($lastpid->state == 2 && empty($stepepid)){
                    $step = DB::table('t_e_eventprocessconfig')->where('pid',$lastpid->pid)->first()->step;
                    if(!empty($configinfo[$step])){
                        $x = $step+1;
                        $lastpid = (object)null;
                        $lastpid->step = $x;
                    }
                }
                //获取所有的日程
                $task = DB::table('t_e_eventtask')->where('eventid',$eventId)->where('state','<>','2')->orderBy('etid','desc')->get();
                $remark = [];
                foreach($epids as $v){
                    $data1 = DB::table('t_e_eventprocessremark')->where('epid',$v)->orderBy('id','desc')->paginate(5);
                    $data2 = DB::table('t_e_eventprocessremark')->where('epid',$v)->count();
                    if($data2){
                        //若有返回信息则吧反馈的信息对象存放到数组中
                        $remark[$v] = [$data1,$data2];
                    }
                }
                return view("myenterprise.new_uct_works5",compact('isfirstevent','task',"datas","eventId",'info','configinfo','lastpid','remark','stmpstate'));
        }
        $selExperts=!empty($selExperts)?$selExperts:"";
        $selected=!empty($selected)?$selected:"";
        $view="works".$configId;
        return view("myenterprise.".$view,compact("datas","counts","selected","selExperts",'selExperts2',"eventId"));
    }

    /**
     * 终止合作
     */
    public function stopEvent(Request $request)
    {
        if($request->ajax()){
            if(empty(session('userId'))){
                return ['msg' => '请登录','icon' => 2];
            }
            $data = $request->only('eventid','laststep','msg','action');
            $eventinfo = DB::table('t_e_event')
                ->leftJoin('t_u_enterprise as pre','pre.userid','=','t_e_event.userid')
                ->leftJoin('view_eventstatus as state','state.eventid','=','t_e_event.eventid')
                ->select('t_e_event.*','pre.enterprisename','state.configid')
                ->where('t_e_event.eventid',$data['eventid'])
                ->first();
            if(empty($eventinfo) || $eventinfo->configid != 6){
                return ['msg' => '办事异常或者办事已经完成','icon' => 2];
            }
            $eventexpert = DB::table('t_e_eventresponse')->where(['eventid' => $data['eventid'],'state' => 3])->first();
            $nowexpert = DB::table('t_u_expert')->where('userid',session('userId'))->first();
            if(empty($eventexpert)){
                return ['msg' => '找不到办事的专家','icon' => 2];
            }
            //若参与者是发起办事的人
            if($eventinfo->userid == session('userId')){
                if($data['action']){
                    DB::beginTransaction();
                    try{
                        $res = DB::table('t_e_eventverify')->insert([
                            'eventid' => $data['eventid'],
                            'configid' => 7,
                            'verifytime' => date('Y-m-d H:i:s',time()),
                            'remark' => ''
                        ]);
                        DB::table('t_e_eventresponse')->insert([
                            'eventid' => $data['eventid'],
                            'expertid' => $eventexpert->expertid,
                            'responsetime' => date('Y-m-d H:i:s',time()),
                            'state' => 4,
                            'updated_at' => date('Y-m-d H:i:s',time())
                        ]);
                        DB::commit();
                        $action = '办事已完成,请您继续给为您办事的专家做出等级及文字';
                    }catch(Exception $e){
                        DB::rollback();
                        return ['msg' => '操作失败','icon' => 2];
                    }

                } else {
                    $remark = '企业'.$eventinfo->enterprisename.'终止办事，原因：'.$data['msg'];
                    DB::beginTransaction();
                    try{
                        $res = DB::table('t_e_eventverify')->insert([
                            'eventid' => $data['eventid'],
                            'configid' => 9,
                            'verifytime' => date('Y-m-d H:i:s',time()),
                            'remark' => $remark
                        ]);
                        DB::table('t_e_eventresponse')->insert([
                            'eventid' => $data['eventid'],
                            'expertid' => $eventexpert->expertid,
                            'responsetime' => date('Y-m-d H:i:s',time()),
                            'state' => 5,
                            'updated_at' => date('Y-m-d H:i:s',time())
                        ]);

                        DB::commit();

                    }catch(Exception $e){
                        DB::rollback();
                        return ['msg' => '操作失败','icon' => 2];
                    }
                    $action = '办事中止...';
                }
            } elseif(!empty($nowexpert) && $eventexpert->expertid == $nowexpert->expertid){
                if($data['action']){
                    $action = '办事只能够企业完成，您不能完成此办事';
                } else {
                    $remark = '专家'.$nowexpert->expertname.'终止办事，原因：'.$data['msg'];
                    DB::beginTransaction();
                    try{
                        $res = DB::table('t_e_eventverify')->insert([
                            'eventid' => $data['eventid'],
                            'configid' => 9,
                            'verifytime' => date('Y-m-d H:i:s',time()),
                            'remark' => $remark
                        ]);
                        DB::table('t_e_eventresponse')->insert([
                            'eventid' => $data['eventid'],
                            'expertid' => $eventexpert->expertid,
                            'responsetime' => date('Y-m-d H:i:s',time()),
                            'state' => 5,
                            'updated_at' => date('Y-m-d H:i:s',time())
                        ]);

                        DB::commit();

                    }catch(Exception $e){
                        DB::rollback();
                        return ['msg' => '操作失败','icon' => 2];
                    }


                    $action = '办事中止...';
                }
            } else {
                return ['msg' => '非办事参与者','icon' => 2];
            }

            if(!empty($res)){
                return ['msg' => $action,'icon' => 1];
            }
            return ['msg' => '操作失败','icon' => 2];
        }
        return ['msg' => '操作失败','icon' => 2];
    }

    /**办事的文件的上传
     * @param $proid
     * @param Request $request
     * @return array
     */
    public function eventUpload($proid,Request $request)
    {
        if(empty(session('userId'))){
            return ['error' => '请登录','icon' => 2];
        }
        $data = $request->input();
        // 接收文件信息 进行上传
        $file = Input::file('files');
        if($file->isValid()){
            $clientName = $file -> getClientOriginalName();
            //$tmpName = $file ->getFileName();
            //$realPath = $file -> getRealPath();
            $entension = $file -> getClientOriginalExtension();
            //$mimeTye = $file -> getMimeType();
            $fileTypes = ['pdf','doc','txt','excel','docx','pptx','wps'];
            if(!in_array($entension,$fileTypes)){
                return ['error' => '您上传的不是正确的类型文件','icon' => 2];
            }
            //验证是否为指定身份提交
            $starttype = DB::table('t_e_eventprocessconfig')->where('pid',$proid)->first()->starttype;
            $eventuserid = DB::table('t_e_event')->where('eventid',$data['eventid'])->first()->userid;
            $eventexpertid = DB::table('t_e_eventresponse')->where(['eventid' => $data['eventid'],'state' => 3])->first()->expertid;

            if($starttype){
                $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
                if(empty($expertid) || $expertid->expertid != $eventexpertid){
                    return ['error' => '该资料应由专家上传','icon' => 2];
                }
            } else{
                if($eventuserid != session('userId')){
                    return ['error' => '该资料应由企业上传','icon' => 2];
                }
            }
            //将获取到的文件名装换成gb2312的编码方式
            $name = iconv("UTF-8","gb2312", $file->getClientOriginalName());
            $uploadpath = $data['eventid'].'/'.$proid.'/'.date('mdHis',time()).'/';
            $path1 = $file->move('../../swUpload/event/'.$uploadpath,$name);
            $path = '/event/'.$uploadpath.$name;
            if(!empty($path1)){
                //吧路径的编码方式转换成utf-8
                $path = iconv("gb2312","UTF-8", $path);
                //加密路径
                $down = Crypt::encrypt('../../swUpload'.$path);
                $data['pid'] = $proid;
                $data['documenturl'] = $path;
                $data['state'] = 0;
                $data['time'] = date('Y-m-d H:i:s',time());
                //获取到该办事的指定的过程的信息
                $verify = DB::table('t_e_eventprocess')->where(['pid' => $proid,'eventid' => $data['eventid']])->first();
                //如果存在过程信息  更改
                if(!empty($verify)){
                    $epid = $verify->epid;
                    DB::table('t_e_eventprocess')->where('epid',$epid)->update(['documenturl' => $path,'state' => 1]);
                } else {
                    $epid = DB::table('t_e_eventprocess')->insertGetId($data);
                }
                return ['msg' => '上传文件成功','path' => $path,'name' => $clientName,'icon' => 1,'downpath' => $down,'epid' => $epid];
            }
            return ['error' => '上传文件失败FF000002','icon' => 2];


        }
        return ['error' => '上传文件失败FF000001','icon' => 2];
    }

    /**确认资料
     * @param Request $request
     * @return array
     */
    public function trueDocument (Request $request) {
        if($request->ajax()){
            $data = $request->only('epid','pid','eventid');
            if(!$data['epid']){
                return ['error' => '系统错误，请刷新页面','icon' => 2];
            }
            //验证是否为指定身份提交
            $starttype = DB::table('t_e_eventprocessconfig')->where('pid',$data['pid'])->first()->starttype;
            $eventuserid = DB::table('t_e_event')->where('eventid',$data['eventid'])->first()->userid;

            if($starttype){
                if($eventuserid != session('userId')){
                    return ['error' => '该资料应由该办事企业确定','icon' => 2];
                }
            } else{
                $eventexpertid = DB::table('t_e_eventresponse')->where(['eventid' => $data['eventid'],'state' => 3])->first()->expertid;
                $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
                if(empty($expertid->expertid) ||$expertid->expertid != $eventexpertid){
                    return ['error' => '该资料应由该办事专家确定','icon' => 2];
                }
            }
            $eventdomain = DB::table('t_e_event')->where('eventid',$data['eventid'])->pluck('domain1');
            $res2 = DB::table('t_e_eventprocess')->where('epid',$data['epid'])->first();
            if(!empty($res2) && (empty(trim($res2->documenturl)) || $res2->state == 0)){
                return ['error' => '暂未上传资料~','icon' => 2];
            }

            $res = DB::table('t_e_eventprocess')->where('epid',$data['epid'])->update(['state' => 2]);
            switch ($eventdomain){
                case "找资金" :
                    if ($data['pid'] < 4){
                        DB::table('t_e_eventprocess')
                            ->insert([
                                'pid' => ($data['pid']+1),
                                'startuserid' => $res2->acceptuserid,
                                'acceptuserid' => $res2->startuserid,
                                'eventid' => $data['eventid'],
                                'time' => date("Y-m-d H:i:s"),
                                'state' => '0'
                            ]);
                    }
                    break;
                case "定战略" :
                    if ($data['pid'] < 15){
                        DB::table('t_e_eventprocess')
                            ->insert([
                                'pid' => ($data['pid']+1),
                                'startuserid' => $res2->acceptuserid,
                                'acceptuserid' => $res2->startuserid,
                                'eventid' => $data['eventid'],
                                'time' => date("Y-m-d H:i:s"),
                                'state' => '0'
                            ]);
                    }
                    break;
                case "找技术" :
                    if ($data['pid'] < 11){
                        DB::table('t_e_eventprocess')
                            ->insert([
                                'pid' => ($data['pid']+1),
                                'startuserid' => $res2->acceptuserid,
                                'acceptuserid' => $res2->startuserid,
                                'eventid' => $data['eventid'],
                                'time' => date("Y-m-d H:i:s"),
                                'state' => '0'
                            ]);
                    }
                    break;
                case "找市场" :
                    if ($data['pid'] < 19){
                        DB::table('t_e_eventprocess')
                            ->insert([
                                'pid' => ($data['pid']+1),
                                'startuserid' => $res2->acceptuserid,
                                'acceptuserid' => $res2->startuserid,
                                'eventid' => $data['eventid'],
                                'time' => date("Y-m-d H:i:s"),
                                'state' => '0'
                            ]);
                    }
                    break;
                default :
                    break ;
            }
            if($res){
                return ['msg' => '确认成功' ,'icon' => 1];
            }
            return ['error' => '您已经确认过资料了哦~','icon' => 2];
        }
        return ['error' => '非法请求','icon' => 2];
    }

    /**专家或者企业进行反馈
     * @param Request $request
     * @return array
     */
    public function sendRemark(Request $request)
    {
        if($request->ajax()){
            if(empty(session('userId'))){
                return ['error' => '请登陆后操作','icon' => 2];
            }
            $data = $request->input();
            $eventid = $data['eventid'];
            unset($data['eventid']);
            //获取到这个办事的发起人
            $eventuserid = DB::table('t_e_event')->where('eventid',$eventid)->first()->userid;
            //获取到这个办事被选择的专家的expertid
            $eventexpertid = DB::table('t_e_eventresponse')->where(['eventid' => $eventid,'state' => 3])->first()->expertid;
            //获取到当前登录用户的专家的id
            $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
            //若这个用户是专家  若不是 则只需判定办事的发起人是不是这个当前的登录者
            if(!empty($expertid->expertid)){
                //若被选择的专家id不是登录用户专家id 且 办事发起人不是当前用户
                if($eventexpertid != $expertid->expertid && $eventuserid != session('userId')){
                    return ['error' => '您不是办事企业或者受邀专家','icon' => 2];
                }
                //判定发起人是谁
                if($eventuserid == session('userId')){
                    $data['adduser'] = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterprisename;
                } elseif ($eventexpertid == $expertid->expertid){
                    $data['adduser'] = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertname;
                }
            } else {
                if($eventuserid != session('userId')){
                    return ['error' => '您不是办事企业','icon' => 2];
                } else {
                    $data['adduser'] = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterprisename;
                }
            }
            $res2 = DB::table('t_e_eventprocess')->where('epid',$data['epid'])->first();
            if(!empty($res2) && (empty(trim($res2->documenturl)) || $res2->state == 0)){
                return ['error' => '暂未上传资料~','icon' => 2];
            }
            $data['addtime'] = date('Y-m-d H:i:s',time());
            DB::table('t_e_eventprocessremark')->insert($data);
            return ['msg' => '反馈成功' , 'icon' => 1];

        }
        return ['error' => '非法请求','icon' => 2];
    }

    /**添加过程的最后一步
     * @param Request $request
     * @return int
     */
    public function addEventTask(Request $request)
    {
        $data = $request->input();
        $res = DB::table('t_e_eventprocess')->where(['pid' => $data['pid'],'eventid' => $data['eventid']])->first();
        if(empty($res)){
            $data['state'] = 0;
            $data['time'] = date('Y-m-d H:i:s',time());
            $epid = DB::table('t_e_eventprocess')->insertGetId($data);
            return $epid;
        }
        return 0;
    }

    /**提交日程
     * @param Request $request
     * @return array
     */
    public function submitTask(Request $request)
    {
        if($request->ajax()){
            $data = $request->input();
            //获取到这个办事的发起人
            $eventuserid = DB::table('t_e_event')->where('eventid',$data['eventid'])->first()->userid;
            //获取到这个办事被选择的专家的expertid
            $eventexpertid = DB::table('t_e_eventresponse')->where(['eventid' => $data['eventid'],'state' => 3])->first()->expertid;
            //获取到当前登录用户的专家的id
            $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
            if($eventuserid == session('userId') || (!empty($expertid->expertid) && $expertid->expertid == $eventexpertid)){
                if(!$data['state'] && $eventuserid == session('userId') ){
                    $etid = DB::table('t_e_eventtask')->insertGetId([
                        'eventid' => $data['eventid'],
                        'taskname' => $data['taskname'],
                        'createuserid' => session('userId'),
                        'addtime' => date('Y-m-d H:i:s',time()),
                        'state' => 0,
                    ]);
                    if($etid){
                        return ['msg' => '添加日程成功','icon' => 1,'etid' => $etid];
                    }

                } elseif ($data['state'] == 1){
                    if($eventuserid == session('userId')){
                        $name = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterprisename;
                        $operate = '企业'.$name.'完成此日程';
                    } else {
                        $name = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertname;
                        $operate = '专家'.$name.'完成此日程';
                    }
                    $res = DB::table('t_e_eventtask')->where('etid',$data['etid'])->update([
                        'state' => 1,
                        'finishtime' => date('Y-m-d H:i:s',time()),
                        'operate' => $operate
                    ]);
                    if($res){
                        return ['msg' => '完成日程成功','icon' => 1];
                    }
                    return ['error' => '处理失败FF00002','icon' => 2];
                } elseif ($data['state'] == 2){
                    if($eventuserid == session('userId')){
                        $name = DB::table('t_u_enterprise')->where('userid',session('userId'))->first()->enterprisename;
                        $operate = '企业'.$name.'删除此日程';
                    } else {
                        $name = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertname;
                        $operate = '专家'.$name.'删除此日程';                    }
                    $res = DB::table('t_e_eventtask')->where('etid',$data['etid'])->update([
                        'state' => 2,
                        'deletetime' => date('Y-m-d H:i:s',time()),
                        'operate' => $operate
                    ]);
                    if($res){
                        return ['msg' => '删除日程成功','icon' => 1];
                    }
                    return ['error' => '处理失败FF00001','icon' => 2];
                }                return ['error' => '添加日程失败您不是企业用户不能添加日程','icon' => 2];

            }
            return ['error' => '非本次办事参与人','icon' => 2];
        }
        return ['error' => '非法请求','icon' => 2];
    }
    
    /**申请办事服务
     * @return mixed
     */
    public function applyWork(){

        $cate = DB::table('t_common_domaintype')->get();
        $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();
        return view("myenterprise.work1",compact("cate",'memberrights'));
    }
    /**保存申请的办事
     * @return array
     * @throws Exception
     */
    public function saveEvent($data,$memberType,$enterpriseId){
        $userId=session("userId");
        $result=array();
        $domain=explode("/",$data['domain']);
        DB::beginTransaction();
        try{
            $eventId=DB::table("t_e_event")->insertGetId([
                "userid"=>$userId,
                "domain1"=>$domain[0],
                "domain2"=>$domain[1],
                "brief"=>$data['describe'],
                "isRandom"=>$data['isAppoint'],
                "eventtime"=>date("Y-m-d H:i:s",time()),
                "consulttime"=>env('EventVideoTime'),
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            DB::table("t_e_eventverify")->insert([
                "eventid"=>$eventId,
                "configid"=>1,
                'verifytime' => date("Y-m-d H:i:s",time()),
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);

            DB::commit();

            $verify = PublicController::ValidationAudit('event',['eventid' => $eventId]);
            if($verify['icon'] == 2){
                return $verify;
            } elseif ($verify['icon'] == 1){
                $verify2 = PublicController::eventPutExpert('event',['eventid' => $eventId,'state' => $data['state'],'expertIds' => $data['expertIds']],$memberType,$enterpriseId);
                return $verify2;
            } else {
                return ['msg' => '操作失败','icon' => 2];
            }


        }catch(Exception $e){
            DB::rollback();
            return ['msg' => '操作失败','icon' => 2];
        }
    }

    /**筛选专家
     * @param Request $request
     * @return mixed
     */
    public function  reselect(Request $request){
        //获取板块信息
        $cate = DB::table('t_common_domaintype')->get();
        $datas = DB::table('t_u_expert as ext')
            ->leftJoin('t_u_user as user','ext.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expertfee as fee','ext.expertid' ,'=' ,'fee.expertid')
            ->leftJoin('view_expertcollectcount as coll','ext.expertid' ,'=' ,'coll.expertid')
            ->leftJoin('view_expertmesscount as mess','ext.expertid' ,'=' ,'mess.expertid')
            ->leftJoin('view_expertstatus as status','ext.expertid' ,'=' ,'status.expertid')
            ->where('status.configid',2)
            ->where("ext.userid","<>",session('userId'))
            ->select('ext.*','user.phone','fee.fee','fee.state','coll.count as collcount','mess.count as messcount');
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_u_collectexpert')->where(['userid' => session('userId'),'remark' => 1])->lists('expertid');
        }
        $domainselect = ['找资金' => '投融资','找技术' => '科研技术', '定战略' => '战略管理', '找市场' => '市场资源'];
        $domainselect2 = ['投融资' => '找资金','科研技术' => '找技术', '战略管理' => '定战略', '市场资源' => '找市场'];
        //判断是否为http请求
        if(!empty($get = $request->input())){
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $consult=(isset($get['consult']) && $get['consult'] != "null") ? $get['consult'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("category"=>$role):array();
            $addresswhere = !empty($address)?array("ext.address"=>$address):array();
            if(!empty($consult) && $consult == '收费'){
                $consultwhere = ['fee.state' => 1];
                $datas = $datas->where('fee.fee','<>','null');
            } elseif(!empty($consult) && $consult == '免费'){
                $consultwhere = ['fee.state' => 0];
                $datas = $datas->whereRaw('fee.fee = 0 or fee.state = 0');
            } else {
              $consultwhere = [];
           }
            if(!empty($supply)){
                $supply[0] = $domainselect2[$supply[0]];
                $obj = $datas->where($rolewhere)->where('ext.domain1',$supply[0])->where('ext.domain2','like','%'.$supply[1].'%')->where($addresswhere)->where($consultwhere);
                $supply[0] = $domainselect[$supply[0]];
            } else {
                $obj = $datas->where($rolewhere)->where($addresswhere)->where($consultwhere);
            }            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("ext.expertname","like","%".$searchname."%");
            }
            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('ext.expertid',$ordertime);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } elseif(!empty($ordermessage)){
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->paginate(4);
            return view("myenterprise.reselect",compact('cate','searchname','datas','domainselect','role','collectids','consult','supply','address','ordertime','ordercollect','ordermessage'));
        }

          $datas = $datas->orderBy("ext.expertid",'desc')->paginate(4);
        $ordertime = 'desc';
        return view("myenterprise.reselect",compact('cate','datas','ordertime','domainselect','collectids'));
    }
    /**专家反选
     * @return array
     */
    public function  selectExpert(){
        $result=array();
        $expertIds=$_POST['expertIds'];
        try{
            $Ids=DB::table("t_e_eventresponse")
                ->select('expertid')
                ->where("eventid",$_POST['eventId'])
                ->whereRaw('t_e_eventresponse.id in (select max(id) from t_e_eventresponse group by eventid,expertid)')
                ->distinct()
                ->get();
            foreach($Ids as $v){
                if(in_array($v->expertid,$expertIds)){
                    DB::table("t_e_eventresponse")->insert([
                        'eventid' => $_POST['eventId'],
                        'expertid' =>$v->expertid,
                        'responsetime' => date("Y-m-d H:i:s"),
                        "state"=>3,
                        "updated_at"=>date("Y-m-d H:i:s")
                    ]);
                } else {
                    DB::table("t_e_eventresponse")->insert([
                        'eventid' => $_POST['eventId'],
                        'expertid' =>$v->expertid,
                        "state"=>5,
                        'remark'=>"您未被企业邀请",
                        'responsetime' => date("Y-m-d H:i:s"),
                        "updated_at"=>date("Y-m-d H:i:s")
                    ]);
                }
            }

            DB::table("t_e_eventverify")->insert([
                'eventid' => $_POST['eventId'],
                "configid"=>6,
                "verifytime"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time())
            ]);

            $domain1 = DB::table('t_e_event')
                ->where('eventid' , $_POST['eventId'])
                ->pluck('domain1') ;

            $pid = DB::table('t_e_eventprocessconfig')
                ->where('domain' , $domain1 )
                ->orderby('step' , 'asc')
                ->take(1)
                ->pluck('pid') ;

            DB::table('t_e_eventprocess')
                ->insert([
                    'pid' => $pid ,
                    'eventid' => $_POST['eventId'],
                    'state' => '0',
                    'time' => date("Y-m-d H:i:s"),
                ]);
        }catch (Exception $e){
            throw $e;
        }
        if(!isset($e)){
            foreach ($expertIds as $expertId) {
                $phone = DB::table('t_u_expert')
                    ->leftJoin('t_u_user', 't_u_expert.userid', '=', 't_u_user.userid')
                    ->where('expertid', $expertId)
                    ->pluck('phone');
                $name = DB::table('t_e_event')
                    ->leftJoin('t_u_enterprise', 't_e_event.userid', '=', 't_u_enterprise.userid')
                    ->where('eventid', $_POST['eventId'])
                    ->pluck('enterprisename');
                $this->_sendSms($phone, '办事选择', 'reselects', $name);
            }
            \UserClass::createEventGroups($expertIds,$_POST['eventId']);
            $result['code']="success";
        }else{
            $result['code']="error";
        }
        return $result;

    }

    /**给专家星级评论
     * @return array
     */
    public  function toExpertMsg(){
        $result=array();
        $eventId=$_POST['eventId'];
        try{
            $counts=DB::table("t_e_eventtcomment")->where([ "eventid"=>$eventId,"expertid"=>$_POST['expertId']])->count();
            if($counts){
                DB::table("t_e_eventtcomment")->where([ "eventid"=>$eventId,"expertid"=>$_POST['expertId']])->update([
                    "score"=>$_POST['score'],
                    "updated_at"=>date("Y-m-d H:i:s",time()),
                ]);
            }else {
                DB::table("t_e_eventtcomment")->insert([
                    "eventid" => $eventId,
                    "expertid" => $_POST['expertId'],
                    "score" => $_POST['score'],
                    "comment" => "",
                    "commenttime" => date("Y-m-d H:i:s", time()),
                    "created_at" => date("Y-m-d H:i:s", time()),
                    "updated_at" => date("Y-m-d H:i:s", time()),
                ]);
            }
        }catch(Exception $e){
            throw $e;
        }
        if(!isset($e)){
            $result['code']="success";
        }else{
            $result['code']="error";
        }
        return $result;
    }

    /**给专家评论
     * @return array
     */
    public function toExpertContent(){
        $result=array();
        $eventId=$_POST['eventId'];
        try{
            DB::table("t_e_eventtcomment")->where(["eventid"=>$eventId,"expertid"=>$_POST['expertId']])->update([
                "comment"=>$_POST['content'],
                "commenttime"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);

            DB::table('t_e_eventverify')->insert([
                'eventid' => $eventId,
                'configid' => 8,
                'verifytime' => date("Y-m-d H:i:s", time())
            ]);
        }catch(Exception $e){
            throw $e;
        }
        if(!isset($e)){
            $result['code']="success";
        }else{
            $result['code']="error";
        }
        return $result;
    }

    /**视频咨询
     * @return mixed
     */
    public function video(){
        $userId=session('userId');
        $type=isset($_GET['type'])?$_GET['type']:"全部";
        switch ($type){
            case '找资金':
                $type2 = '投融资';
                break;
            case '找技术':
                $type2 = '产品升级换代';
                break;
            case '定战略':
                $type2 = '战略定位';
                break;
            case '找市场':
                $type2 = '市场拓展';
                break;
            default :
                $type2 = 0;
                break;
        }
        $typeWhere=($type2)?array("t_c_consult.domain1"=>$type2):array();
        $result=DB::table("t_c_consult")
            ->leftJoin("t_c_consultverify","t_c_consultverify.consultid","=","t_c_consult.consultid")
            ->select("t_c_consult.consultid","t_c_consult.domain1","t_c_consult.domain2","t_c_consult.starttime","t_c_consult.endtime","t_c_consult.brief","t_c_consultverify.configid")
            ->whereRaw('t_c_consultverify.id in (select max(id) from t_c_consultverify group by consultid)')
            ->where("t_c_consult.userid",$userId)
            ->where($typeWhere);
        $count=clone $result;
        $datas=$result->orderBy("t_c_consult.created_at","desc")->paginate(2);
        $counts=$count->count();
        foreach ($datas as $data){
            $data->starttime=date("m-d H:i",strtotime($data->starttime));
            $data->endtime=date("m-d H:i",strtotime($data->endtime));
            switch($data->domain1){
                case '投融资':
                    $data->icon = 'v-manage-link-icon';
                    break;
                case '产品升级换代':
                    $data->icon = 'v-manage-link-icon nature1';
                    break;
                case '战略定位':
                    $data->icon = 'v-manage-link-icon nature2';
                    break;
                case '市场拓展':
                    $data->icon = 'v-manage-link-icon nature3';
                    break;
                default :
                    $data->icon = 'v-manage-link-icon';
                    break;
            }
        }
        return view("myenterprise.newVideoManage",compact("datas","type","counts"));
    }
    /**视频咨询详情
     * @return mixed
     */
    public function videoDetail($consultId){
        $userId=session("userId");
        $datas=DB::table("t_c_consult")
            ->leftJoin("t_c_consultverify","t_c_consultverify.consultid","=","t_c_consult.consultid")
            ->where("t_c_consult.consultid",$consultId)
            ->whereRaw('t_c_consultverify.id in (select max(id) from t_c_consultverify group by consultid)')
            ->get();
        $counts=DB::table("t_c_consultresponse")->where("consultid",$consultId)->where('state',1)->count();
        $counts2=DB::table("t_c_consultresponse")->where("consultid",$consultId)->where('state',0)->count();
        foreach ($datas as $data){
            $configId=$data->configid;
            if(!$counts){
                $counts = $counts2;
                $data->state="指定专家";
            }else{
                $data->state="系统分配";
            }
            $data->starttime=date("Y年m月d日 H:i:s",strtotime($data->starttime));
            $data->endtime=date("Y年m月d日 H:i:s",strtotime($data->endtime));
        }
        DB::table('t_c_consult')->where('consultid',$consultId)->update(['entislook' => 1]);
        switch($configId){
            case 4:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->whereIn("t_c_consultresponse.state",[0,1])
                    ->where("consultid",$consultId)
                    ->get();
                $selected=count($selExperts);
                break;
            case 5:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->leftJoin("t_u_expertfee","t_u_expertfee.expertid","=","t_u_expert.expertid")
                    ->select("t_u_expert.*","t_u_expertfee.fee","t_c_consultresponse.consultid","t_u_expertfee.state")
                    ->where("t_c_consultresponse.state",2)
                    ->where("consultid",$consultId)
                    ->get();
                $expertcost = 0;
                foreach($selExperts as $v){
                    $expertcost += $v->fee;
                }
                $selected=count($selExperts);
            break;
            case 6:
                $selExperts=DB::table("t_c_consult")
                    ->leftJoin("t_c_consultresponse","t_c_consultresponse.consultid","=","t_c_consult.consultid")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                $ExpertMoneys=DB::table("t_u_bill")
                    ->where("t_u_bill.consultid",$consultId)
                    ->where("type","收入")
                    ->sum('money');
                if($ExpertMoneys){
                    $selectExpertMoney=$ExpertMoneys;
                }else{
                    $selectExpertMoney="免费";
                }
                $comperes=DB::table("t_u_user")->where("userid",session('userId'))->get();
                break;
            case 7:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_c_consultcomment","t_c_consultresponse.expertid","=","t_c_consultcomment.expertid" )
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                break;
            case 8:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_c_consultcomment","t_c_consultresponse.expertid","=","t_c_consultcomment.expertid" )
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                $configId = 7;
                break;
            case 9:

                $selExperts=DB::table("t_u_enterprise")
                    ->where('userid',$datas[0]->userid)
                    ->first();
                $selExperts2=DB::table("t_c_consultresponse")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("consultid",$consultId)
                    ->get();
                break;
        }
        $selExperts=!empty($selExperts)?$selExperts:"";
        $selected=!empty($selected)?$selected:"";
        $comperes=!empty($comperes)?$comperes:"";
        $view="video".$configId;
        return view("myenterprise.".$view,compact("datas","counts","selected","selExperts","consultId","userId","selectExpertMoney",'expertcost','selExperts2',"comperes"));
    }
    /**申请视频咨询
     * @return mixed
     */
    public function applyVideo(){
        $cate = DB::table('t_common_domaintype')->get();
        $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();
        return view("myenterprise.applyVideo",compact("cate",'memberrights'));
    }

    /**保存申请的咨询
     * @return array
     */
    public  function saveVideo($data,$enterpriseId){
        $userId=session("userId");
        $result=array();
        $domain=explode("/",$data['domain']);
        if(strtotime($data['dateStart']) < time() ){
            return  ['msg' => '视频咨询开始时间不能在今天以前','icon' => 2];
        }
       /* if(strtotime($data['dateStart']) > strtotime($data['dateEnd'])){
            return   ['msg' => '视频咨询开始时间结束时间错误','icon' => 2];
        }*/
        DB::beginTransaction();
        try{
            $consultId=DB::table("t_c_consult")->insertGetId([
                "userid"=>$userId,
                "domain1"=>$domain[0],
                "domain2"=>$domain[1],
                "videotype"=>$data['videoType'],
                "brief"=>$data['describe'],
                "isRandom"=>$data['isAppoint'],
                "starttime"=>$data['dateStart'],
                "endtime"=>$data['dateEnd'],
                "consulttime"=>date("Y-m-d H:i:s",time()),
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            DB::table("t_c_consultverify")->insert([
                "consultid"=>$consultId,
                "configid"=>1,
                "created_at"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            DB::commit();

            $verify = PublicController::ValidationAudit('video',['consultid' => $consultId]);
            if($verify['icon'] == 2){
                return $verify;
            } elseif ($verify['icon'] == 1){
                $verify2 = PublicController::eventPutExpert('video',['consultid' => $consultId,'state' => $data['state'],'expertIds' => $data['expertIds'],'dateEnd'=>$data['dateEnd'],'dateStart'=>$data['dateStart']],'',$enterpriseId);
                return $verify2;
            } else {
                return ['msg' => '操作失败','icon' => 2];
            }
        }catch(Exception $e){
            DB::rollback();
            return ['msg' => '操作失败','icon' => 2];
        }
    }

    /**申请咨询 指定专家
     * @param Request $request
     * @return mixed
     */
    public  function videoSelect(Request $request){
        $cate = DB::table('t_common_domaintype')->get();
        $times=strtotime($_GET['start'])+$_GET['end']*60;
        $endtime=date("Y-m-d H:i",$times);
        $_GET['end']=$endtime;
        $workIngExperts=array();
        $workExperts=DB::table('view_expertresponsetime')
                        ->select('expertid')
                        ->whereRaw('(starttime between  "'.$_GET['start'].'" and "'.$_GET['end'].'" or endtime between "'.$_GET['start'] .'" and "'.$_GET['end'] .'" or starttime < "'.$_GET['start'].'" and endtime > "'.$_GET['end'].'") and state in (2,3)')
                        ->distinct()
                        ->get();
        foreach ($workExperts as $workExpert){
            if(!in_array($workExpert->expertid,$workIngExperts)){
                $workIngExperts[]=$workExpert->expertid;
            }
        }
        $datas = DB::table('t_u_expert as ext')
            ->leftJoin('t_u_user as user','ext.userid' ,'=' ,'user.userid')
            ->leftJoin('t_u_expertfee as fee','ext.expertid' ,'=' ,'fee.expertid')
            ->leftJoin('view_expertcollectcount as coll','ext.expertid' ,'=' ,'coll.expertid')
            ->leftJoin('view_expertmesscount as mess','ext.expertid' ,'=' ,'mess.expertid')
            ->leftJoin('view_expertstatus as status','ext.expertid' ,'=' ,'status.expertid')
            ->where('status.configid',2)
            ->where("ext.userid","<>",session('userId'))
            ->whereNotIn("ext.expertid",$workIngExperts)
            ->select('ext.*','user.phone','fee.fee','fee.state','coll.count as collcount','mess.count as messcount');
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_u_collectexpert')->where(['userid' => session('userId'),'remark' => 1])->lists('expertid');
        }
        $domainselect = ['找资金' => '投融资','找技术' => '科研技术', '定战略' => '战略管理', '找市场' => '市场资源'];
        $domainselect2 = ['投融资' => '找资金','科研技术' => '找技术', '战略管理' => '定战略', '市场资源' => '找市场'];
        //判断是否为http请求
        $get=$request->input();
        if(isset($get['ordertime'])){
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $consult=(isset($get['consult']) && $get['consult'] != "null") ? $get['consult'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;
            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("category"=>$role):array();
            $addresswhere = !empty($address)?array("ext.address"=>$address):array();
            if(!empty($consult) && $consult == '收费'){
                $consultwhere = ['fee.state' => 1];
               // $datas = $datas->where('fee.fee','<>','null');
            } elseif(!empty($consult) && $consult == '免费'){
                $consultwhere = ['fee.state' => 0];
              //  $datas = $datas->whereRaw('fee.fee = 0 or fee.state = 0');
            } else {
                $consultwhere = [];
            }
            if(!empty($supply)){
                $supply[0] = $domainselect2[$supply[0]];
                $obj = $datas
                    ->where($rolewhere)
                    ->where('ext.domain1',$supply[0])
                    ->where('ext.domain2','like','%'.$supply[1].'%')
                    ->where($addresswhere)
                    ->where($consultwhere);
                $supply[0] = $domainselect[$supply[0]];
            } else {
                $obj = $datas->where($rolewhere)->where($addresswhere)->where($consultwhere);
            }
            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("ext.expertname","like","%".$searchname."%");
            }
            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('ext.expertid',$ordertime);
                //$obj = $obj->orderBy('coll.count',null);
                //$obj = $obj->orderBy('mess.count',null);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } elseif(!empty($ordermessage)) {
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->paginate(4);
            return view("myenterprise.videoSelect",compact('cate','searchname','domainselect','datas','role','collectids','consult','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->orderBy("ext.expertid",'desc')->paginate(4);
        $ordertime = 'desc';
        return view("myenterprise.videoSelect",compact('cate','datas','ordertime','domainselect','collectids'));
    }
    /**申请咨询 处理反选的专家
     * @param Request $request
     * @return mixed
     */
    public  function handleSelect(){
        $result=array();
        $expertIDS=array();
        $expertMoney=0;
        $expertIds=$_POST['expertIds'];
        $userId=$_POST['userId'];
        $consultId=$_POST['consultId'];
        $consults=DB::table("t_c_consult")
            ->leftJoin("t_c_consultverify","t_c_consult.consultid","=","t_c_consultverify.consultid")
            ->select("t_c_consult.starttime","t_c_consult.endtime","t_c_consultverify.payflag")
            ->where(["t_c_consult.consultid"=>$consultId,"configid"=>5])
            ->orderBy("t_c_consultverify.id","desc")
            ->take(1)
            ->first();
        $startTimes=strtotime($consults->starttime);
        $endTimes=strtotime($consults->endtime);
        $timeLong=($endTimes-$startTimes)/60;
        foreach ($expertIds as $value){
            $values=explode("/",$value);
            if(!in_array($values,$expertIDS)){
                $expertIDS[]=$values[0];
            }
            $expertMoney=$expertMoney+$values[1]*$timeLong;
        }
        if($consults->payflag==0 && $expertMoney ){
            $result['code']="noMoney";
            $result['money']=$expertMoney;
            return $result;
        }else{
            DB::beginTransaction();
            try{
                $Ids=DB::table("T_C_CONSULTRESPONSE")
                    ->select('expertid')
                    ->where("consultid",$_POST['consultId'])
                    ->distinct()
                    ->get();
                foreach ($Ids as $ID){
                    if(in_array($ID->expertid,$expertIDS)){
                        DB::table("T_C_CONSULTRESPONSE")->insert([
                            "consultid"=>$_POST['consultId'],
                            "state"=>3,
                            "expertid"=>$ID->expertid,
                            "responsetime"=>date("Y-m-d H:i:s",time()),
                            "created_at"=>date("Y-m-d H:i:s",time()),
                            "updated_at"=>date("Y-m-d H:i:s")
                        ]);
                        $phone=DB::table('t_u_expert')
                            ->leftJoin('t_u_user','t_u_expert.userid','=','t_u_user.userid')
                            ->where('expertid',$ID->expertid)
                            ->pluck('phone');
                        $name=DB::table('t_c_consult')
                            ->leftJoin('t_u_enterprise','t_c_consult.userid','=','t_u_enterprise.userid')
                            ->where('consultid',$_POST['consultId'])
                            ->pluck('enterprisename');
                        $this->_sendSms($phone,'视频咨询','reselects',$name);

                    }else{
                        DB::table("T_C_CONSULTRESPONSE")->insert([
                            "consultid"=>$_POST['consultId'],
                            "state"=>5,
                            "expertid"=>$ID->expertid,
                            'remark'=>"您未被企业邀请",
                            "responsetime"=>date("Y-m-d H:i:s",time()),
                            "created_at"=>date("Y-m-d H:i:s",time()),
                            "updated_at"=>date("Y-m-d H:i:s")
                        ]);
                    }
                }
                DB::table("t_c_consultverify")->insert([
                    "consultid"=>$_POST['consultId'],
                    "configid"=>6,
                    "verifytime"=>date("Y-m-d H:i:s",time()),
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date("Y-m-d H:i:s",time())
                ]);
                DB::commit();
            }catch (Exception $e){
                DB::rollback();
                throw $e;
            }
            if(!isset($e)){
                \UserClass::createGroups($expertIDS,$_POST['consultId']);
                $result['code']="success";
            }else{
                $result['code']="error";
            }
        }
        return $result;
    }
    /*
     * 视频完成
     */
    public  function finishConsult(){
        $consutId=$_POST['consultId'];
        $type=$_POST['type'];
        $content = empty($_POST['msg']) ? '' : '异常终止:'.$_POST['msg'];
        if($type=='end'){
            $configId=7;
            $state=4;
        }else{
            $configId=9;
            $state=5;
        }
        $res=array();
        DB::beginTransaction();
        try{
            DB::table('t_c_consultverify')->insert([
                'consultid'=>$consutId,
                'configid'=>$configId,
                'verifytime'=>date('Y-m-d H:i:s',time()),
                'remark'=>$content,
                'created_at'=>date('Y-m-d H:i:s',time()),
                'updated_at'=>date('Y-m-d H:i:s',time()),
            ]);
            $expertIds=DB::table('t_c_consultresponse')
                ->where(['consultid'=>$consutId,'state'=>3])
                ->select('expertid')
                ->distinct()
                ->get();
            foreach($expertIds as $value){
                DB::table('t_c_consultresponse')->insert([
                    'consultid'=>$consutId,
                    'expertid'=>$value->expertid,
                    'responsetime'=>date('Y-m-d H:i:s'),
                    'state'=>$state,
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time()),

                ]);
            }
            DB::commit();
        }catch (Exception $e){
            DB::rollback();
            throw $e;
        }
        if(!isset($e)){
            $res['code']='success';
        }else{
            $res['code']='error';
        }
        return $res;
    }
    /**视频星级评分
     * @return array
     */
    public function  toVideoExpertMsg(){
        $result=array();
        $consultId=$_POST['consultId'];
        try{
            $counts=DB::table("t_c_consultcomment")->where([ "consultid"=>$consultId,"expertid"=>$_POST['expertId']])->count();
            if($counts){
                DB::table("t_c_consultcomment")->where([ "consultid"=>$consultId,"expertid"=>$_POST['expertId']])->update([
                    "score"=>$_POST['score'],
                    "updated_at"=>date("Y-m-d H:i:s",time()),                ]);
            }else{
                DB::table("t_c_consultcomment")->insert([
                    "consultid"=>$consultId,
                    "expertid"=>$_POST['expertId'],
                    "score"=>$_POST['score'],
                    "comment"=>"",
                    "commenttime"=>date("Y-m-d H:i:s",time()),
                    "created_at"=>date("Y-m-d H:i:s",time()),
                    "updated_at"=>date("Y-m-d H:i:s",time()),
                ]);
            }
        }catch(Exception $e){
            throw $e;
        }
        if(!isset($e)){
            $result['code']="success";
        }else{
            $result['code']="error";
        }
        return $result;
    }

    /**视频内容评论
     * @return array
     */
    public function   toVideoExpertContent(){
        $result=array();
        $consultId=$_POST['consultId'];
        try{
            DB::table("t_c_consultcomment")->where(["consultid"=>$consultId,"expertid"=>$_POST['expertId']])->update([
                "comment"=>$_POST['content'],
                "commenttime"=>date("Y-m-d H:i:s",time()),
                "updated_at"=>date("Y-m-d H:i:s",time()),
            ]);
            DB::table('t_c_consultverify')->insert([
                'consultid' => $consultId,
                'configid' => 8,
                'verifytime' => date('Y-m-d H:i:s')
            ]);
        }catch(Exception $e){
            throw $e;
        }
        if(!isset($e)){
            $result['code']="success";
        }else{
            $result['code']="error";
        }
        return $result;
    }

    /**新办事服务
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manage(){
        $userId=session('userId');
        $type=isset($_GET['domain'])?$_GET['domain']:"不限";
        $configTypeArray=array("已推送"=>4,"已响应"=>5,"正在办事"=>6,"已完成"=>7,"已评价"=>8,"异常终止"=>9);
        $configType=isset($_GET['configType'])?$_GET['configType']:"不限";
        $typeWhere = ($type=='不限')?[]:["t_e_event.domain1"=>$type];
        $configTypeWhere = ($configType=='不限')?[]:["t_e_eventverify.configid"=>$configTypeArray[$configType]];

        $result=DB::table("t_e_event")
            ->leftJoin("t_e_eventverify","t_e_eventverify.eventid","=","t_e_event.eventid")
            ->select("t_e_event.eventid",'t_e_eventverify.configid',"t_e_event.domain1","t_e_event.domain2","t_e_event.created_at","t_e_event.brief")
            ->whereRaw('t_e_eventverify.id in (select max(id) from t_e_eventverify group by eventid)')
            ->where("t_e_event.userid",$userId)
            ->whereNotIn('t_e_eventverify.configid',[1,2,3])
            ->where( $configTypeWhere)
            ->where($typeWhere);
        $count=clone $result;
        $datas=$result->orderBy("t_e_event.created_at","desc")->paginate(6);
        $counts=$count->count();
        foreach ($datas as $data){
            $data->created_at=date("Y年m月d日",strtotime($data->created_at));
            $totals=DB::table("t_e_eventresponse")->where("eventid",$data->eventid)->count();
            if($totals!=0){
                $data->state="指定专家";
            }else{
                $data->state="匹配专家";
            }
            switch($data->domain1){
                case '找资金':
                    $data->icon = 'v-manage-link-icon';
                    break;
                case '找技术':
                    $data->icon = 'v-manage-link-icon nature1';
                    break;
                case '定战略':
                    $data->icon = 'v-manage-link-icon nature2';
                    break;
                case '找市场':
                    $data->icon = 'v-manage-link-icon nature3';
                    break;
                default :
                    $data->icon = 'v-manage-link-icon';
                    break;
            }
            $configname = DB::table('t_e_eventverifyconfig')->where('configid',$data->configid)->first()->name;
            $data->configname = $configname;
            switch($data->configid){
                case 1:
                    $data->btnicon = 'eventwait';
                    break;
                case 2:
                    $data->btnicon = 'eventfollow';
                    break;
                case 3:
                    $data->btnicon = 'eventdont';
                    break;
                case 4:
                    $data->btnicon = 'eventput';
                    break;
                case 5:
                    $data->btnicon = 'response';
                    break;
                case 6:
                    $data->btnicon = 'eventing';
                    break;
                case 7:
                    $data->btnicon = 'eventend';
                    break;
                case 8:
                    $data->btnicon = 'eventend';
                    break;
                case 9:
                    $data->btnicon = 'eventdont';
                    break;
            }
        }
        $domains=DB::table("T_COMMON_DOMAINTYPE")->select('domainname')->where("level",1)->get();
        return view("myenterprise.newWorkManage",compact("datas","type","counts","domains","configType"));
    }

    /**新咨询
     * @return Redirect
     */
    public function manageVideo(){
        $userId=session('userId');
        $singleArray=array();
        $moneyArray=array();
        $type=isset($_GET['type'])?$_GET['type']:"不限";
        $configTypeArray=array("已推送"=>4,"已响应"=>5,"已完成"=>7,"已评价"=>8,"正在咨询"=>6,"异常终止"=>9);
        $configType=isset($_GET['configType'])?$_GET['configType']:"不限";
        $configTypeWhere = ($configType=='不限')?[]:["t_c_consultverify.configid"=>$configTypeArray[$configType]];
        $typeWhere=($type!="不限")?array("t_c_consult.domain1"=>$type):array();
        $consultType=isset($_GET['consultType'])?$_GET['consultType']:"不限";
       /* $consultIds=DB::table("view_consultstatus")->select('consultid')->where('configid',6)->where("userid",$userId)->get();
       foreach($consultIds as $val){
           $countExperts=DB::table("t_c_consultresponse")->where("consultid",$val->consultid)->where("state",3)->count();
           if($countExperts>1){
               $moneyArray[]=$val->consultid;
           }else{
               $singleArray[]=$val->consultid;
           }
       }*/
        $result=DB::table("t_c_consult")
            ->leftJoin("t_c_consultverify","t_c_consultverify.consultid","=","t_c_consult.consultid")
            ->select("t_c_consult.consultid",'t_c_consultverify.configid',"t_c_consult.domain1","t_c_consult.domain2","t_c_consult.created_at","t_c_consult.starttime","t_c_consult.endtime","t_c_consult.brief")
            ->whereRaw('t_c_consultverify.id in (select max(id) from t_c_consultverify group by consultid)')
            ->where("t_c_consult.userid",$userId)
            ->whereNotIn('t_c_consultverify.configid',[1,2,3])
            ->where($configTypeWhere)
            ->where($typeWhere);
        switch($consultType){
            case "不限":
                $datas=$result->orderBy("t_c_consult.created_at","desc")->paginate(6);
            break;
            case "单人":
                $datas=$result->where('t_c_consult.videotype',0)->orderBy("t_c_consult.created_at","desc")->paginate(6);
                break;
            case "多人":
                $datas=$result->where('t_c_consult.videotype',1)->orderBy("t_c_consult.created_at","desc")->paginate(6);
                break;
           /* case "未知":
                $datas=$result->whereIn('t_c_consultverify.configid',[4,5])->orderBy("t_c_consult.created_at","desc")->paginate(6);
                break;*/
        }
        $count=clone $result;
        $counts=$count->count();
        foreach ($datas as $data){
            $data->created_at=date("Y-m-d",strtotime($data->created_at));
            $data->starttime=date("m月d日 H:i",strtotime($data->starttime));
            $data->endtime=date("m月d日 H:i",strtotime($data->endtime));
            $totals=DB::table("t_c_consultresponse")->where("consultid",$data->consultid)->count();
            if($totals!=0){
                $data->state="指定专家";
            }else{
                $data->state="匹配专家";
            }
            switch($data->domain1){
                case '找资金':
                    $data->icon = 'v-manage-link-icon';
                    break;
                case '找技术':
                    $data->icon = 'v-manage-link-icon nature1';
                    break;
                case '定战略':
                    $data->icon = 'v-manage-link-icon nature2';
                    break;
                case '找市场':
                    $data->icon = 'v-manage-link-icon nature3';
                    break;
                default :
                    $data->icon = 'v-manage-link-icon';
                    break;
            }
            $configname = DB::table('t_c_consultverifyconfig')->where('configid',$data->configid)->first()->name;
            $data->configname = $configname;
            switch($data->configid){
                case 1:
                    $data->btnicon = 'eventwait';
                    break;
                case 2:
                    $data->btnicon = 'eventfollow';
                    break;
                case 3:
                    $data->btnicon = 'eventdont';
                    break;
                case 4:
                    $data->btnicon = 'eventput';
                    break;
                case 5:
                    $data->btnicon = 'response';
                    break;
                case 6:
                    $data->btnicon = 'eventing';
                    break;
                case 7:
                    $data->btnicon = 'eventend';
                    break;
                case 8:
                    $data->btnicon = 'eventend';
                    break;
                case 9:
                    $data->btnicon = 'eventdont';
                    break;
            }
        }
        $domains=DB::table("T_COMMON_DOMAINTYPE")->select('domainname')->where("level",1)->get();
        return view("myenterprise.newVideoManage",compact("datas","type","counts",'type2','domains','configType','consultType'));
    }

    /**办事管理视频
     * @param $eventId
     * @return mixed
     */
    public function eventVideo($eventId){
        return view('myenterprise.enevtVideo',compact('eventId'));
        $consulttime=DB::table("t_e_event")->where("eventid",$eventId)->pluck("consulttime");
        if(!empty($consulttime)){
            return view('myenterprise.enevtVideo',compact('eventId'));
        }else{
            return redirect('uct_works/detail/'.$eventId);
        }

    }

    /**获取办事中视频的免费时长
     * @return array
     */
    public  function  getEventVideoTime(){
        $res=array();
        $eventId=$_POST['eventId'];
        $consultTime=DB::table('t_e_event')->where("eventid",$eventId)->pluck('consulttime');
        if(!empty($consultTime)){
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }

    /**
     * 办事减少咨询时间
     * @return array
     */
    public  function reduceTime(){
        $res=array();
        $eventVideoTime=$_POST['eventVideoTime'];
        $eventVideoTimes=explode(":",$eventVideoTime);
        $eventId=$_POST['eventId'];
        $consulttime=DB::table("t_e_event")->where("eventid",$eventId)->pluck("consulttime");
        if(intval($eventVideoTimes[0])>$consulttime){
            $consulttime=0;
            $res['code']="error";
        }else{
            $consulttime=$consulttime-intval($eventVideoTimes[0]);
            $res['code']="success";
        }
        $datas=DB::table("t_e_event")->where("eventid",$eventId)->update([
            "consulttime"=>$consulttime,
            "updated_at"=>date("Y-m-d H:i:s",time()),
        ]);
        return $res;

    }

    /**比较当前时间与办事剩余的时间
     * @return array
     */
    public  function compareTime(){
        $res=array();
        $eventId=$_POST['eventId'];
        $timeLong=explode(":",$_POST['timeLong']);
        $consulttime=DB::table("t_e_event")->where("eventid",$eventId)->pluck("consulttime");
        if(intval($timeLong[0])>$consulttime){
            $res['code']="error";
        }else{
            $res['code']="success";
        }
        return $res;
    }

    /**视频咨询时间
     * @return array
     */
    public  function compareConsultTime(){
        $res=array();
        $consultId=$_POST['consultId'];
        $nowTime=$_POST['nowTime'];
        $endTime=DB::table("t_c_consult")->where("consultId",$consultId)->pluck("endtime");
        if(strtotime($nowTime)>=strtotime($endTime)){
            $res['code']="success";
        }else{
            $res['code']="error";
        }
        return $res;
    }

    /**
     * 企业办事充值判断
     */
    public function eventCharge(){
        $res=array();
        $userId=session('userId');
        $data=$_POST;
        if(empty(session('userId'))){
            return ['icon'=>0,'code' => 7,'msg' => '您还未登陆请登录','url' => url('login')];
        }
        $enterprise=DB::table("t_u_enterprise")
            ->leftJoin("t_u_enterpriseverify","t_u_enterprise.enterpriseid","=","t_u_enterpriseverify.enterpriseid")
            ->where("t_u_enterprise.userid",$userId)
            ->orderBy("t_u_enterpriseverify.id","desc")
            ->first();
        if(empty($enterprise) || $enterprise->configid != 3){
            return ['icon'=>3,'code' => 2,'msg' => '企业不存在或者未通过认证','url' => url('uct_member')];
        }
        $enterpriseid = DB::table('t_u_enterprise')
            ->where('userid', $userId)
            ->pluck('enterpriseid') ;
        $results = DB::table('t_u_enterprisemember')
            ->where('enterpriseid', $enterpriseid)
            ->get() ;
        if(!$results){
            //不存在记录 202 开通会员操作
            return ['icon'=>3,'code' => 3 ,'msg' => '您不是会员,请办理会员或充值单次收费','url' => '?'];
        }
        $datas = DB::table('t_u_enterprisemember')
            ->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
            ->where('enterpriseid', $enterpriseid)
            ->get() ;
        //判断是否是普通会员
        if ($datas[0]->cost == 0){
            if ($datas[0]->eventcount == 1){
                $benben = $this->saveEvent($data,"非无限",$enterpriseid) ;
                return $benben ;
            }else{
                //没有可剩余次数 ，去充值次数
                return ['icon'=>3,'code' => 4 ,'msg' => '没有可剩余次数 ，是否充值','url' => '?'];
            }
        }else{
            //是否过期
            $endTime = $datas[0] -> endtime ;
            if($endTime <= date("Y-m-d H:i:s")){
                //会员过期返回204
                return ['icon'=>3,'code' => 5 ,'msg' => '您的会员已过期,是否续交会员','url' => '?'];
            }else{
                if ($datas[0]->eventcounts  == "无限"){
                    $benben = $this->saveEvent($data,"无限",$enterpriseid) ;
                    return $benben ;
                }else if ($datas[0]->eventcount  >= 1 ){
                    $benben = $this->saveEvent($data,"非无限",$enterpriseid) ;
                    return $benben ;
                }else{
                    //超过剩余使用次数 ，优惠充次
                    return ['icon'=>3,'code' => 6 ,'msg' => '没有可用次数 ，是否优惠充值','url' => '?'];
                }
            }
        }
    }

    /**企业咨询判断
     * @return array
     */
    public function  consultCharge(){
        $res=array();
        $userId=session('userId');
        $data=$_POST;
        $times=strtotime($data['dateStart'])+$data['dateEnd']*60;
        $endtime=date("Y-m-d H:i",$times);
        $data['dateEnd']=$endtime;
        $enterprise=DB::table("t_u_enterprise")
            ->leftJoin("t_u_enterpriseverify","t_u_enterprise.enterpriseid","=","t_u_enterpriseverify.enterpriseid")
            ->where("t_u_enterprise.userid",$userId)
            ->orderBy("t_u_enterpriseverify.id","desc")
            ->first();
        if(empty($enterprise) || $enterprise->configid != 3){
            return ['icon'=>3,'code' => 2,'msg' => '企业不存在或者未通过认证','url' => url('uct_member')];
        }
        $enterpriseid = DB::table('t_u_enterprise')
            ->where('userid', $userId)
            ->pluck('enterpriseid') ;
        $ben = DB::table('t_u_enterprisemember')
            ->where('enterpriseid', $enterpriseid)
            ->get() ;
        //时间段
        $time = (strtotime($data['dateEnd']) - strtotime($data['dateStart'])) / 60 ;
        if(!$ben){
            //不存在记录 202 开通会员操作
            return ['icon'=>3,'time'=>$time,'code' => 3 ,'msg' => '您不是会员,请办理会员或充值单次收费'];
        }
        $datas = DB::table('t_u_enterprisemember')
            ->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
            ->where('enterpriseid', $enterpriseid)
            ->get() ;
        //判断是否是普通会员
        if ($datas[0]->cost == 0){
            if ($datas[0]->consultcount >= $time ){
               // $this->time111($enterpriseid ,$payload ) ;
                $benben = $this->saveVideo($data , $enterpriseid ) ;
                return $benben ;
            }else{
                //小于时长  ，去充值
                //return $this->response->array(["return_code" => 203]);
                return ['icon'=>3,'time'=>$time,'code' => 4 ,'msg' => '剩余时间不够，请充值'];
            }
        }else{
            //是否过期
            $endTime = $datas[0] -> endtime ;
            if($endTime <= date("Y-m-d H:i:s")){
                //会员过期返回204
               // return $this->response->array(["return_code" => 204]);
                return ['icon'=>3,'time'=>$time,'code' => 5 ,'msg' => '您的会员已过期,是否续交会员'];
            }else{
                if ($datas[0]->consultcount >= $time ){
                   // $this->time111($enterpriseid ,$payload ) ;
                    $benben = $this->saveVideo($data , $enterpriseid ) ;
                    return $benben ;
                }else{
                   // return $this->response->array(["return_code" => 205]);
                    return ['icon'=>3,'time'=>$time,'code' => 6 ,'msg' => '没有可用时长 ，是否优惠充值'];
                }
            }
        }
    }

    /**企业认证修改
     * @param $enterpriseId
     * @return mixed
     */
    public function updateEnterprise($enterpriseId){
        $data=DB::table("t_u_enterprise")->where("enterpriseid",$enterpriseId)->first();
        return view("myenterprise.updateEnterprise",compact('data'));
    }
    /**申请视频咨询
     * @return mixed
     */
    public function linemeet(){
        //$cate = DB::table('t_common_domaintype')->get();

        $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();
        //dd($memberrights);
        $userid = session('userId');

        $result = DB::table("t_u_enterprise")
            ->leftJoin('t_u_enterpriseverify','t_u_enterprise.enterpriseid','=','t_u_enterpriseverify.enterpriseid')
            ->whereRaw('t_u_enterpriseverify.id in (select max(id) from t_u_enterpriseverify group by enterpriseid)')
            ->where('t_u_enterprise.userid',$userid)
            ->select('t_u_enterpriseverify.configid')
            ->first();
        //dd($enterpriseid);

        if(!$result){
            $enterpriseid = 0000;
        }else{
            $enterpriseid = $result->configid;
        }

        return view("myenterprise.lineMeet",compact('memberrights','enterpriseid'));
    }

    /**
     * 企业线下约见专家
     */
    public function startMeet()
    {
        $res=array();
        $userId=session('userId');
        $data=$_POST;
        /*dd($data);*/
        $price = $data['time'] * $data['linefee'];

        $result = DB::table("t_l_linemeet")->insert([
        "userid" => $userId,
        "timelot" => $data['time'],
        'expertid' => $data['expertid'],
        'contents' => $data['describe'],
        'price' => $price,
        "puttime" => date("Y-m-d H:i:s", time()),
        "created_at" => date("Y-m-d H:i:s", time()),
        "updated_at" => date("Y-m-d H:i:s", time())
    ]);

        $linemeetid = DB::table('t_l_linemeet')->where('userid',$userId)->orderBy('id','desc')->first()->id;

        DB::table("t_l_linemeetverify")->insert([
        "meetid" => $linemeetid,
        "configid" => 2,
        'verifytime' => date("Y-m-d H:i:s", time()),

        "created_at" => date("Y-m-d H:i:s", time()),
        "updated_at" => date("Y-m-d H:i:s", time())
    ]);



        if($result){
            return  ['msg'=>'success','linemeetid'=>$linemeetid];
        }else{
            return  ['msg'=>'error'];

        }
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */


    /**
     * 查看约见信息
     */

    public function lineMeetData()
    {

        $userId=session('userId');

        $type = isset($_GET['type'])?$_GET['type']:"不限";

        $linemeetconfigtype = array("已提交"=>1,"未通过"=>2,"已通过"=>3,"已完成"=>4);

        $typeWhere=($type!="不限")?array("t_l_linemeetverify.configid"=>$linemeetconfigtype[$type]):array();

        //dd($typeWhere);

        $datas=DB::table("t_l_linemeet")
            ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
            ->leftJoin("t_u_expert","t_u_expert.expertid","=","t_l_linemeet.expertid")
            ->select("t_l_linemeet.*",'t_l_linemeetverify.configid','t_u_expert.expertname','t_u_expert.domain2')
            ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
            ->where("t_l_linemeet.userid",$userId)
            ->where($typeWhere)
            ->orderBy('t_l_linemeet.puttime','desc')
            ->paginate(6);
        return view("myenterprise.lineMeetManage",compact("datas","type","counts"));
    }

    public function lineMeetDetail($linemeetid)
    {
            $configid = DB::table('t_l_linemeet')
                ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
                ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
                ->where('meetid',$linemeetid)->first()->configid;

        if($configid == 2){

                $data = DB::table('t_l_linemeet')
                    ->leftJoin('t_u_expert','t_u_expert.expertid',"=",'t_l_linemeet.expertid')
                    ->leftJoin('t_u_expertfee','t_u_expertfee.expertid',"=",'t_l_linemeet.expertid')
                    //->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
                    ->where('t_l_linemeet.id',$linemeetid)
                    ->select('t_l_linemeet.*','t_u_expert.expertname','t_u_expert.showimage','t_u_expertfee.linefee')
                    ->first();
                $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();

                return view("myenterprise.lineMeet2",compact('data','memberrights'));
            }elseif ($configid == 3){
                $data = DB::table('t_l_linemeet')
                    ->leftJoin('t_u_expert','t_u_expert.expertid',"=",'t_l_linemeet.expertid')
                    ->leftJoin('t_u_expertfee','t_u_expertfee.expertid',"=",'t_l_linemeet.expertid')
                    //->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
                    ->where('t_l_linemeet.id',$linemeetid)
                    ->select('t_l_linemeet.*','t_u_expert.expertname','t_u_expert.showimage','t_u_expertfee.linefee')
                    ->first();

                $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();

                return view("myenterprise.lineMeet3",compact('data','memberrights'));
            }elseif ($configid == 4){
                $data = DB::table('t_l_linemeet')
                    ->leftJoin('t_u_expert','t_u_expert.expertid',"=",'t_l_linemeet.expertid')
                    ->leftJoin('t_u_expertfee','t_u_expertfee.expertid',"=",'t_l_linemeet.expertid')
                    //->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
                    ->where('t_l_linemeet.id',$linemeetid)
                    ->select('t_l_linemeet.*','t_u_expert.expertname','t_u_expert.showimage','t_u_expertfee.linefee')
                    ->first();

                $phone = DB::table('t_u_expert')
                    ->leftJoin('t_u_user','t_u_user.userid','=','t_u_expert.userid')
                    ->where('t_u_expert.expertid',$data->expertid)
                    ->first()->phone;

                $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();

                return view("myenterprise.lineMeet4",compact('data','memberrights','phone'));
            }
    }
}
