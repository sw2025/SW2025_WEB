<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use PhpSpec\Exception\Exception;

class CenterController extends Controller
{
    /**基本资料
     * @return mixed
     */
    public function index(){
        $userId=session("userId");
        $data=DB::table("T_U_USER")->where("userid",2)->first();
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
    public function  changeTel2(){
        return view("ucenter.changeTel2");
    }

    /**
     * 充值提现
     * @return mixed
     */
    public function recharge(){
        $userId=session("userId");
        $incomes=DB::table("T_U_BILL")->where(["userid"=>$userId,"type"=>"收入"])->sum("money");
        $pays=DB::table("T_U_BILL")->where(["userid"=>$userId,"type"=>"支出"])->sum("money");
        $expends=DB::table("T_U_BILL")->where(["userid"=>$userId,"type"=>"在途"])->sum("money");
        $balance=$incomes-$pays-$expends;
        return view("ucenter.recharge",compact("incomes","pays","expends","balance"));
    }

    /**充值
     * @return mixed
     */
    public function rechargeMoney(){
        return view("ucenter.rechargeMoney");
    }

    /**提现
     * @return mixed
     */
    public function cash(){
        return view("ucenter.cash");
    }

    /**我的信息
     * @return mixed
     */
    public  function  myinfo(){
        $userId=session("userId");
       
        return view("ucenter.myinfo");
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
            ->leftJoin('t_n_collectneed as colneed','colneed.needid' ,'=' ,'need.needid')
            ->select('need.*','ent.enterprisename','ent.showimage as entimg','coll.count as collcount','mess.count as messcount','ext.showimage as extimg','ext.expertname');
        //获得用户的收藏
        $collectids = [];
        if(session('userId')){
            $collectids = DB::table('t_n_collectneed')->where(['userid' => session('userId'),'remark' => 1])->lists('needid');
        }
        //用户发布的数量
        $putcount = DB::table('t_n_need as need')->where('userid',session('userId'))->count();
        //用户回复的数量
        $msgcount = count(DB::table('t_n_messagetoneed as need')->where('userid',session('userId'))->groupBy('needid')->lists('needid'));
        //判断是否为http请求
        if(!empty($get = $request->input())){
            //获取到get中的数据并处理
            $searchname=(isset($get['searchname']) && $get['searchname'] != "null") ? $get['searchname'] : null;
            $role=(isset($get['role']) && $get['role'] != "null") ? $get['role'] : null;
            $supply=(isset($get['supply']) && $get['supply'] != "null") ? explode('/',$get['supply']) : null;
            $address=(isset($get['address']) && $get['address'] != "null") ? $get['address'] : null;
            $ordertime=( isset($get['ordertime']) && $get['ordertime'] != "null") ? $get['ordertime'] : null;
            $ordercollect=( isset($get['ordercollect']) && $get['ordercollect'] != "null") ? $get['ordercollect'] : null;
            $ordermessage=( isset($get['ordermessage']) && $get['ordermessage'] != "null") ? $get['ordermessage'] : null;
            $action = empty($get['action']) ? null : $get['action'];
            //设置where条件生成where数组
            $rolewhere = !empty($role)?array("needtype"=>$role):array();
            $supplywhere = !empty($supply)?array("need.domain1"=>$supply[0],'need.domain2' => $supply[1]):array();
            $addresswhere = !empty($address)?array("ent.address"=>$address):array();
            $obj = $datas->where($rolewhere)->where($supplywhere)->where($addresswhere);
            //判断是否有搜索的关键字
            if(!empty($searchname)){
                $obj = $obj->where("need.brief","like","%".$searchname."%");
            }
            if(!empty($action)){
                switch($action){
                    case 'collect':
                        $obj = $obj->where('colneed.userid',session('userId'))->where('colneed.remark',1);
                        break;
                    case 'myput':
                        $obj = $obj->where('need.userid',session('userId'))->whereRaw('need.needid in (select needid from t_n_needverify  where configid=3 group by needid order by id desc)');
                        break;
                    case 'message':
                        $obj = $obj->whereRaw('need.needid in (select  needid from t_n_messagetoneed  where userid='.session('userId').' group by needid)');
                        break;
                    case 'waitverify':
                        $obj = $obj->where('need.userid',session('userId'))->whereRaw('need.needid in (select needid from t_n_needverify  where configid=1 group by needid order by id desc)');
                        break;
                    case 'refuseverify':
                        $obj = $obj->where('need.userid',session('userId'))->whereRaw('need.needid in (select needid from t_n_needverify  where configid=2 group by needid order by id desc)');
                        break;
                }
            }
            //对三种排序进行判断
            if(!empty($ordertime)){
                $obj = $obj->orderBy('need.needtime',$ordertime);
            } elseif(!empty($ordercollect)){
                $obj = $obj->orderBy('coll.count',$ordercollect);
            } else {
                $obj = $obj->orderBy('mess.count',$ordermessage);
            }
            $datas = $obj->paginate(4);
            return view("ucenter.myNeed",compact('cate','searchname','msgcount','datas','role','action','collectids','putcount','supply','address','ordertime','ordercollect','ordermessage'));
        }
        $datas = $datas->whereRaw('need.needid in (select needid from t_n_needverify  where configid=3 group by needid order by id desc)')
            ->orderBy("need.needtime",'desc')
            ->paginate(4);
        $ordertime = 'desc';
        return view("ucenter.myNeed",compact('cate','datas','ordertime','collectids','putcount','msgcount'));
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
        //取出同类下推荐的供求
        $info = ['domain1' => $datas->domain1,'domain2' =>$datas->domain2,'needid' => $datas->needid];
        $recommendNeed = $obj->where('needid','<>',$info['needid'])->orderBy('needtime','desc');
        $obj2 = clone $recommendNeed;
        //取出相同二级类下面的供求
        $recommendNeed = $recommendNeed->where(['need.domain2' => $info['domain2'],'need.domain1' => $info['domain1']])->take(5)->get();
        //不足5条时 在一级类下面查找供求
        if(count($recommendNeed) < 5){
            $commedomain1 = $obj2->where('need.domain1',$info['domain1'])->where('need.domain2','<>',$info['domain2'])->take(5-count($recommendNeed))->get();
            $recommendNeed = array_merge($recommendNeed,$commedomain1);
        }
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
        return view("ucenter.needDetail",compact('datas','recommendNeed','message','configid','collectids','msgcount','collcount','cryptid'));
    }

    /**
     * 解决需求
     */
    public function solveNeed (Request $request){
        if($request->ajax()){
            $data = $request->input();
            $supplyid = $data['supplyid'];
            $mdid = $data['mdid'];
            if(session('userId').$supplyid == Crypt::decrypt($mdid)){
                $res = DB::table('t_n_need')->where('userid',session('userId'))->where('needid',$supplyid)->first();
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
    public function  supplyNeed(){
        return view("ucenter.supplyNeed");
    }
    /**个人中心获取验证码
     * @return array
     */
    public function  getcodes(){
        $res=array();
        $userId=$_POST['userId'];
        $phone = DB::table("T_U_USER")->where("userid",$userId)->pluck("phone");
        $action =$_POST['action'];
        switch ($action){
            case "registr":
                $user = User::where('phonenumber', $phone)->first();
                if($user) {
                    $res['code']="phone";
                    $res['msg']="该手机号已经注册!";
                    return $res;
                }
                break;
            case "forget":
                $user = User::where('phonenumber', $phone)->first();
                if(!$user) {
                    $res['code']="phone";
                    $res['msg']="该手机号不存在!";
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
        if(Cache::has($phone)){
            $smsCode=Cache::get($phone);
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
        $avatar=!empty($_POST['myAvatar'])?$_POST['myAvatar']:"avatar.jpg";
        $userId=$_POST["userId"];
        $res=array();
        $result=DB::table("T_U_USER")->where("userid",$userId)->update([
            "nickname"=>$nickName,
            "avatar"=>$avatar,
            "updated_at"=>date("Y-m-d H:i:s",time())
        ]);
        if($result){
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
    public  function getRecord(){
        $type=$_POST['type'];
        $startPage=isset($_POST['startPage'])?$_POST['startPage']:1;
        $offset=($startPage-1)*2;
        $userId=session("userId");
        $result=array();
        $counts=DB::table("T_U_BILL")->where("userid",2)->where("type",$type)->count();
        $counts=!empty(ceil($counts/2))?ceil($counts/2):0;
        $datas=DB::table("T_U_BILL")->select("brief","payno","money","created_at")->where("userid",2)->where("type",$type)->skip($offset)->take(2)->get(2);
        foreach ($datas as $data){
            $data->created_at=date("Y-m-d",strtotime($data->created_at));
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
    
    

    
   

}
