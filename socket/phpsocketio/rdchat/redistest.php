<?php
$redis = new \Redis(); //创建redis实例
$redis -> connect("127.0.0.1", "6379");
$redis->zAdd('key', 1, 'val1');
$redis->zAdd('key', 0, 'val0');
$redis->zAdd('key', 5, 'val5');
$list = $redis->zRevRange('key', 0, -1,true);
var_dump($list);

$list2 = $redis->zRevRange('key2', 0, -1,true);
var_dump($list2);

$redis->hMset("user:001", array("uid" => "001","nickname" => "miles2", "avatar" => "http://d.56show.com/boy_02.png"));

$user001 = $redis->hmGet('user:001', array('uid', 'nickname', 'avatar'));
var_dump($user001);