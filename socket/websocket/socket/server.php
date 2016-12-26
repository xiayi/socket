<?php
header("Content-Type:text/html; charset=utf-8");
set_time_limit(0);//确保在连接客户端时不会超时

$ip = '192.168.1.239';
$port = 9003;

if(($sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
    echo "socket_create() 失败:".socket_strerror($sock)."\n";
}

if(($ret = socket_bind($sock,$ip,$port)) < 0) {
    echo "socket_bind() 失败:".socket_strerror($ret)."\n";
}

if(($ret = socket_listen($sock,4)) < 0) {
    echo "socket_listen() 失败:".socket_strerror($ret)."\n";
}

$count = 0;

do {
    if (($msgsock = socket_accept($sock)) < 0) {
        echo "socket_accept() 失败: " . socket_strerror($msgsock) . "\n";
        break;
    } else {
        //把收到的内容发到客户端
        $buf = socket_read($msgsock,8192);
        socket_write($msgsock, $buf, strlen($buf));

        //if(++$count >= 10){
        //    break;
        //};

    }
    //echo $buf;
    socket_close($msgsock);

} while (true);

socket_close($sock);
?>