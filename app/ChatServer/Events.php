<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;
use Workerman\Lib\Timer;
class Events
{

    public static function onWorkerStart($businessWorker)
    {

        $hostUrl = "http://119.28.226.32:8787/";
        if($businessWorker->id === 2){
            Timer::add(2, function () use ($hostUrl) {
                // 订单5分钟更改为过期：
                file_get_contents($hostUrl . "code/Timernotify/setstale");
                // 订单3个小时解冻订单并返回跑分
                file_get_contents($hostUrl . "code/Timernotify/orderunfreeze");
            });
        }
        if($businessWorker->id === 3){
            Timer::add(2, function () use ($hostUrl) {
                // 商户返佣：
                file_get_contents($hostUrl . "code/Timernotify/bussiness_fy");
                // 码商返佣
                file_get_contents($hostUrl . "code/Timernotify/user_fy");
            });
        }

        if($businessWorker->id === 4){
            Timer::add(5, function () use ($hostUrl) {
                // 第一次回调：
                file_get_contents($hostUrl . "code/Timernotify/sfpushfirst?key=9a1b1f272be8c08c9ef05f601f9dde5d");
            });

            Timer::add(10, function () use ($hostUrl) {
                // 第二次回调
                file_get_contents($hostUrl . "code/Timernotify/sfpushsecond?key=c04c96f89ade5c6e88deaf5a8b351c35");
            });
            Timer::add(15, function () use ($hostUrl) {
                // 第三次回调
                file_get_contents($hostUrl . "code/Timernotify/sfpushthird?key=3592f1c48196014b9161bc52aeefb45e");
            });

        }


    }

        /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message) {

        // 向所有人发送
        //数据解析
        /*$a=array(
            "m"=>"index",
            "a"=>"index",
            "uid"=>51,
            'client_id'=>$client_id
        );*/
        //$message=json_encode($a,true);
        //以上调试时候使用
        $req_data=json_decode($message,true);

        if($req_data['a']=='slogin'){

            $clientid = Gateway::getClientIdByUid($req_data['uid']);
            if(!$clientid){
                Gateway::bindUid($client_id, $req_data['uid']);
            }
            $msg =array(
                'm'=>'slogin',
                'token'=>1
            );
            Gateway::sendToUid($req_data['uid'], json_encode($msg));
        }
    }

//   /**
//    * 有消息时
//    * @param int $client_id
//    * @param mixed $message
//    */
//   public static function onMessage($client_id, $message)
//   {
//        // debug
//        echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id session:".json_encode($_SESSION)." onMessage:".$message."\n";
//
//        // 客户端传递的是json数据
//        $message_data = json_decode($message, true);
//        if(!$message_data)
//        {
//            return ;
//        }
//
//        // 根据类型执行不同的业务
//        switch($message_data['type'])
//        {
//            // 客户端回应服务端的心跳
//            case 'pong':
//                return;
//            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
//            case 'login':
//                // 判断是否有房间号
//                if(!isset($message_data['room_id']))
//                {
//                    throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
//                }
//
//                // 把房间号昵称放到session中
//                $room_id = $message_data['room_id'];
//                $client_name = htmlspecialchars($message_data['client_name']);
//                $_SESSION['room_id'] = $room_id;
//                $_SESSION['client_name'] = $client_name;
//
//                // 获取房间内所有用户列表
//                $clients_list = Gateway::getClientSessionsByGroup($room_id);
//                foreach($clients_list as $tmp_client_id=>$item)
//                {
//                    $clients_list[$tmp_client_id] = $item['client_name'];
//                }
//                $clients_list[$client_id] = $client_name;
//
//                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx}
//                $new_message = array('type'=>$message_data['type'], 'client_id'=>$client_id, 'client_name'=>htmlspecialchars($client_name), 'time'=>date('Y-m-d H:i:s'));
//                Gateway::sendToGroup($room_id, json_encode($new_message));
//                Gateway::joinGroup($client_id, $room_id);
//
//                // 给当前用户发送用户列表
//                $new_message['client_list'] = $clients_list;
//                Gateway::sendToCurrentClient(json_encode($new_message));
//                return;
//
//            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
//            case 'say':
//                // 非法请求
//                if(!isset($_SESSION['room_id']))
//                {
//                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
//                }
//                $room_id = $_SESSION['room_id'];
//                $client_name = $_SESSION['client_name'];
//
//                // 私聊
//                if($message_data['to_client_id'] != 'all')
//                {
//                    $new_message = array(
//                        'type'=>'say',
//                        'from_client_id'=>$client_id,
//                        'from_client_name' =>$client_name,
//                        'to_client_id'=>$message_data['to_client_id'],
//                        'content'=>"<b>对你说: </b>".nl2br(htmlspecialchars($message_data['content'])),
//                        'time'=>date('Y-m-d H:i:s'),
//                    );
//                    Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
//                    $new_message['content'] = "<b>你对".htmlspecialchars($message_data['to_client_name'])."说: </b>".nl2br(htmlspecialchars($message_data['content']));
//                    return Gateway::sendToCurrentClient(json_encode($new_message));
//                }
//
//                $new_message = array(
//                    'type'=>'say',
//                    'from_client_id'=>$client_id,
//                    'from_client_name' =>$client_name,
//                    'to_client_id'=>'all',
//                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
//                    'time'=>date('Y-m-d H:i:s'),
//                );
//                return Gateway::sendToGroup($room_id ,json_encode($new_message));
//        }
//   }
//
//   /**
//    * 当客户端断开连接时
//    * @param integer $client_id 客户端id
//    */
//   public static function onClose($client_id)
//   {
//       // debug
//       echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
//
//       // 从房间的客户端列表中删除
//       if(isset($_SESSION['room_id']))
//       {
//           $room_id = $_SESSION['room_id'];
//           $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
//           Gateway::sendToGroup($room_id, json_encode($new_message));
//       }
//   }
  
}
