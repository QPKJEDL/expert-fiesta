<?php

namespace App\Http\Controllers\Code;

use App\Http\Controllers\Controller;
use App\Models\Imsi;
use App\Models\User;
use App\Models\Users;
use App\Models\Userscount;
use App\Models\Verificat;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /**
     * 码商登录
     */
    public function login(Request $request){

        $account=$_POST['account'];
        $password=htmlspecialchars($_POST['password']);
        //dump($account);
        //dump($password);
        //判断用户名密码
        if($account!=""&&$password!=""){
            //查询用户信息
            $userinfo=Users::where(array("account"=>$account,"password"=>md5($password)))->first();
            if($userinfo){
                if ($userinfo['is_over'] == 1){
                    ajaxReturn(null,'此账号已被封禁!',0);
                }
                $ip =$request->ip();
                $token=md5(rand_string(6,1));
                Users::where(array("account"=>$account))->update(array("token"=>$token,"last_ip"=>$ip));
                $userinfo['token']=$token;
                ajaxReturn($userinfo,"登录成功");
            }else{
                ajaxReturn(null,'用户名或密码错误',0);
            }
        }else{
            ajaxReturn(null,'用户名或密码不能为空',0);
        }

    }

    /**
     * 手机验证码登陆
     */
    public function mobilelogin(Request $request){
        $mobile=(int)$_POST['mobile'];
        $code=(int)$_POST['code'];

        if(!isMobile($mobile)){
            ajaxReturn(null,'手机号码格式错误！',0);
        }
        if (empty($code)){
            ajaxReturn(null,'验证码为空！',0);
        }

        $Cachecode=Redis::get('login_code_'.$mobile);
        if($code!=$Cachecode){
            ajaxReturn(null,'验证码错误！',0);
        }
        $userInfo=Users::where(array("account"=>$mobile))->first();
        if(!empty($userInfo)){
            if ($userInfo['is_over'] == 1){
                ajaxReturn(null,'此账号已被封禁!',0);
            }
            $ip =$request->ip();
            $token=md5(rand_string(6,1));
            Users::where(array("account"=>$mobile))->update(array("token"=>$token,'take_status'=>0,"last_ip"=>$ip,'last_time'=>time()));
            $userInfo['token']=$token;
            ajaxReturn($userInfo,'登陆成功！');
        }else{
            ajaxReturn("",'账号不存在！',0);
        }
    }

    /**
     * 短信登录发送验证码
     */
    public function sendcode(Request $request) {
    	
        if($request->isMethod('post')) {
            $mobile=(int)$_POST['mobile'];
            
            if(!isMobile($mobile)) {
                ajaxReturn('','手机号码格式错误！',0);
            }
            
            $userInfo=Users::where(array("account"=>$mobile))->first();
            if(!$userInfo){
                ajaxReturn('','账号不存在！',0);
            }
            if ($userInfo['is_over'] == 1){
                ajaxReturn(null,'此账号已被封禁!',0);
            }
            $code=rand_string(6,1);
            $ip =$request->ip();
            //todo 发送短信
            $res=Verificat::yxtsend($mobile,$code,$ip,'login_code_');
            if($res== 0) {
                Verificat::insertsendcode($code,$mobile,4,$ip,1,'发送成功!');
                ajaxReturn('','发送成功!',1);
            } elseif($res==10001) {
                Verificat::insertsendcode($code,$mobile,4,$ip,0,'请勿频繁发送!');
                ajaxReturn('faild','请勿频繁发送!',0);
            } else {
                Verificat::insertsendcode($code,$mobile,4,$ip,0,$res);
                ajaxReturn('faild','发送失败!',0);
            }
        } else {
            ajaxReturn(null,'请求数据异常!',0);
        }
    }


    /**
     * 用户注册
     */
    public function mobile(Request $request){
        if($request->isMethod('post')) {
            $mobile=(int)$_POST['mobile'];
            $code=htmlformat($_POST['code']);
            $sendcode=htmlformat($_POST['sendcode']);
            $password=htmlformat($_POST['pwd']);
            $repassword=htmlformat($_POST['repwd']);

            if(!isMobile($mobile)){
                ajaxReturn(null,'手机号码格式错误',0);
            }
            if($password!=$repassword){
                ajaxReturn(null,'密码不一致',0);
            }
            if(strlen($password)<6){
                ajaxReturn(null,'密码不能小于6位',0);
            }


            //判断邀请码是否存在
            $codeinfo=Imsi::where(array("code"=>$code))->first();
            if (empty($codeinfo)){
                ajaxReturn(null,'邀请码不存在',0);
            }

            if ($codeinfo['status'] == 1){
                ajaxReturn(null,'邀请码已被占用'.$code,0);
            }
            $Cachecode=Redis::get('register_code_'.$mobile);
            if($sendcode!=$Cachecode){
                ajaxReturn(null,'验证码错误！',0);
            }
            //判断用户是否存在
            $userInfo=Users::where(array("account"=>$mobile))->first();
            if(!empty($userInfo)){
                ajaxReturn(null,'账号已存在！',0);
            }else{
                //不存在 入库用户信息
                $ip =$request->ip();
                $user_id=$this->insertUserInfo($mobile,$codeinfo,$password,$ip);
                if(empty($user_id)){
                    ajaxReturn(null,'注册失败！',0);
                }else{
                    Imsi::where(array("code"=>$code))->update(array("bind_id"=>$user_id,"status"=>1,"zytime"=>time()));
                    Userscount::insert(array("user_id"=>$user_id,"creatime"=>time()));
                    $user_info=Users::where(array("account"=>$mobile))->first();
                    ajaxReturn($user_info,'注册成功！');
                }
            }
        } else {
            ajaxReturn(null,'请求数据异常!',0);
        }
    }

    /**
     * 账户注册发送验证码
     */
    public function regsendcode(Request $request) {
        if($request->isMethod('post')) {
            $mobile=(int)$_POST['mobile'];
            if(!isMobile($mobile)) {
                ajaxReturn('','手机号码格式错误！',0);
            }
            $userInfo=Users::where(array("account"=>$mobile))->first();
            if($userInfo){
                ajaxReturn('','账号已存在！',0);
            }
            $code=rand_string(6,1);
            $ip =$request->ip();
            //todo 发送短信
            $res=Verificat::yxtsend($mobile,$code,$ip,'register_code_');
            if($res=="0") {
                Verificat::insertsendcode($code,$mobile,4,$ip,1,'发送成功!');
                ajaxReturn('','发送成功!',1);
            } elseif($res==10001) {
                Verificat::insertsendcode($code,$mobile,4,$ip,0,'请勿频繁发送!');
                ajaxReturn('faild','请勿频繁发送!',0);
            } else {
                Verificat::insertsendcode($code,$mobile,4,$ip,0,$res);
                ajaxReturn('faild','发送失败',0);
            }
        } else {
            ajaxReturn(null,'请求数据异常!',0);
        }
    }

    /**码商注册数据存入
     * @param $mobile
     * @param $codeinfo
     * @param $password
     * @param $ip
     * @return bool
     */
    public function insertUserInfo($mobile,$codeinfo,$password,$ip){

        if($codeinfo["user_id"]==0||$codeinfo["user_id"]==""||$codeinfo["user_id"]==null)
            $codeinfo["user_id"]=0;

        $info['pid']=$codeinfo["user_id"];
        $info['shenfen']=$codeinfo["grade"];
        $info['account']=$mobile;
        $info['password']=md5($password);
        $info['token']=md5(rand_string(6,1));
        $info['money']=0;
        $info['imsi_num']=0;
        $info['frozen']=0;
        $info['take_status']=0;
        $info['reg_ip']=$info['last_ip']= $ip;
        $info['reg_time']=$info['last_time']=time();
        $info['mobile']=$mobile;
        $user_id=Users::insertGetId($info);
        return $user_id;
    }



}
