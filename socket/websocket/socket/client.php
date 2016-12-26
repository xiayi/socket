<?php
header("Content-Type:text/html; charset=utf-8");
error_reporting(E_ALL);
set_time_limit(0);

$port = 9003;
$ip = "192.168.1.239";

/*
 +-------------------------------
 *    @socket连接整个过程
 +-------------------------------
 *    @socket_create
 *    @socket_connect
 *    @socket_write
 *    @socket_read
 *    @socket_close
 +--------------------------------
 */

$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if ($socket < 0) {
    echo "socket_create() 失败: " . socket_strerror($socket) . "\n";
}else {
    echo "创建连接...";
}

echo '试图连接'.$ip.':'.$port .'<br>';
$result = socket_connect($socket, $ip, $port);
if ($result < 0) {
    echo "socket_connect() 失败: ($result) " . socket_strerror($result) . "\n";
}else {
    echo '连接成功...';
}

$in = "我来了";
$out = '';

if(!socket_write($socket, $in, strlen($in))) {
    echo "socket_write() 失败: " . socket_strerror($socket) . "\n";
}else {
    echo "发送信息:{$in}".'<br>';;
}

while($out = socket_read($socket, 8192)) {
    echo "服务器返回:{$out}".'<br>';;
}

echo "开始关闭...".'<br>';;
socket_close($socket);
echo "关闭成功".'<br>';;
?>