<?php
/**
 * Created by PhpStorm.
 * User: LK
 * Date: 2019/11/1
 * Time: 14:19
 */
namespace App\Http\Controllers\Code;
use App\Models\User;
use App\Models\Users;
use App\Models\Order;
use App\Models\Czinfo;
use App\Models\Czrecord;
use App\Models\Notice;
use App\Models\UserAccount;
use App\Models\UserBill;
use App\Models\Verificat;
use App\Models\Withdraw;
use App\Models\Adminvesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class MycenterController extends CommonController {
    /*
     * 昵称修改
     */
    public function change_nickname(Request $request){
        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $newname = htmlformat($_POST['nickname']);
            $is=Users::where("nickname",$newname)->whereNotIn("user_id",[$user_id])->exists();
            if(!$is){
                $name=Users::where("user_id",$user_id)->update(array("nickname"=>$newname));
                if($name!==false){
                    $k="UserInfo_".$user_id;
                    $old=Redis::get($k);
                    $data=json_decode($old,true);
                    $data["NickName"]=$newname;

                    $new=json_encode($data);
                    Redis::set($k,$new);

                    ajaxReturn('','修改成功!',1);
                }else{
                    ajaxReturn('','昵称不可重复!',0);
                }
            }else{
                ajaxReturn('','昵称不可重复!',0);
            }
        }else{
            ajaxReturn('','请求异常!',0);
        }
    }

    /*
     * 在线充值
     */
    public function chongzhi(Request $request) {
        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $username = htmlformat($_POST['name']);
            //姓名
            $score = (int)($_POST['money']*100);
            $admin_kefu_id = (int)($_POST['kefu']);
            //金额
            $sk_name = htmlformat($_POST['sk_name']);
            //收款银行姓名
            $sk_bankname = htmlformat($_POST['sk_bankname']);
            // 收款银行名称
            $sk_banknum = htmlformat($_POST['sk_banknum']);
            //收款银行卡号
            $code=time().rand(100000,999999);
            Redis::del("hq_app_recharge".$user_id);
            //随机锁入队
            Redis::rPush('hq_app_recharge'.$user_id,$code);
            //随机锁出队
            $codes=Redis::LINDEX("hq_app_recharge".$user_id,0);
            if ($code != $codes) {
                Redis::del("hq_app_recharge".$user_id);
                ajaxReturn(null, "数据信息异常",0);
            }
            if ($score<=0) {
                Redis::del("hq_app_recharge".$user_id);
                ajaxReturn(null, "金额不能小于0",0);
            }
            if (empty($username)  ||  empty($score) ||empty($sk_name) ||empty($sk_bankname) || empty($sk_banknum)) {
                Redis::del("hq_app_recharge".$user_id);
                ajaxReturn(null, "数据信息异常",0);
            }
            $czrecordinfo=Czrecord::where(array("user_id"=>$user_id,"status"=>0))->first();
            if (!empty($czrecordinfo)) {
                Redis::del("hq_app_recharge".$user_id);
                ajaxReturn(null, "当前有订单未审核",0);
            }
            //限定格式为jpg,jpeg,png
            $fileTypes = ['jpeg', 'jpg','png'];
            if ($request->hasFile('uploadfile')) {
                $file=$request->file('uploadfile');
                if ($file->isValid()) { //判断文件上传是否有效
                    $FileType = $file->getClientOriginalExtension(); //获取文件后缀
                    file_put_contents('./FileType.txt',$FileType.PHP_EOL,FILE_APPEND);
                    if (!in_array($FileType, $fileTypes)) {
                        ajaxReturn("","图片格式为jpg,png,jpeg",0);
                    }
                    $FilePath = $file->getRealPath(); //获取文件临时存放位置
                    file_put_contents('./FileType.txt',$FilePath.PHP_EOL,FILE_APPEND);
                    $FileName = date('YmdHis') . uniqid() . '.' . $FileType; //定义文件名
                    file_put_contents('./FileType.txt',$FileName.PHP_EOL,FILE_APPEND);
                    Storage::disk('recharge')->put($FileName, file_get_contents($FilePath)); //存储文件
                }
                $data =array(
                    'user_id'=>$user_id,
                    'admin_kefu_id'=>$admin_kefu_id,
                    'name'=>$username,
                    'score'=>$score,
                    'czimg'=>"/recharge/" . $FileName,
                    'status'=>0,
                    'sk_name'=>$sk_name,
                    'sk_bankname'=>$sk_bankname,
                    'sk_banknum'=>$sk_banknum,
                    'creatime'=>time()
                );
                file_put_contents('./FileType.txt',print_r($data,true).PHP_EOL,FILE_APPEND);
                $status = Czrecord::insert($data);
                if ($status) {
                    Redis::del("hq_app_recharge".$user_id);
                    ajaxReturn("", "上传成功");
                } else {
                    Redis::del("hq_app_recharge".$user_id);
                    ajaxReturn("", "上传失败!", 0);
                }

            } else {
                Redis::del("hq_app_recharge".$user_id);
                ajaxReturn("","未上传图片!",0);
            }
        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }

    /*
     * 充值记录
     */
    public function recharge_list(Request $request){
        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $lastid = $request->input('lastid');
            $date=$request->input('date');

            $start=strtotime($date);
            $end=$start+86400-1;

            if($lastid){
                $where =[['user_id',$user_id],['id','<',$lastid]];
            }else{
                $where =array('user_id'=>$user_id);
            }
            $czrecord=Czrecord::where($where)->whereBetween('creatime',[$start,$end])->orderBy('creatime','desc')->limit(5)->get();
            foreach ($czrecord as &$v) {
                $v['date']= date('Y-m-d',$v['creatime']);
                $v['time']= date('H:i:s',$v['creatime']);
                $v['score']=  $v['score']/100;
                unset($v['creatime']);
                unset($v['savetime']);
            }
            $data['list']=$czrecord;
            ajaxReturn($data, "充值记录");
        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }
    
    /*
   * 提现申请
   */
    public function draw(Request $request) {

        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $draw_name = htmlformat($_POST['draw_name']); //姓名
            $draw_money = $_POST['draw_money'];//金额
            $bank_name = htmlformat($_POST['bank_name']);// 收款银行名称
            $bank_card = htmlformat($_POST['bank_card']); //收款银行卡号
            $draw_pwd=htmlformat($_POST['draw_pwd']); //提现密码

            $min=Redis::get("hq_admin_draw_min");
            $max=Redis::get("hq_admin_draw_max");
            $fee=Redis::get("hq_admin_draw_fee");

            $start=strtotime(date('Ymd'));
            $end=strtotime("+1day",$start)-1;

            $tablepfe=date('Ymd');
            $userbill =new UserBill();
            $userbill->setTable('user_billflow_'.$tablepfe);

            $code=time().rand(100000,999999);
            Redis::del("hq_app_draw".$user_id);
            //随机锁入队
            Redis::rPush('hq_app_draw'.$user_id,$code);
            //随机锁出队
            $codes=Redis::LINDEX("hq_app_draw".$user_id,0);

            if ($code != $codes) {
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "请勿频繁操作！",0);
            }
            if ($draw_money<=0) {
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "提现金额不能小于0！",0);
            }
            if ($draw_money<$min ||$draw_money>$max){
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "提现金额不能小于100或大于5000！",0);
            }
            if (empty($draw_name)  ||  empty($draw_money) ||empty($bank_name) || empty($bank_card)||empty($draw_pwd) ){
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "数据信息异常！",0);
            }
            $pwd=Users::where("user_id",$user_id)->value("draw_pwd");
            if(md5($draw_pwd)!=$pwd){
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "提现密码不对！",0);
            }
            $is=Users::where(array("user_id"=>$user_id,"draw_name"=>$draw_name,"bank_name"=>$bank_name,"bank_card"=>$bank_card))->first();
            if(!$is){
                Redis::del("hq_app_draw".$user_id);
                ajaxReturn(null, "银行卡信息有误！",0);
            }

            $score=$draw_money*100;
            $count=Withdraw::where(array("user_id"=>$user_id,"status"=>1))->whereBetween('creatime',[$start,$end])->count();
            if($count==0){
                $tradeMoney=$score;
            }else if($count>=1){
                $tradeMoney = $draw_money - $draw_money*$fee/100; //到账金额
            }
            DB::beginTransaction();
            try{
                $before=UserAccount::where("user_id",$user_id)->value("balance");
                $balance=UserAccount::where("user_id",$user_id)->decrement('balance',$score);
                if(!$balance){
                    DB::rollBack();
                    Redis::del("hq_app_draw".$user_id);
                    return ['msg'=>'提现失败！','status'=>0];
                }
                $after=UserAccount::where("user_id",$user_id)->value("balance");
                $order_sn=getrequestId();
                $draw=[
                    "user_id"=>$user_id,
                    "order_sn"=>$order_sn,
                    "money"=>$score,
                    "tradeMoney"=>$tradeMoney,
                    "draw_name"=>$draw_name,
                    "bank_name"=>$bank_name,
                    "bank_card"=>$bank_card,
                    "status"=>0,
                    "remark"=>"用户提现申请",
                    "creatime"=>time()
                ];
                $with_draw=Withdraw::insert($draw);
                if(!$with_draw){
                    DB::rollBack();
                    Redis::del("hq_app_draw".$user_id);
                    return ['msg'=>'申请失败！','status'=>0];
                }

                $bill=[
                    "user_id"=>$user_id,
                    "order_sn"=>$order_sn,
                    "score"=>-$score,
                    "bet_before"=>$before,
                    "bet_after"=>$after,
                    "status"=>3,
                    "game_type"=>0,
                    "remark"=>"用户提现申请",
                    "creatime"=>time()
                ];
                $insert=$userbill->insert($bill);
                if(!$insert){
                    DB::rollBack();
                    Redis::del("hq_app_draw".$user_id);
                    return ['msg'=>'流水更新失败！','status'=>0];
                }
                DB::commit();
                Redis::del("hq_app_draw".$user_id);
                return ['msg'=>'申请成功！','status'=>1];
            }catch (Exception $e){
                DB::rollBack();
                Redis::del("hq_app_draw".$user_id);
                return ['msg'=>'数据异常！','status'=>0];
            }

        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }

    /**
     * 提现记录
     */
    public function withdraw_list(Request $request) {
        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $lastid = $request->input('lastid');
            $date=$request->input('date');

            $start=strtotime($date);
            $end=$start+86400-1;

            if($lastid){
                $where =[['user_id',$user_id],['id','<',$lastid]];
            }else{
                $where =array('user_id'=>$user_id);
            }
            $list = Withdraw::where($where)->whereBetween('creatime',[$start,$end])->orderBy('id','desc')->limit(5)->get();
            foreach ($list as $k=>&$v) {
                $v['money']=$v['money']/100;
                $v['date']= date('Y-m-d',$v['creatime']);
                $v['time']= date('H:i:s',$v['creatime']);
                $v['score']=  $v['score']/100;
                unset($v['creatime']);
                unset($v['endtime']);
            }
            $data['list']=$list;
            ajaxReturn($data,'提现记录!',1);
        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }


    /**
     * 下注记录
     */
    public function bet_list(Request $request){
        if($request->isMethod('post')) {
            $user_id = $this->uid;
            $lastid = $request->input('lastid');
            $date=$request->input('date');

            $time = strtotime($date);
            $weeksuf = date('Ymd',$time);

            $order=new Order;
            $order->setTable('order_'.$weeksuf);
            $sql=$order->orderBy('creatime','desc');

            if($lastid){
                $where =[['user_id',$user_id],['id','<',$lastid]];
            }else{
                $where =array('user_id'=>$user_id);
            }
            $list = $sql->where($where)->limit(5)->get()->toArray();
            $sum =$sql->where('status',1)->sum('get_money');
            foreach ($list as $k=>&$v) {
                $v['date']= date('Y-m-d',$v['creatime']);
                $v['time']= date('H:i:s',$v['creatime']);
                $v['bet_money']=json_decode($v["bet_money"],true);
                unset($v['user_id']);
                unset($v['record_sn']);
                unset($v['desk_id']);
                unset($v['creatime']);
                unset($v['paytime']);
            }
            $wash="10078";
            $data["wash"]=$wash;
            $data["sum"]=$sum;
            $data['list']=$list;
            ajaxReturn($data,'下注记录!',1);
        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }

    /**
     * 检测更新
     */

    public function update(){

        $v=$_POST['version_no'];

        //hq
        $data['strongerUrl'] = 'http://www.baidu.com';
        $zxinfo =Adminvesion::where('is_open',1)->orderBy('creatime','desc')->first();
        $version_no =$zxinfo['version_no'];
        $data['detail'] =$zxinfo['detail'];
        /**
         * force 0 非强制更新 1 强制更新 2不更新
         */
        if($v!=$version_no){
            $data['isStronger']=$zxinfo['force'];
            $data["serverClientVersion"]=$version_no;
            ajaxReturn($data,'版本更新',1);
        }
    }


    /**
     * 获取公告详情
     */
    public function getnotice(Request $request) {
        if($request->isMethod('post')) {
            $messageinfo =Notice::orderBy('creattime','desc')->get();
            foreach ($messageinfo as $k=>&$v) {
                $v['creattime']= date('Y/m/d H:i:s',$v['creattime']);
            }
            $data =array(
                'messageinfo'=>$messageinfo
            );
            ajaxReturn($data,'请求成功!',1);
        } else {
            ajaxReturn('','请求数据异常!',0);
        }
    }


    /**历史资金列表查询时间控制
     * @param Request $request
     */
    public function getdatetime(Request $request){
        if($request->isMethod('post')) {
            $data =array(
                'Y'=>2020,
                'M'=>1,
                'D'=>20,
            );
            ajaxReturn($data,'请求成功!',1);
        }else {
            ajaxReturn('','请求数据异常!',0);
        }

    }


    /**生成随机码
     * @param $nums
     * @param $num
     * @return string
     */
    private  function generateCode($nums,$num) {
        $strs="abcdefghjkmnpqrstuvwxyz";
        $str="123456789";
        $keys = "";
        for ($t=0;$t<$nums;$t++) {
            $keys .= $strs {
            mt_rand(0,22)
            }
            ;
        }
        $key = "";
        for ($i=0;$i<31;$i++) {
            $key .= $str {
            mt_rand(0,8)
            }
            ;
        }
        $time  = substr($this->getMillisecond(), 10,3);
        $key = substr($key,3,$num);
        $res = $keys.$key.$time;
        $info = Imsi::where(array('code'=>$res))->first();
        if(!empty($info)) {
            return $this->generateCode($nums,$num);
        } else {
            return $res;
        }
    }
    /**生成毫秒级时间戳
     * @return float
     */
    private function getMillisecond() {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
    }
    /**
     * 验证手机验证码
     */
    private function verifycat($mobile,$key,$code) {
        if(!isMobile($mobile)) {
            ajaxReturn('','手机号码格式错误!',0);
        }
        $Cachecode=Redis::get($key.$mobile);
        if($code==$Cachecode) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 发送验证码
     */
    private function csendcode($mobile,$key,$type,$ip) {
        if(!isMobile($mobile)) {
            ajaxReturn('','手机号码格式错误!',0);
        }
        $code=rand_string(6,1);
        //todo 发送短信
        $res=Verificat::yxtsend($mobile,$code,$ip,$key);
        if($res==0) {
            Verificat::insertsendcode($code,$mobile,$type,$ip,1,'发送成功!');
            ajaxReturn('','发送成功!',1);
        } elseif($res==10001) {
            Verificat::insertsendcode($code,$mobile,$type,$ip,0,'请勿频繁发送!');
            ajaxReturn('faild','请勿频繁发送!',0);
        } else {
            Verificat::insertsendcode($code,$mobile,$type,$ip,0,$res);
            ajaxReturn('faild','发送失败',0);
        }
    }

}