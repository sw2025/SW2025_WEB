<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class MyExpertController extends Controller
{
    /**专家认证
     * @return mixed
     */
    public function  expert(){
        $cate = DB::table('t_common_domaintype')->get();
        $result = DB::table('t_u_expert')
            ->leftjoin('t_u_expertverify',"t_u_expert.expertid","=","t_u_expertverify.expertid")
            ->where(['userid' => session('userId')])
            ->whereRaw('T_U_EXPERTVERIFY.id in (select max(id) from T_U_EXPERTVERIFY group by expertid)')
            ->orderBy('configid','desc')
            ->first();
/*        dd($result);*/
        if(!empty($result)) {
            if ($result->configid == 1) {
                return redirect()->action('MyExpertController@expert2');
            } elseif ($result->configid == 2) {
                return redirect()->action('MyExpertController@expert3');
            }
        }
        return view("myexpert.expert",compact('cate','result'));


    }
    /**专家审核
     */
    public function  expert2(){
        $data=DB::table("T_U_USER")
            ->leftJoin("T_U_EXPERT","T_U_USER.USERID","=","T_U_EXPERT.USERID")
            ->leftJoin("T_U_EXPERTVERIFY","T_U_EXPERT.EXPERTID","=","T_U_EXPERTVERIFY.expertid")
            ->select("T_U_EXPERT.*","T_U_EXPERTVERIFY.configid")
            ->where("T_U_EXPERT.userid",session('userId'))
            ->where("T_U_EXPERTVERIFY.configid",1)
            ->whereRaw('T_U_EXPERTVERIFY.id in (select max(id) from T_U_EXPERTVERIFY group by expertid)')
            ->first();
        if(!$data){
            return redirect()->action('MyExpertController@expert');
        }
        return view("myexpert.expert2",compact('data'));
    }
    /**认证成功
     */
    public function expert3(){
        $data=DB::table("T_U_USER")
            ->leftJoin("T_U_EXPERT","T_U_USER.USERID","=","T_U_EXPERT.USERID")
            ->leftJoin("T_U_EXPERTVERIFY","T_U_EXPERT.EXPERTID","=","T_U_EXPERTVERIFY.expertid")
            ->select("T_U_EXPERT.*","T_U_EXPERTVERIFY.configid")
            ->where("T_U_EXPERT.userid",session('userId'))
            ->whereIn("T_U_EXPERTVERIFY.configid",[2,4])
            ->whereRaw('T_U_EXPERTVERIFY.id in (select max(id) from T_U_EXPERTVERIFY group by expertid)')
            ->first();
        if(!$data){
            return redirect()->action('MyExpertController@expert');
        }
        return view("myexpert.expert3",compact('data'));
    }


    /**专家资料提交
     */
    public function expertData(Request $request)
    {
        //判断是否为ajax请求
        if($request->ajax()){
            //判断是否登陆
            if(!empty(session('userId'))){
                $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
                $data = $request->input();
                $domain1 = explode('-',$data['industry'])[0];
                $domain2 = explode('-',$data['industry'])[1];
                $domain2 = trim(str_replace('/',',',$domain2),',');
                //开启事务
                DB::beginTransaction();
                try{
                    if(empty($expertid->expertid)) {
                        $expertid = DB::table("T_U_EXPERT")
                            ->insertGetId([
                                "userid" => session('userId'),
                                "expertname" => $data['name'],
                                "category" => $data['category'],
                                "address" => $data['address'],
                                "licenceimage" => $data['photo1'],
                                "showimage" => $data['photo2'],
                                "brief" => $data['brief'],
                                "domain1" => $domain1,
                                "domain2" => $domain2,
                                "created_at" => date("Y-m-d H:i:s", time()),
                                "updated_at" => date("Y-m-d H:i:s", time())
                            ]);
                    }else{
                        $expertdata = DB::table("T_U_EXPERT")
                            ->where('expertid',$expertid->expertid)
                            ->update([
                                "expertname" => $data['name'],
                                "category" => $data['category'],
                                "address" => $data['address'],
                                "licenceimage" => $data['photo1'],
                                "showimage" => $data['photo2'],
                                "brief" => $data['brief'],
                                "domain1" => $domain1,
                                "domain2" => $domain2,
                                "created_at" => date("Y-m-d H:i:s", time()),
                                "updated_at" => date("Y-m-d H:i:s", time())
                            ]);
                        $expertid = $expertid->expertid;
                    }
                    if(!empty($expertid)){
                       // $configid=DB::table("t_u_expertverify")->where("expertid",$expertid)->orderBy("ID",'desc')->first();
                        $result=DB::table("T_U_EXPERTVERIFY")
                            ->insert([
                                    "expertid"=>$expertid,
                                    "configid"=>1,
                                    "verifytime"=>date("Y-m-d H:i:s",time()),
                                    "updated_at"=>date("Y-m-d H:i:s",time())
                                ]);
                        }
                    DB::commit();
                    return ['msg' => '添加专家认证成功,进入审核阶段','icon' => 1];
                }catch(Exception $e){
                
                    DB::rollBack();
                    return ['msg' => '处理失败','icon' => 2];
                }
            }
            return ['msg' => '请登录','icon' => 2];
        }
        return ['msg' => '非法访问','icon' => 2];
    }

    /**我的办事
     * @return mixed
     */
    public function myworks(Request $request){
        //获取到登陆用户的专家的id
        $expert = DB::table('t_u_expert')->where('userid',session('userId'))->first();
        if(empty($expert)){
            $expertid = 0;
        } else {
            $expertid = $expert->expertid;
        }
            $countobj = DB::table('t_e_eventresponse as res')
                ->leftJoin('view_eventstatus as status','status.eventid','=','res.eventid');
            $countobj2 = clone $countobj;
            $countobj3 = clone $countobj;
            //专家已响应的办事数量
            $responsecount = $countobj->where(['res.state' => 2,'res.expertid' => $expertid,'status.configid' => 5])->count();
            //专家受邀请（被推送）的办事数量
            $putcount = $countobj2->where(['res.expertid' => $expertid,'status.configid' => 4])->count();
            //专家已经完成的办事数量
            $complatecount = $countobj3->where(['res.state' => 3,'res.expertid' => $expertid,'status.configid' => 7])->count();
            $datas = DB::table('t_e_eventresponse as res')
                ->leftJoin('t_e_event as event','event.eventid','=','res.eventid')
                ->leftJoin('t_u_enterprise as ent','event.userid','=','ent.userid')
                ->leftJoin('view_eventstatus as status','status.eventid','=','res.eventid')
                ->select('res.*','event.domain1','event.domain2','event.brief','status.configid','event.eventtime','ent.enterprisename as name','res.state')
                ->whereRaw('res.id in (select max(`t_e_eventresponse`.`id`) from `t_e_eventresponse` group by `t_e_eventresponse`.`expertid`,eventid)');
            $obj = clone $datas;
            $ajaxobj = clone $datas;
            $eventobj = clone $datas;
            //克隆ajax请求的对象
            $ajaxobj = $ajaxobj->where(['res.expertid' => $expertid]);
            //datas为办事推送列表
            $datas = $datas
                ->where(['res.expertid' => $expertid])
                ->whereIn('status.configid',[4,5])
                ->whereIn('res.state',[0,1])
                ->orderBy('res.id','desc')
                ->paginate(6);

            $waitcount = $eventobj
                ->where(['res.expertid' => $expertid])
                ->whereIn('status.configid',[4,5])
                ->whereIn('res.state',[0,1])
                ->count();
            //datas2为我的办事列表
            $datas2 = $obj
                ->where(['res.expertid' => $expertid])
                ->whereIn('status.configid',[5,6,7,8,9])
                ->orderBy('res.id','desc')
                ->paginate(6);
            //调用eventclass中的方法进行对象的处理
            $datas = \EventClass::handelObj2($datas);
            $datas2 = \EventClass::handelObj2($datas2);
            //ajax请求判定返回指定的对象
            if($request->ajax()){
                $action = $request->input()['action'];
                if(!$action){
                    //action为0时世界返回的是办事推送列表
                    return $datas;
                } elseif($action == 1){
                    //action为1的时候返回的是我的办事列表
                    return $datas2;
                } else {
                    //其余的action在ajax请求的时候是configid 通过where条件来进行查询
                    $ajaxobj = $ajaxobj->where(['status.configid' => $action])->paginate(2);
                    $ajaxobj = \EventClass::handelObj($ajaxobj);
                    return $ajaxobj;
                }
            }
            $token = Crypt::encrypt(session('userId'));
            return view("myexpert.newMyWork",compact('datas','datas2','responsecount','putcount','complatecount','token','waitcount'));
    }
    
    
    /*8888888888888888888888888888888888888888*/
    public function mywork(){
        $userId=session('userId');
        $expert = DB::table('t_u_expert')->where('userid',session('userId'))->first();
        if(empty($expert)){
            $expertid = 0;
        } else {
            $expertid = $expert->expertid;
        }
        if(!isset($_GET['index']) || $_GET['index']==0){
            $configTypeWhere=["t_e_eventverify.configid"=>4];
            $typeWhere=[];
            $index=0;
        }else{
            $type=isset($_GET['domain'])?$_GET['domain']:"不限";
            $configTypeArray=array("已响应"=>5,"正在办事"=>6,"已完成"=>7,"已评价"=>8,"异常终止"=>9);
            $configType=isset($_GET['configType'])?$_GET['configType']:"不限";
            $typeWhere = ($type=='不限')?[]:["t_e_event.domain1"=>$type];
            $configTypeWhere = ($configType=='不限')?[]:["view_eventstatus.configid"=>$configTypeArray[$configType]];
            $index=$_GET['index'];
        }
        $result=DB::table("t_e_eventresponse")
            ->leftJoin('t_e_event','t_e_event.eventid','=','t_e_eventresponse.eventid')
            ->leftJoin('t_u_enterprise','t_e_event.userid','=','t_u_enterprise.userid')
            ->leftJoin('view_eventstatus','view_eventstatus.eventid','=','t_e_eventresponse.eventid')
            ->select("t_e_event.eventid","t_e_event.domain1","t_e_event.domain2","t_e_event.created_at","t_e_event.brief","t_e_event.eventtime","view_eventstatus.configid","t_u_enterprise.enterprisename")
            ->whereRaw('t_e_eventresponse.id in (select max(`t_e_eventresponse`.`id`) from `t_e_eventresponse` group by `t_e_eventresponse`.`expertid`,eventid)');
        if($index==0){
            $datas = $result
                ->where('t_e_eventresponse.expertid',$expertid)
                ->where('view_eventstatus.configid' ,4)
                ->orderBy('t_e_eventresponse.id','desc')->paginate(6);
            $counts = DB::table('t_e_eventresponse as res')
                ->leftJoin('view_eventstatus as status','status.eventid','=','res.eventid')
                ->whereRaw('res.id in (select max(`t_e_eventresponse`.`id`) from `t_e_eventresponse` group by `t_e_eventresponse`.`expertid`,eventid)')
                ->where(['res.expertid' => $expertid,'status.configid' => 4])
                ->count();
        }else{
            if($configType=="不限"){
                $datas=$result ->where('t_e_eventresponse.expertid',$expertid)->where($typeWhere)->whereIn('view_eventstatus.configid',[5,6,7,8,9])->orderBy('t_e_eventresponse.id','desc')->paginate(6);
            }else{
                $datas=$result ->where('t_e_eventresponse.expertid',$expertid)->where($typeWhere)->where($configTypeWhere)->orderBy('t_e_eventresponse.id','desc')->paginate(6);
            }
        }
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
        return view("myexpert.newMyWork",compact("datas","type","counts","domains","configType","index"));
    }

    /**我的办事详情
     * @return mixed
     */
    public function  workDetail($eventid){
        $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertid;
        $datas = DB::table('t_e_event as event')
            ->leftJoin('t_e_eventresponse as res','event.eventid','=','res.eventid')
            ->leftJoin('t_u_enterprise as ent','event.userid','=','ent.userid')
            ->leftJoin('view_eventstatus as status','status.eventid','=','res.eventid')
            ->where(['event.eventid' => $eventid,'expertid' => $expertid])
            ->whereRaw('res.id in (select max(id) from t_e_eventresponse group by eventid,expertid)')
            ->select('event.*','ent.enterprisename','res.expertid','status.configid','res.state')
            ->first();

        $selExperts=DB::table("t_u_enterprise")
            ->where('userid',$datas->userid)
            ->first();
        if(!empty($datas) && $datas->configid != 9 && $datas->state == 5){
            $selExperts2=DB::table("t_e_eventresponse")
                ->leftJoin("t_u_expert","t_e_eventresponse.expertid","=","t_u_expert.expertid")
                ->where("t_u_expert.expertid",$expertid)
                ->where("eventid",$eventid)
                ->first();
            $remark = DB::table('t_e_eventverify')
                ->where("t_e_eventverify.eventid",$eventid)
                ->whereRaw('t_e_eventverify.id in (select max(id) from t_e_eventverify group by eventid)')->first();
            return view("myexpert.workDetailFaild",compact('datas','selExperts','eventid','selExperts2','remark'));
        }
        if(!$datas){
            return redirect('/');
        } elseif ($datas->configid == 6 || $datas->configid == 8 || $datas->configid == 7 || $datas->configid == 9){
           // dd(234);
            return redirect('uct_mywork/workDetails/'.$eventid);
        }
        $token = Crypt::encrypt(session('userId'));
        return view("myexpert.workDetail",compact('datas','token','selExperts'));
    }

    /**专家响应办事
     * @param Request $request
     * @return array
     */
    public function responseEvent (Request $request) {
        //判断是否为ajax请求
        if($request->ajax()){
            $data = $request->input();
            //对token进行验证
            //获取到该用户对应的专家的id
                $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertid;
                $expertIds=explode(",",$expertid);
                //$counts=DB::table("t_e_eventresponse")->where("eventid",$data['eventid'])->where('state',1)->count();
                $counts2=DB::table("t_e_eventresponse")->where("eventid",$data['eventid'])->where('state',0)->count();
                DB::beginTransaction();
                try{
                    //查询这个办事是否已经响应过了
                    $verify3 = DB::table('view_eventstatus')->where(['eventid' => $data['eventid']])->where('configid','>','5')->first();
                    if(!empty($verify3)){
                        return ['msg' => '对不起该办事企业已经确定其他专家，请您刷新页面'];
                    }
                    //查询是否存在响应的情况
                    $verify = DB::table('t_e_eventresponse')->where(['expertid' => $expertid,'eventid' => $data['eventid'],'state' => 2])->first();
                    if(!$verify){
                        if($counts2){
                            $res= DB::table('t_e_eventresponse')->insert([
                                'expertid' => $expertid,
                                'eventid' => $data['eventid'],
                                'state' => 3,
                                'responsetime' => date('Y-m-d H:i:s',time()),
                                'updated_at' => date('Y-m-d H:i:s',time())
                            ]);
                        }else{
                            $res= DB::table('t_e_eventresponse')->insert([
                                'expertid' => $expertid,
                                'eventid' => $data['eventid'],
                                'state' => 2,
                                'responsetime' => date('Y-m-d H:i:s',time()),
                                'updated_at' => date('Y-m-d H:i:s',time())
                            ]); 
                        }
                        if($res){
                            $name=DB::table("t_u_expert")->where("expertid",$expertid)->pluck('expertname');
                            $phone=DB::table('t_e_event')
                                ->leftJoin('t_u_user','t_e_event.userid','=','t_u_user.userid')
                                ->where('eventid',$data['eventid'])
                                ->pluck('phone');
                            $time=DB::table('t_e_event')->where('eventid',$data['eventid'])->pluck('created_at');
                            $this->_sendSms($phone,'办事请求','answer',$name,$time);
                        }
                    }else {
                        return ['msg' => '您已经响应过此办事','icon' => 2];
                    }
                    //查询这个办事是否已经响应过了
                    $verify2 = DB::table('t_e_eventverify')->where(['eventid' => $data['eventid'],'configid' => 5])->first();
                    if(!$verify2){
                        if($counts2){
                            DB::table('t_e_eventverify')->insert([
                                'eventid' => $data['eventid'],
                                'configid' => 6,
                                'verifytime' =>date('Y-m-d H:i:s',time()),
                                'updated_at' =>date('Y-m-d H:i:s',time())
                            ]);
                            \UserClass::createEventGroups($expertIds,$data['eventid']);
                        }else{
                            DB::table('t_e_eventverify')->insert([
                                'eventid' => $data['eventid'],
                                'configid' => 5,
                                'verifytime' =>date('Y-m-d H:i:s',time()),
                                'updated_at' =>date('Y-m-d H:i:s',time())
                            ]);
                        }
                    }
                    DB::commit();
                    return ['msg' => '响应成功,请等待企业回应','icon' => 1];
                }catch(Exception $e)
                {
                    DB::rollBack();
                    return ['msg' => '处理失败','icon' => 2];
                }

        }
        return ['msg' => '非法操作','icon' => 2];
    }

    /**我的咨询
     * @return mixed
     */
    public function myasks(Request $request){

        //获取到登陆用户的专家的id
        $expert = DB::table('t_u_expert')->where('userid',session('userId'))->first();
        if(empty($expert)){
            $expertid = 0;
        } else {
            $expertid = $expert->expertid;
        }
        $countobj = DB::table('t_c_consultresponse as res')
            ->leftJoin('view_consultstatus as status','status.consultid','=','res.consultid');
        $countobj2 = clone $countobj;
        $countobj3 = clone $countobj;
        //专家已响应的咨询数量
        $responsecount = $countobj->where(['res.state' => 2,'res.expertid' => $expertid,'status.configid' => 5])->count();
        //专家受邀请（被推送）的咨询数量
        $putcount = $countobj2->where(['res.expertid' => $expertid,'status.configid' => 4])->count();
        //专家已经完成的咨询数量
        $complatecount = $countobj3->where(['res.state' => 3,'res.expertid' => $expertid,'status.configid' => 7])->count();

        $datas = DB::table('t_c_consultresponse as res')
            ->leftJoin('t_c_consult as consult','consult.consultid','=','res.consultid')
            ->leftJoin('t_u_enterprise as ent','consult.userid','=','ent.userid')
            ->leftJoin('view_consultstatus as status','status.consultid','=','res.consultid')
            //->whereRaw('res.id in (select max(id) from t_c_consultresponse group by consultid)')
            ->whereRaw('res.id in (select max(`t_c_consultresponse`.`id`) from `t_c_consultresponse` group by `t_c_consultresponse`.`consultid`,expertid)')
            ->select('res.*','consult.domain1','consult.domain2','consult.brief','status.configid','consult.consulttime','consult.starttime','consult.endtime','ent.enterprisename as name');
        $obj = clone $datas;
        $ajaxobj = clone $datas;
        $ajaxobj = $ajaxobj->where(['res.expertid' => $expertid]);

        $countobj4 = clone $datas;
        $count= $countobj4
           ->where(['res.expertid' => $expertid])
            ->whereIn('status.configid' ,[4,5])
            ->whereIn('res.state',[0,1])
           ->orderBy('res.id','desc')
           ->count();

        $datas = $datas
            ->where(['res.expertid' => $expertid])
            ->whereIn('status.configid' ,[4,5])
            ->whereIn('res.state',[0,1])
            ->orderBy('res.id','desc')
            ->paginate(6);
        $datas2 = $obj
            ->where(['res.expertid' => $expertid])
            ->whereIn('status.configid',[5,6,7,8,9])
            ->orderBy('res.id','desc')
            ->paginate(6);
        $datas = \ConsultClass::handelObj2($datas);
        $datas2 = \ConsultClass::handelObj2($datas2);
        if($request->ajax()){
            $action = $request->input()['action'];
            if(!$action){
                return $datas;
            } elseif($action == 1){
                return $datas2;
            } else {
                $ajaxobj = $ajaxobj->where(['status.configid' => $action])->paginate(2);
                $ajaxobj = \ConsultClass::handelObj($ajaxobj);
                return $ajaxobj;
            }
        }
        $token = Crypt::encrypt(session('userId'));

        return view("myexpert.newMyAsk",compact('datas','datas2','responsecount','putcount','complatecount','token','count'));
    }

    /****************************************************/
    public function myask(){

        $userId=session('userId');
        $expert = DB::table('t_u_expert')->where('userid',session('userId'))->first();
        if(empty($expert)){
            $expertid = 0;
        } else {
            $expertid = $expert->expertid;
        }
        if(!isset($_GET['index']) || $_GET['index']==0){
            $typeWhere=[];
            $configTypeWhere=[];
            $index=0;
        }else{
            $type=isset($_GET['type'])?$_GET['type']:"不限";
            $configTypeArray=array("已推送"=>4,"已响应"=>5,"已完成"=>7,"已评价"=>8,"正在咨询"=>6,"异常终止"=>9);
            $configType=isset($_GET['configType'])?$_GET['configType']:"不限";
            $configTypeWhere = ($configType=='不限')?[]:["view_consultstatus.configid"=>$configTypeArray[$configType]];
            $typeWhere=($type!="不限")?array("t_c_consult.domain1"=>$type):array();
            $index=1;
        }
        $result=DB::table("t_c_consultresponse")
            ->leftJoin('t_c_consult','t_c_consult.consultid','=','t_c_consultresponse.consultid')
            ->leftJoin('t_u_enterprise','t_c_consult.userid','=','t_u_enterprise.userid')
            ->leftJoin('view_consultstatus','view_consultstatus.consultid','=','t_c_consultresponse.consultid')
            ->select("t_c_consult.consultid","t_c_consult.domain1","t_c_consult.domain2","t_c_consult.created_at","t_c_consult.starttime","t_c_consult.endtime","t_c_consult.brief","t_c_consult.consulttime","view_consultstatus.configid",'t_c_consult.userid',"t_u_enterprise.enterprisename")
            ->whereRaw('t_c_consultresponse.id in (select max(`t_c_consultresponse`.`id`) from `t_c_consultresponse` group by `t_c_consultresponse`.`consultid`,expertid)');
        if($index==0){
            $datas = $result
                ->where('t_c_consultresponse.expertid',$expertid)
                ->where('view_consultstatus.configid' ,4)
                ->orderBy('t_c_consultresponse.id','desc')->paginate(6);
            $counts = DB::table('t_c_consultresponse as res')
                ->leftJoin('view_consultstatus as status','status.consultid','=','res.consultid')
                ->whereRaw('res.id in (select max(`t_c_consultresponse`.`id`) from `t_c_consultresponse` group by `t_c_consultresponse`.`consultid`,expertid)')
                ->where(['res.expertid' => $expertid,'status.configid' => 4])
                ->count();
        }else{
            if($configType=="不限"){
                $datas=$result ->where('t_c_consultresponse.expertid',$expertid)->where($typeWhere)->whereIn('view_consultstatus.configid',[5,6,7,8,9])->orderBy('t_c_consultresponse.id','desc')->paginate(6);
            }else{
                $datas=$result  ->where('t_c_consultresponse.expertid',$expertid)->where($typeWhere)->where($configTypeWhere)->orderBy('t_c_consultresponse.id','desc')->paginate(6);
            }
        }
        foreach ($datas as $data){
            $data->created_at=date("Y-m-d",strtotime($data->created_at));
            $data->starttime=date("m月d日 H:i",strtotime($data->starttime));
            $data->endtime=date("m月d日 H:i",strtotime($data->endtime));
            $totals=DB::table("t_c_consultresponse")->where("consultid",$data->consultid)->count();
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
        return view("myexpert.newMyAsk",compact("datas","type","counts",'type2','domains','configType','index'));
    }

    /**我的咨询的详情
     * @return mixed
     */
    public function askDetail($consultId){

        $consultStatus=DB::table("view_consultstatus")->where("consultid",$consultId)->pluck("configid");
        $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first();
        if(empty(session('userId')) || empty($expertid->expertid)){
            return redirect('login');
        }
        DB::table("t_c_consult")->where('consultid',$consultId)->update(['extislook' => 1]);
        /*$datas = DB::table('t_c_consult as consult')
            ->leftJoin('t_c_consultresponse as res','consult.consultid','=','res.consultid')
            ->leftJoin('t_u_enterprise as ent','consult.userid','=','ent.userid')
            ->leftJoin('view_consultstatus as status','status.consultid','=','res.consultid')
            ->where(['consult.consultid'=>$consultId,'res.expertid'=>$expertid->expertid])
            ->select('consult.*','res.*','status.*','consult.brief','')
            ->first();*/
        $datas=DB::table("t_c_consult")
            ->leftJoin('t_c_consultresponse as res','t_c_consult.consultid','=','res.consultid')
            ->leftJoin("t_c_consultverify","t_c_consultverify.consultid","=","t_c_consult.consultid")
            ->leftJoin('t_u_enterprise as ent','t_c_consult.userid','=','ent.userid')
            ->where(['t_c_consult.consultid'=>$consultId,'res.expertid'=>$expertid->expertid])
            ->whereRaw('t_c_consultverify.id in (select max(id) from t_c_consultverify group by consultid)')
            ->whereRaw('res.id in (select max(id) from t_c_consultresponse group by consultid,expertid)')
            ->select('t_c_consult.*','res.*','ent.*','t_c_consultverify.*','t_c_consult.brief')
            ->first();
        if(!empty($datas) && $datas->configid != 9 && $datas->state == 5){
            $selExperts=DB::table("t_u_enterprise")
                ->where('userid',$datas->userid)
                ->first();
            /*$selExperts2=DB::table("t_c_consultresponse")
                ->leftJoin("t_u_expert","t_c_consultresponse.consultid","=","t_u_expert.expertid")
                ->where("t_u_expert.expertid",$expertid->expertid)
                ->where("consultid",$consultId)
                ->first();*/
            $remark = DB::table('t_c_consultverify')
                ->where("t_c_consultverify.consultid",$consultId)
                ->whereRaw('t_c_consultverify.id in (select max(id) from t_c_consultverify group by consultid)')->first();
            return view("myexpert.askDetailFaild",compact('datas','selExperts','consultId','selExperts2','remark','expertid'));
        }
        switch ($consultStatus){
            case 4:
                $token = Crypt::encrypt(session('userId'));
                return view("myexpert.askDetail",compact('datas','token'));
                break;
            case 5:
                $token = Crypt::encrypt(session('userId'));
                return view("myexpert.askDetail",compact('datas','token'));
                break;
            case 6:
                $selExperts=DB::table("t_c_consult")
                    ->leftJoin("t_c_consultresponse","t_c_consultresponse.consultid","=","t_c_consult.consultid")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                $comperes=DB::table("t_u_bill")
                    ->leftJoin("t_u_user","t_u_user.userid","=","t_u_bill.userid")
                    ->where("t_u_bill.consultid",$consultId)
                    ->where("t_u_bill.type","支出")
                    ->get();
                return view("myexpert.askDetail06",compact('selExperts','comperes','datas',"consultId"));
                break;
            case 7:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_c_consultcomment","t_c_consultresponse.expertid","=","t_c_consultcomment.expertid" )
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                return view("myexpert.video7",compact('selExperts','comperes','datas',"consultId"));
                break;
            case 8:
                $selExperts=DB::table("t_c_consultresponse")
                    ->leftJoin("t_c_consultcomment","t_c_consultresponse.expertid","=","t_c_consultcomment.expertid" )
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("t_c_consultresponse.consultid",$consultId)
                    ->get();
                return view("myexpert.video7",compact('selExperts','comperes','datas',"consultId"));
                break;
            case 9:

                $selExperts=DB::table("t_u_enterprise")
                    ->where('userid',$datas->userid)
                    ->first();
                $selExperts2=DB::table("t_c_consultresponse")
                    ->leftJoin("t_u_expert","t_c_consultresponse.expertid","=","t_u_expert.expertid")
                    ->where("t_c_consultresponse.state",3)
                    ->where("consultid",$consultId)
                    ->get();
                return view("myexpert.video9",compact('selExperts','comperes','datas',"consultId",'selExperts2'));
                break;

        }

    }

    /**专家响应咨询
     * @param Request $request
     * @return array
     */
    public function responseConsult (Request $request) {
        if($request->ajax()){
            $data = $request->input();
            //对token进行验证
                $startTime=DB::table('t_c_consult')->where('consultid',$data['consultid'])->pluck('starttime');
                $endTime=DB::table('t_c_consult')->where('consultid',$data['consultid'])->pluck('endtime');
                //获取到该用户对应的专家的id
                $expertid = DB::table('t_u_expert')->where('userid',session('userId'))->first()->expertid;
                $workIngConsults=DB::table('view_expertresponsetime')
                    ->where("view_expertresponsetime.expertid",$expertid)
                    ->whereRaw('(starttime between  "'.$startTime.'" and "'.$endTime.'" or endtime between "'.$startTime .'" and "'.$endTime .'" or starttime < "'.$startTime.'" and endtime > "'.$endTime.'") and state in (2,3)')
                    ->count();
                if(!$workIngConsults){
                    DB::beginTransaction();
                    try{
                        //查询是否存在响应的情况
                        $verify = DB::table('t_c_consultresponse')->where(['expertid' => $expertid,'consultid' => $data['consultid'],'state' => 2])->first();
                        if(!$verify){
                            $res=DB::table('t_c_consultresponse')->insert([
                                'expertid' => $expertid,
                                'consultid' => $data['consultid'],
                                'state' => 2,
                                'responsetime' => date('Y-m-d H-:i:s',time()),
                                'updated_at' => date('Y-m-d H-:i:s',time())
                            ]);
                            if($res){
                                $name=DB::table("t_u_expert")->where("expertid",$expertid)->pluck('expertname');
                                $phone=DB::table('t_c_consult')
                                    ->leftJoin('t_u_user','t_c_consult.userid','=','t_u_user.userid')
                                    ->where('consultid',$data['consultid'])
                                    ->pluck('phone');
                                $time=DB::table('t_c_consult')->where('consultid',$data['consultid'])->pluck('created_at');
                                $this->_sendSms($phone,'视频咨询','answer',$name,$time);
                            }
                        }else {
                            return ['msg' => '您已经响应过此咨询事件','icon' => 2];
                        }
                        //查询咨询是否已响应
                        $verify2 = DB::table('t_c_consultverify')->where(['consultid' => $data['consultid'],'configid' => 5])->first();
                        if(!$verify2){
                            DB::table('t_c_consultverify')->insert([
                                'consultid' => $data['consultid'],
                                'configid' => 5,
                                'verifytime' =>  date('Y-m-d H-:i:s',time()),
                                'updated_at' => date('Y-m-d H-:i:s',time())
                            ]);
                        }
                        DB::commit();
                        return ['msg' => '响应成功,等待回应','icon' => 1];
                    }catch(Exception $e)
                    {
                        DB::rollBack();
                        return ['msg' => '处理失败','icon' => 2];
                    }
                }
                return ['msg' => '你在这个时间段已经有其他的咨询了,请合理安排时间','icon' => 2];

            return ['msg' => '非法操作FF000002','icon' => 2];
        }
        return ['msg' => '非法操作FF000001','icon' => 2];
    }
    /**进入视频会议
     * @return mixed
     */
    public function myaskinvt(){
        return view("myexpert.myaskinvt");
    }

   /**收费标准
     * @return mixed
     */
    public function standard(Request $request){
        $result = DB::table('t_u_expert')
            ->leftJoin("T_U_EXPERTVERIFY","T_U_EXPERT.EXPERTID","=","T_U_EXPERTVERIFY.expertid")
            ->where('userid',session('userId'))
            ->whereIn('configid',[1,2,3])
            ->whereRaw('T_U_EXPERTVERIFY.id in (select max(id) from T_U_EXPERTVERIFY group by expertid)')
            ->first();
        if($result){
            $expertid=$result->expertid;
        }else{
            return redirect('/uct_expert');
        }
        $islinemeet = $result->islinemeet;
        $isonlinemeet = $result->isonlinemeet;
        $fee =DB::table('t_u_expertfee')->where('expertid',$expertid)->orderBy('expertid', 'desc')->first();
        if($fee){
            $linefee=$fee->linefee;
        }else{
            $linefee=0;
        }
        if($fee){
            $fee=$fee->fee;
        }else{
            $fee=0;
        }

        if($request->ajax()){
            $data = $request->input();
            $islinemeet = DB::table('t_u_expert')->where('expertid',$expertid)->first()->islinemeet;
            $isonlinemeet = DB::table('t_u_expert')->where('expertid',$expertid)->first()->isonlinemeet;
            $linefee = DB::table('t_u_expertfee')->where('expertid',$expertid)->first()->linefee;
            $fee = DB::table('t_u_expertfee')->where('expertid',$expertid)->first()->fee;

            if($data['islinemeet'] != $islinemeet){
                DB::table('t_u_expert')->where('expertid',$expertid)->update(['islinemeet' => $data['islinemeet']]);
            }
            if($data['isonlinemeet'] != $isonlinemeet){
                DB::table('t_u_expert')->where('expertid',$expertid)->update(['isonlinemeet' => $data['isonlinemeet']]);
            }
            $count= DB::table('t_u_expertfee')->where('expertid',$expertid)->count();
            /*if($data['fee']==0){
                $state=0;
            }else{
                $state=1;
            }*/
            if($data['linefee'] != $linefee || $data['fee'] != $fee) {
                if ($count) {
                    $result = DB::table('t_u_expertfee')
                        ->where('expertid', $expertid)
                        ->update([
                            'fee' => $data['fee'],
                            'linefee' => $data['linefee']
                        ]);
                } else {
                    $result = DB::table('t_u_expertfee')->insert([
                        "expertid" => $expertid,
                        "fee" => $data['fee'],
                        'linefee' => $data['linefee'],
                        "created_at" => date("Y-m-d H:i:s", time()),
                        "updated_at" => date("Y-m-d H:i:s", time())
                    ]);

                }
            }

            if($result){
                return ['msg' => '设置成功','icon' => 1];
            }else{
                return ['msg' => '设置失败','icon' => 2];

            }
        }
        //dd($fee);
        return view("myexpert.standard",compact('linefee','fee','islinemeet','isonlinemeet'));
    }

    /**我的办事视频
     * @param $eventId
     * @return mixed
     */
    public function myEventVideo($eventId){
        return view('myenterprise.myEeventVideo',compact('eventId'));
    }

    /**专家认证修改
     * @param $expertId
     * @return mixed
     */
    public  function  updateExpert($expertId){
        $result=DB::table("t_u_expert")->where("expertid",$expertId)->first();
        $cate = DB::table('t_common_domaintype')->get();
        return view("myexpert.updateExpert",compact('result','cate'));
    }

    public function lineMeetExpert()
    {
        $userId=session('userId');

        $type = isset($_GET['type'])?$_GET['type']:"不限";

        $linemeetconfigtype = array("已提交"=>1,"未通过"=>2,"已通过"=>3,"已完成"=>4);

        $typeWhere=($type!="不限")?array("t_l_linemeetverify.configid"=>$linemeetconfigtype[$type]):array();


        $results = DB::table('t_u_expert')
            ->leftJoin('t_u_expertverify','t_u_expert.expertid','=','t_u_expertverify.expertid')
            ->where('t_u_expert.userid',$userId)
            ->where('t_u_expertverify.configid',2)
            ->first();
        if(!$results){
            $expertdata = [0,000];
            if(!isset($_GET['index']) || $_GET['index']==0){
                $index=0;
            }else{
                $index=1;
            }
            $result =DB::table("t_l_linemeet")
                ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
                ->leftJoin("t_u_expert","t_u_expert.expertid","=","t_l_linemeet.expertid")
                ->leftJoin("t_u_enterprise","t_l_linemeet.userid","=","t_u_enterprise.userid")
                ->select("t_l_linemeet.*",'t_l_linemeetverify.configid','t_u_expert.expertname','t_u_expert.domain2','t_u_enterprise.enterprisename','t_u_enterprise.industry')
                ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
                ->whereIn('t_l_linemeet.expertid', $expertdata)
                ->where('t_l_linemeetverify.configid',2)
                ->orderBy('t_l_linemeet.puttime','desc');
            $count=clone $result;
            $counts = $result->count();
            $datas = $result->paginate(6);
            return view("myexpert.lineMeetExpert",compact('datas','index','counts'));

        }
        $expertid = $results->expertid;

        if(!isset($_GET['index']) || $_GET['index']==0){
            $typeWhere=[];
            $configTypeWhere=[];
            $index=0;

            $result =DB::table("t_l_linemeet")
                ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
                ->leftJoin("t_u_expert","t_u_expert.expertid","=","t_l_linemeet.expertid")
                ->leftJoin("t_u_enterprise","t_l_linemeet.userid","=","t_u_enterprise.userid")
                ->select("t_l_linemeet.*",'t_l_linemeetverify.configid','t_u_expert.expertname','t_u_expert.domain2','t_u_enterprise.enterprisename','t_u_enterprise.industry')
                ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
                ->where("t_l_linemeet.expertid",$expertid)
                ->where($typeWhere)
                ->where('t_l_linemeetverify.configid',2)
                ->orderBy('t_l_linemeet.puttime','desc');
        }else{
            $typeWhere=[];
            $configTypeWhere=[];
            $index=1;
            $result =DB::table("t_l_linemeet")
                ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
                ->leftJoin("t_u_expert","t_u_expert.expertid","=","t_l_linemeet.expertid")
                ->leftJoin("t_u_enterprise","t_l_linemeet.userid","=","t_u_enterprise.userid")
                ->select("t_l_linemeet.*",'t_l_linemeetverify.configid','t_u_expert.expertname','t_u_expert.domain2','t_u_enterprise.enterprisename','t_u_enterprise.industry')
                ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
                ->where("t_l_linemeet.expertid",$expertid)
                ->where($typeWhere)
                ->whereIn('t_l_linemeetverify.configid',[3,4])
                ->orderBy('t_l_linemeet.puttime','desc');
        }

        $count=clone $result;
        $counts = $result->count();
        $datas = $result->paginate(6);



        return view("myexpert.lineMeetExpert",compact("datas","type","counts",'index'));
    }


    //接受企业的先下约谈请求
    public function requestLineMeet()
    {
        $linemeetid = $_POST['id'];
        $result = DB::table('t_l_linemeetverify')->insert([
            "meetid"=>$linemeetid,
            "configid"=>3,
            "verifytime"=> date("Y-m-d H:i:s", time()),
            "created_at"=> date("Y-m-d H:i:s", time()),
            "updated_at"=> date("Y-m-d H:i:s", time()),

        ]);

        if(!$result){
            return  ['icon'=>'2','msg'=>"接收失败"];
        }else{
            return  ['icon'=>'1','msg'=>"接收成功"];

        }

    }

    /**
     *
     */
    public function lineMeetDetail($linemeetid)
    {
        /*$configid = DB::table('t_l_linemeetverify')->where('meetid',$linemeetid)->first()->configid;

        if($configid == '1'){*/

            $datas = DB::table('t_l_linemeet')
                ->leftJoin('t_u_expert','t_u_expert.expertid',"=",'t_l_linemeet.expertid')
                ->leftJoin('t_u_enterprise','t_u_enterprise.userid',"=",'t_l_linemeet.userid')
                ->leftJoin('t_u_expertfee','t_u_expertfee.expertid',"=",'t_l_linemeet.expertid')
                ->leftJoin("t_l_linemeetverify","t_l_linemeetverify.meetid","=","t_l_linemeet.id")
                //->leftJoin("t_u_memberright", "t_u_memberright.memberid", "=", "t_u_enterprisemember.memberid")
                ->whereRaw('t_l_linemeetverify.id in (select max(id) from t_l_linemeetverify group by meetid)')
                ->where('t_l_linemeet.id',$linemeetid)
                ->select('t_l_linemeet.*','t_u_expert.expertname','t_u_expert.showimage','t_u_expertfee.linefee','t_u_enterprise.*','t_l_linemeetverify.configid')
                ->first();
            $memberrights=DB::table("t_u_memberright")->whereNotIn("memberid",[1,5])->get();

            if($datas->configid == 2){
                return view("myexpert.lineMeetDetail",compact('datas','memberrights'));
            }elseif ($datas->configid == 3){
                return view("myexpert.lineMeetDetail2",compact('datas','memberrights'));
            }elseif ($datas->configid == 4){
                return view("myexpert.lineMeetDetail3",compact('datas','memberrights'));
            }


    }
}
