<?php

!defined('ROOT') && define ('ROOT', __DIR__ . '/');
if (file_exists(ROOT . 'init.inc.private.php')) {
	require_once ROOT . 'init.inc.private.php'; //加载私有配置文件
}

require_once ROOT . 'init.inc.php'; //加载配置文件

// 定义应用目录
define('APP_PATH', ROOT . 'application/');

define('BIND_MODULE', 'push/Worker');

// 加载框架引导文件
require ROOT . 'thinkphp/start.php';
