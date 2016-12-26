<?php
use Workerman\Worker;
use Workerman\WebServer;
use Workerman\Autoloader;
use PHPSocketIO\SocketIO;

// composer autoload
include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../src/autoload.php';

$io = new SocketIO(2020);
$io->on('connection', function($socket){

    //创建redis实例
    $socket -> redis = new \Redis();
    $socket -> redis -> connect("127.0.0.1", "6379");

    $socket->addedUser = false;

    //登录事件
    $socket->on('enter', function ($data) use($socket){
        $socket -> roomid = $data['roomid'];
        $socket -> user = $data['user'];
        $socket -> uid = $data['user']['uid'];
        $socket -> nickname = $data['user']['nickname'];
        $socket -> avatar = $data['user']['avatar'];
        if(!empty($socket -> roomid) && !empty($socket -> uid)){
            $socket -> user['entertime'] = time();//进入时间
            $socket -> redis -> hMset("user:".$socket -> uid,$socket -> user);//存入hash表
            $socket -> redis -> sadd($socket -> roomid,$socket -> uid);//存入set集合

            $socket -> join($socket -> roomid);//加入聊天室组

            $socket->addedUser = true;

            //当前用户发送数据
            $userlist = $socket -> redis -> smembers($socket -> roomid);
            foreach($userlist as $key => $val){
                $userlist_[] = $socket -> redis -> hmGet("user:".$val, array('uid', 'nickname', 'avatar'));
            }
            $socket -> emit('entered',
                array(
                    'usercount' => $socket -> redis -> sSize($socket -> roomid),
                    'userlist' => $userlist_
                )
            );

            //广播给聊天室组所有用户
            $socket -> broadcast -> to($socket -> roomid) -> emit('userentered',
                array(
                    'user' => $socket -> user,
                    'usercount' => $socket -> redis -> sSize($socket -> roomid)
                )
            );

        }
    });

    //发送聊天数据
    $socket->on('sendmsg', function ($data)use($socket){
        $socket -> roomid = $data['roomid'];
        $socket -> user = $data['user'];
        $socket -> data = $data['data'];
        $socket -> emit('sendmsg', array(
                'user'=> $socket->user,
                'data'=> $socket -> data
            )
        );

        $socket -> broadcast -> to($socket -> roomid) -> emit('sendmsg', array(
                'user'=> $socket->user,
                'data'=> $socket -> data
            )
        );

    });

    //当用户断开
    $socket->on('disconnect', function () use($socket) {
        if($socket->addedUser) {
            $socket -> leave($socket->roomid);//离开聊天室组
            $socket -> redis -> sRem($socket->roomid,$socket->uid);//从数据库删除

            $socket -> user['leavetime'] = time();//断开时间
            //广播给聊天室组所有用户
            $socket -> broadcast -> to($socket->roomid) -> emit('userleft',
                array(
                    'user' => $socket -> user,
                    'usercount' => $socket -> redis -> sSize($socket->roomid)
                )
            );
        }
    });

});

$web = new WebServer('http://0.0.0.0:2022');
$web->addRoot('localhost', __DIR__ . '/public');

Worker::runAll();
