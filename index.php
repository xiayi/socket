<?php
// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 绑定Admin模块到当前入口文件  生成模块目录
//define('BIND_MODULE','Home');

// 定义应用目录
define('APP_PATH','./Apps/');

// 定义运行时目录
define('RUNTIME_PATH','./Runtime/');

// 开启调试模式
define('APP_DEBUG',True);

// 更名框架目录名称，并载入框架入口文件
require './TPcore/ThinkPHP.php';
