<?php
/**
 * Created by PhpStorm.
 * User: LK
 * Date: 2019/10/31
 * Time: 16:44
 */
namespace App\Http\Controllers\Code;

use App\Models\Adminvesion;
use App\Models\Czrecord;
use App\Models\Order;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use \GatewayWorker\Lib\Gateway;
use PragmaRX\Google2FA\Google2FA;
use \QrReader;




class IndexController extends Controller
{
    public function welcome(Request $request){
//        for($i=0;$i<2000;$i++){
//            $res =DB::table('users')->insert(array('second_pwd'=>1,'is_over'=>12,'account'=>123,'password'=>123456,'reg_ip'=>'0.0.0.0'));
//        }
//        print_r($res);
//        if($request->isMethod('post')){
//            $res =$request->input();
//        }
//        $this->ajaxReturn('123');
//        $userinfo =DB::table('users')->where(array('user_id'=>1))->first();
//        print_r(get_object_vars($userinfo));
//        print_r($this->uid);
//        Redis::set('aaa',123);

//        print_r(geoip('115.54.175.76')->toArray());
//        print_r(Redis::get('aaa'));
//
//        for ($i=0;$i<10000;$i++){
//            $res[] =getrequestId('1','300013','14');
//        }
//        $ress = FetchRepeatMemberInArray($res);
//        print_r($res);
//        print_r($ress);

//        $data = array(
//            'user_id'=>1,
//            'order_no'=>getrequestId('1','300013','14'),
//            'money'=>10000,
//            'creatime'=>time(),
//            'status'=>1
//        );
//        $data = array(
//            'user_id'=>1,
//            'name'=>111,
//            'score'=>10000,
//            'czimg'=>getrequestId('1','300013','14'),
//            'creatime'=>time(),
//            'status'=>1
//        );
//        for ($i=0;$i<10000;$i++){
//            Czrecord::insert($data);
//        }
//        $type = 2;
//        $user_id=1;
//        $id =2;
//        Redis::rPush('erweimas'.$type.$user_id,$id);
//        $google2fa = new Google2FA();
//        $secretKey=$google2fa->generateSecretKey();
//        $qrCodeUrl = $google2fa->getQRCodeUrl(
//            "EPayPlus",//名称后台获取
//            13632470525,
//            $secretKey
//        );
//       $code = "<img src='{$qrCodeUrl}'>";
//        echo $code;
//        print_r($secretKey);
//        $tradeMoney =10000;
//        Userscount::where('user_id',1)->increment('balance',$tradeMoney,['freeze_money'=>DB::raw("freeze_money - $tradeMoney"),'tol_sore'=>DB::raw("tol_sore + $tradeMoney")]);
//        header("content-type:text/html;charset=utf-8");
//        $str = "'123456'     ";
//         // 转换双引号和单引号
//        echo  preg_replace('/\'/', '', str_replace(" ",'',htmlspecialchars($str)));
//        print_r($balance = Userscount::onWriteConnection()->where('user_id',1)->value('balance'));
//        $tradeMoney =100;
//        Userscount::onWriteConnection()->where('user_id',1)->decrement('balance',$tradeMoney,['freeze_money'=>DB::raw("freeze_money + $tradeMoney")]);

//        try {
//            $cmd = Userscount::onWriteConnection()->where('user_id',1)->decrement('balance','abc');
//            $cmd->execute();
//        } catch (Exception $e) {
//            print $e->getMessage();
//            exit();
//        }
//        $res = Order::getordersntable(getrequestId());
//        print_r($res);
//        Redis::rPush('erweimas00000',12);
//        Redis::rPush('erweimas00000',10);
//        Redis::rPush('erweimas00000',9);
//        Redis::rPush('erweimas00000',8);
//        Redis::rPush('erweimas00000',11);
//        $list = Redis::lrange('erweimas00000',0,-1);
//        print_r($list);
//        Redis::lRem('erweimas00000',0,11);
//        Redis::rPush('erweimas00000',11);
//        $list = Redis::lrange('erweimas00000',0,-1);
//        print_r($list);
//        $ordertatle = Order::getordertable();
//        $weeksuf = computeWeek(time(),false) - 1;
//        $ordertatlepre = Order::getordertable($weeksuf);
//        $first  =$ordertatlepre->where(array("user_id"=>1,"status"=>0,"sk_status"=>0));
//        $res = $ordertatle->where(array("user_id"=>37,"status"=>0,"sk_status"=>0))->union($first)->orderBy('creatime', 'desc')->get()->toArray();
//        print_r($res);
//        $ordertable = Order::getordersntable('2019112311014694603248');
//        $order_infos=$ordertable->where(array("order_sn"=>'2019112311014694603248'))->first();
//        $this->senduidnotify($order_infos,3,2);
//        $orderjson=Redis::get('orderrecord_2019120218170937415849');
//        print_r($orderjson);
//        $rates = 0.0145;
//        $pronums = '1.45';
//        $pronums = htmlformat($pronums/100);
//        if( $pronums >= $rates) {
//            ajaxReturn('','费率需低于自己的!',0);
//        }else{
//            ajaxReturn('','费率设置不正常!',0);
//        }
//        print_r(Gateway::getClientIdByUid('100000'));
//        print_r(Gateway::getClientIdByUid('103'));
//        print_r('11111');
//        ini_set('memory_limit','640M');
//        $qrcode = new QrReader('http://www.orange.com/erweima/oldcode/6.png');
//        $text = $qrcode->text(); //返回识别后的文本
//        $url ='alipays://platformapi/startapp?appId=09999988&actionType=toAccount&goBack=NO&userId=2088212363889840&memo=QQ_1151878751';
//        $code =$this->qrCode($text);
//        print_r(config('code.zfwurl'));
//        VerifyCode::get(1,2);
//        VerifyCode::get(1,2);
//        if(VerifyCode::check(3)){
//            echo 1;
//        }else{
//            echo 0;
//        }
//        Gateway::$registerAddress = '116.140.34.38:1236';
//        $data=array('1231',2,'123');
//        $data=json_encode($data);
//        Gateway::sendToAll($data);
//        $order_sn = '2019121714000616943116';
//        $ordertable =Order::getordersntable($order_sn);
//        $orderinfo=$ordertable->setConnection('mysql2')->where(array("order_sn"=>$order_sn))->get();
//        print_r($orderinfo);
//        $score=Businessbillflow::getbusbftable('20191219085234546508')->where('business_code','300001')->where('status','3')->sum('score');
//        $tradeMoney=Businessbillflow::getbusbftable('20191219085234546508')->where('business_code','300001')->where('status','3')->sum('tradeMoney');
//        $score=Businessbillflow::getbusbftable('20191219085234546508')->where('business_code','300001')->where('creatime','<',1576684800)->sum('score');
//        print_r($score);
//        $type = 2;
//        $user_id = 100082;
//        $lists = Redis::lrange('erweimas'.$type.$user_id,0,-1);
//        $erlist = Erweima::where(array('user_id'=>$user_id,'type'=>$type))->get()->toArray();
//        foreach ($erlist as $k=>$v){
//            if($v['code_status']==1){
//                Redis::lRem('erweimas'.$type.$user_id,0,$v['id']);
//            }
//            if($v['code_status']==0 && $v['status']==0){
//                Redis::lRem('erweimas'.$type.$user_id,0,$v['id']);
//                Redis::rPush('erweimas'.$type.$user_id,$v['id']);
//            }
//        }
//        $list = Redis::lrange('erweimas'.$type.$user_id,0,-1);
//        print_r($lists);
//        print_r($list);
       // $list = Erweima::where(array('status'=>0,'type'=>2,'zf_phone'=>'0'))->get()->toArray();
       // print_r($list);
       // foreach ($list as $k){
       //     Redis::lRem("erweimas2".$k['user_id'],0,$k['id']);
       //     $status = Erweima::where(array('status'=>0,'type'=>2,'zf_phone'=>'0','id'=>$k['id']))->update(['status'=>1]);
       //     print_r($status);
       // }
        // Redis::set('push_map','119.28.226.32');
        // Redis::set('over_time',300);
        // Redis::set('one_time_draw',500);
        // Redis::set('free_time',900);
        // header("Location:https://qr.alipay.com/fkx12419uhlvovp3ff6n7a2");


       
    }
    public function zfbtz(Request $request){
        $url = urlencode($request->input('code_url'));
        header("Location:alipays://platformapi/startapp?appId=20000067&url=$url");
    }

