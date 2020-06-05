<?php
/**
 * Created by PhpStorm.
 * User: LK
 * Date: 2019/11/3
 * Time: 9:14
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Request;
class Verificat extends Model {
    protected  $table = 'verificat';
    public $timestamps = false;

    /**云通信发送短信
     * @param $mobile
     * @param $code
     * @param $ip
     * @return bool|int|mixed|string
     */
    public static function yxtsend($mobile,$code,$ip,$key){
        $status=Redis::get('sendsms_lock_'.$mobile);
        if($status==2){
            return 10001;
        }
        Redis::setex($key.$mobile,300,$code);
        Redis::setex('sendsms_lock_'.$mobile,60,2);
        $data['username']='fjnphy';
        $data["password"] = md5(md5("Uj41oPwQ").time());//密码
        $data["mobile"] = $mobile;//手机号
        $data["content"] = '【大拇指】您的验证码为'.$code.'，在5分钟内有效。';
        $data["tKey"]=time();
        $url = 'http://api.mix2.zthysms.com/v2/sendSms';

        $data=json_encode($data);
        //   print_r($data);
        $res = Verificat::https_post_kf($url,$data);

        $res=json_decode($res,true);

        if ($res['code'] == 200){
            $res=0;
        }else{
            $res=false;
        }
        return $res;
    }

    /**云信发送验证码
     * @param $mobile
     * @param $code
     * @return bool|int|string
     */
    public  static function yunxinsend($mobile,$code,$ip,$key){

        $status=Redis::get('sendsms_lock_'.$ip);
        if($status==2){
            return 10001;
        }
        Redis::setex($key.$mobile,300,$code);
        Redis::setex('sendsms_lock_'.$ip,60,2);

        $username='b01fz0';
        $password=md5('12345678');

        $content='【HOLDON】您的验证码为'.$code.',在5分钟内有效。';
        $postArr = array (
            'smstype' =>'4',//短信发送发送
            'clientid'  =>  $username,
            'password' => $password,
            'mobile' => (string)$mobile,
            'content' => $content ,
            'sendtime'=>date('Y-m-d H:i:s'),
            'extend'=>'00',
            'uid'=>'00'
        );
        $api_send_url = "https://u.smsyun.cc/sms-partner/access/$username/sendsms";
        $result = Verificat::curlPost( $api_send_url , $postArr);
        $result=json_decode($result);
        if ($result->data[0]->code===0) {
            return 0;
        }else{
            return '发送失败,错误码：'.$result->data[0]->code.','.$result->data[0]->msg;
        }
    }

    /**云通信创表发送短信
     * @param $mobile
     * @param $code
     * @param $ip
     * @return bool|int|mixed|string
     */
    public static function createtablesend($mobile,$content){

        $data['username']='fjnphy';
        $data["password"] = md5(md5("Uj41oPwQ").time());//密码
        $data["mobile"] = $mobile;//手机号
        $data["content"] = $content;
        $data["tKey"]=time();
        $url = 'http://api.mix2.zthysms.com/v2/sendSms';

        $data=json_encode($data);
        //   print_r($data);
        $res = Verificat::https_post_kf($url,$data);

        $res=json_decode($res,true);

        if ($res['code'] == 200){
            $res=0;
        }else{
            $res=false;
        }
        return $res;
    }

    /**短信宝发送验证码
     * @param $mobile
     * @param $code
     * @return bool|int|string
     */
    public  static function dxbsend($mobile,$code,$ip,$key){

        $status=Redis::get('sendsms_lock_'.$ip);
        if($status==2){
            return 10001;
        }
        Redis::setex($key.$mobile,300,$code);
        Redis::setex('sendsms_lock_'.$ip,60,2);

        $username='q17152437247';
        $password=md5('qwer1234');

        $content=urlencode('【大拇指】您的验证码为'.$code.'，在5分钟内有效。');
        $host="http://api.smsbao.com/sms?u=".$username."&p=".$password."&m=".$mobile."&c=".$content;
        $res=file_get_contents($host);
        $statusStr = array(
            "0" => 0,//发送成功
            "-1" => "参数不全!",
            "-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
            "30" => "密码错误!",
            "40" => "账号不存在!",
            "41" => "余额不足!",
            "42" => "帐户已过期!",
            "43" => "IP地址限制!",
            "50" => "内容含有敏感词!",
            "10001" => "请勿频繁发送!"
        );
        $msg = $statusStr[$res];
        return $msg;
    }
    /**存储手机验证码发送记录
     * @param $code
     * @param $mobile
     * @param $type
     * @param $ip
     * @param $status
     * @param $sendmsg
     */
    public static function insertsendcode($code,$mobile,$type,$ip,$status,$sendmsg) {
        $data=array(
            'code'=>$code,
            'phone'=>$mobile,
            'type'=>$type,
            'sendip'=>$ip,
            'status'=>$status,
            'sendmsg'=>$sendmsg,
            'creatime'=>time()
        );
        Verificat::insert($data);
    }

    /**发送请求
     * @param $url
     * @param $data
     * @return mixed|string
     */
    private static function https_post_kf($url, $data)
    {
        $headers = array(
            "Content-type: application/json;charset='utf-8'",
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache");

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }

}