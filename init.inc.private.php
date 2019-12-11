<?php

!defined('WEB_HOST') && define('WEB_HOST', 'http://www.tp5.com');
!defined('WEB_BASE') && define('WEB_BASE', '/');

!defined('ADMIN_USER_ENABLED') && define('ADMIN_USER_ENABLED', true);

/**
 * Redis Config [requirepass redis@123456]
 */ 
!defined('REDIS_ENABLED') && define('REDIS_ENABLED', true);
!defined('REDIS_HOST') && define('REDIS_HOST', '127.0.0.1');
!defined('REDIS_PASSWORD') && define('REDIS_PASSWORD', 'redis@123456');
!defined('REDIS_PORT') && define('REDIS_PORT', '6379');
!defined('REDIS_DATABASE') && define('REDIS_DATABASE', '0');

/**#@+
 * database information
 */
!defined('DB_HOST') && define('DB_HOST', '127.0.0.1');
!defined('DB_PORT') && define('DB_PORT', '3306');
!defined('DB_USERNAME') && define('DB_USERNAME', 'root');
!defined('DB_PASSWORD') && define('DB_PASSWORD', '123456');
!defined('DB_DATABASE') && define('DB_DATABASE', 'yfinance');

/**
 * WebScoket Config
 */ 
!defined('WEBSCOKET_ENABLED') && define('WEBSCOKET_ENABLED', false);
!defined('WEBSCOKET_HOST') && define('WEBSCOKET_HOST', '127.0.0.1');
!defined('WEBSCOKET_PORT') && define('WEBSCOKET_PORT', '9502');