    /**判断支付码重复支付或者过期支付
     * @param Request $request
     */
    public function zfpaycheck(Request $request){
        $order_sn = $request->input('order_id');
        $order =Order::getordersntable($order_sn);
        //  获取订单信息
        $orderinfo = $order->where(array("order_sn"=>$order_sn))->first();
        if(!$orderinfo){
            echo "<script>alert('二维码已失效')</script>";exit();
        }
        $code_url=Redis::get('order_codeurl_'.$order_sn);
        if (!$code_url>0 || empty($code_url)) {
            echo "<script>alert('二维码已失效')</script>";exit();
        }
        $code_url = urlencode($code_url);
        header("Location:alipays://platformapi/startapp?appId=20000067&url=$code_url");
    }

    // 生成普通二维码
    // 直接可以在浏览器上输出二维码图片
    public  function qrCode($url=''){
        $img = new \QRcode();
        $errorCorrectionLevel = 'L';//容错级别
        $matrixPointSize = 6; // 生成图片大小
        $filename = 'erweima/oldcode/'.getorderId_three().'.png';
        //生成二维码图片
        $img->png($url,$filename, $errorCorrectionLevel, $matrixPointSize, 2);
        echo '<img src="'.$filename.'">';
    }
    private function scerweima($url=''){

        $object = new \QRcode();

        // $level=3;

        // $size=4;

        // $errorCorrectionLevel =intval($level) ;//容错级别

        // $matrixPointSize = intval($size);//生成图片大小

        $value = $url;                  //二维码内容

        $errorCorrectionLevel = 'H';    //容错级别

        $matrixPointSize = 6;           //生成图片大小

        //生成二维码图片

        $filename = 'uploads/code/oldcode/'.getorderId_three().'.png';


        $object->png($value,$filename , $errorCorrectionLevel, $matrixPointSize, 2);

        $logo = public_path('static/qrcodes/orange.png');  //准备好的logo图片

        $QR = $filename;            //已经生成的原始二维码图

        if (file_exists($logo)) {

            $QR = imagecreatefromstring(file_get_contents($QR));        //目标图象连接资源。

            $logo = imagecreatefromstring(file_get_contents($logo));    //源图象连接资源。

            $QR_width = imagesx($QR);           //二维码图片宽度

            $QR_height = imagesy($QR);          //二维码图片高度

            $logo_width = imagesx($logo);       //logo图片宽度

            $logo_height = imagesy($logo);      //logo图片高度

            $logo_qr_width = $QR_width / 4;     //组合之后logo的宽度(占二维码的1/5)

            $scale = $logo_width/$logo_qr_width;    //logo的宽度缩放比(本身宽度/组合后的宽度)

            $logo_qr_height = $logo_height/$scale;  //组合之后logo的高度

            $from_width = ($QR_width - $logo_qr_width) / 2;   //组合之后logo左上角所在坐标点

            //重新组合图片并调整大小

            /*

             *  imagecopyresampled() 将一幅图像(源图象)中的一块正方形区域拷贝到另一个图像中

             */

            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);

        }

