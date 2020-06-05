<?php
/**
 * Created by PhpStorm.
 * User: LK
 * Date: 2019/11/7
 * Time: 11:04
 */
namespace App\Http\Controllers\Code;

use App\Models\Index;
use App\Models\Kefu;
use App\Models\Message;
use App\Models\Notice;
use Illuminate\Http\Request;
class ZfnoticeController extends CommonController {
    /**
     * 获取公告详情
     */
    public function index(Request $request) {
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
    /**
     * 公告列表
     */
    public function getnotice() {
        $list = Notice::orderBy('id','desc')->limit(1)->first();
        ajaxReturn($list,'请求成功!',1);
    }
}