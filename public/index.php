<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

header("Content-Type: application/json; charset=UTF-8");

header('Expires: 0');
header("Cache-Control:no-store,private, post-check=0, pre-check=0, max-age=0");
header("Pragma: no-cache");
header("Access-Control-Allow-Origin:*"); //设置跨域头

// [ 应用入口文件 ]

!defined('ROOT') && define ('ROOT', __DIR__ . '/../');
if (file_exists(ROOT . 'init.inc.private.php')) {
	require_once ROOT . 'init.inc.private.php'; //加载私有配置文件
}

require_once ROOT . 'init.inc.php'; //加载配置文件

// 定义应用目录
define('APP_PATH', ROOT . 'application/');

// 加载框架引导文件
require ROOT . 'thinkphp/start.php';