        $numbers = getorderId_three();

        //输出图片

        imagepng($QR, 'uploads/code/newcode/'.$numbers.'.png');

//        return '<img src="uploads/code/newcode/'.$numbers.'.png">';
        return "uploads/code/newcode/$numbers.png";
    }

    /**
     * @param $orderinfo
     * @param $type
     * @param $ordercount
     * 发送数据
     */
    private function senduidnotify($orderinfo,$type,$ordercount) {
        $address = Redis::get('push_map');
        Gateway::$registerAddress = $address.':1236';
        $data=array(
            'ordercount'=>$ordercount,
            'type'=>$type,
            'data'=>array(
                'order_id'=>$orderinfo['id'],
                'payType'=>$orderinfo['payType'],
                'tradeMoney'=>$orderinfo['tradeMoney'],
                'order_sn'=>$orderinfo['order_sn'],
                'time'=>$orderinfo['creatime']),
            'home'=>$orderinfo['home']
        );
        $data=json_encode($data);
        Gateway::sendToUid($orderinfo['user_id'],$data);
    }

    /**
     * 检测码商是否在线的数字验证码
     */
    public function getverifyCode() {
        $user_id=input('user_id');
        VerifyCode::get(1,2,$user_id);
    }

    /**
     * 校验数字验证码
     */
    public function checkverifyCode() {
        $user_id=input('user_id');
        if(VerifyCode::check(3,$user_id)){
            ajaxReturn(null,'验证成功!',1);
        }else{
            ajaxReturn(null,'验证失败!',0);
        }
    }

    /**
     * 检测更新
     */

    public function update(){
    	
        $v=$_POST['currentversion'];
        
        //靠谱
        $data['url'] = 'http://download.fir.im/apps/5e25463823389f4cf432c6d2/install?download_token=b2b041816e1b15267a635d1cb3feae99';
        $zxinfo =Adminvesion::where('is_open',1)->orderBy('creatime','desc')->first();
        $version_no =$zxinfo['version_no'];
        $data['detail'] =$zxinfo['detail'];
        /**
         * force 0 非强制更新 1 强制更新 2不更新
         */
        if($v==$version_no){
            $data['force']='2';
            ajaxReturn($data,'最新版本',1);
        }else{
            $data['force']=$zxinfo['force'];
            ajaxReturn($data,'版本更新',1);
        }
    }

    /**
     * 测试支付调取
     */
    public function index() {
        //print_r($_POST);exit();
        $codeType =$_POST['codeType'];
        $data["payType"] = $_POST['payType'];
        //支付方式  1微信  2支付宝
        $data["codeType"] = $codeType;
        //二维码类型  1 固码  2 通用码
        $data["out_order_sn"] = $_POST['out_order_sn'];
        //订单号
        $data["tradeMoney"] = $_POST['money'];
        $data["NotifyUrl"] = "http://".$_SERVER['HTTP_HOST']."/code/index/notifyUrl";
        //回调
        $key = $_POST['accessKey'];        
        $data["sign"] = $this->getSign($data,$key);
        //签名
        $data["business_code"] = $_POST['business_code'];
        $url = 'http://'.$_SERVER['HTTP_HOST'].'/code/orderym/kuaifupay';
        $res = $this->https_post_kf($url,$data);
        print_r($res);
        exit();
    }

    public function notifyUrl() {
        $retrun_datas =$_POST;
        $retrun_sign=$retrun_datas['sign'];
        //签名值
        unset($retrun_datas['sign']);
        $key = '461aefa5253910b40d1d71eabb8963fb';
        $sign =$this->getSign($retrun_datas,$key);
        if($retrun_sign==$sign) {
            echo "success";
            file_put_contents('./notifyUrl.txt',print_r($retrun_datas,true).PHP_EOL,FILE_APPEND);
        } else {
            echo "fail";
            file_put_contents('./notifyUrl.txt',print_r($retrun_datas,true).PHP_EOL,FILE_APPEND);
            file_put_contents('./notifyUrl.txt','sign-'.$sign.PHP_EOL,FILE_APPEND);
            file_put_contents('./notifyUrl.txt','retrun_sign-'.$retrun_sign.PHP_EOL,FILE_APPEND);
        }
    }
    private function getSign($Obj,$key) {
        foreach ($Obj as $k => $v) {
            $Parameters[$k] = $v;
        }
        //签名步骤一：按字典序排序参数
        ksort($Parameters);
        $String = $this->formatBizQueryParaMap($Parameters, false);
//        print_r($String);echo "</br>";

        //签名步骤二：在string后加入KEY
        $String = $String . "&accessKey=" . $key;
//        print_r($String); echo "</br>";

        //签名步骤三：MD5加密
        $String = md5($String);
        //echo "【string3】 ".$String."</br>";
        //签名步骤四：所有字符转为大写
        $result_ = strtoupper($String);
        //echo "【result】 ".$result_."</br>";
        return $result_;
    }
    private function formatBizQueryParaMap($paraMap, $urlencode) {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if ($urlencode) {
                $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }
    private function https_post_kf($url, $data) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            return 'Errno' . curl_error($curl);
        }
        curl_close($curl);
        return $result;
    }
}
