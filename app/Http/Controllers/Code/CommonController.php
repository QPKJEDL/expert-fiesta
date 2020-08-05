<?php
/**
 * Created by PhpStorm.
 * User: LK
 * Date: 2019/10/31
 * Time: 16:44
 */
namespace App\Http\Controllers\Code;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\Models\Users;
class CommonController extends Controller {
    protected $token = '';
    protected $uid='';
    protected $member = array();
    public $redis=null;
    public $imgurl="http://192.168.1.8";
    public $kefuurl="http://192.168.1.8";
    public function __construct() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: OPTIONS,POST,GET,PUT,DELETE');
        $this->checkLogin();
    }
    //验证用户信息
    public function checkLogin() {
        $user_id = $_SERVER['HTTP_USERID'];
        $token = $_SERVER['HTTP_TOKEN'];

        $k="UserInfo_".$user_id;
        $old=Redis::get($k);
        $data=json_decode($old,true);

        $userInfo = Users::where(array('user_id' => $user_id))->first();
        if(!$userInfo) {
            ajaxReturn(null, '用户不存在!', 2);
        }
        if ($userInfo['is_over'] == 1){
            ajaxReturn(null,'此账号已被封禁!',2);
        }
        if ($userInfo['token'] == '') {
            ajaxReturn(null, '登录失效,请重新登录!', 2);
        }

        if ($data['Token'] === $token) {
            $this->uid = $user_id;
            $this->member = $userInfo;
            return;
        } else {
            ajaxReturn(null, '登录验证失败,请重新登录!', 2);
        }
    }

}