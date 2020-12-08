<?php

/**
 * WEB
 */
!defined('WEB_HOST') && define('WEB_HOST', 'http://127.0.0.1');
!defined('WEB_BASE') && define('WEB_BASE', '/');
!defined('WEB_ENABLED') && define('WEB_ENABLED', true);
!defined('WEB_VERSION') && define('WEB_VERSION', time());

/**
 * admin user login config
 * 没有数据交互则需重新登录
 */
!defined('ADMIN_USER_ENABLED') && define('ADMIN_USER_ENABLED', true);
!defined('ADMIN_USER_TIME') && define('ADMIN_USER_TIME', 1800);

/**
 * 密钥
 */
!defined('TOKEN') && define('TOKEN', '$yz@123456^');

/**
 * 高德密钥
 */
!defined('GAO_DE_KEY') && define('GAO_DE_KEY', '123456');

/**
 * RSA 加密
 */
!defined('RSA_ENABLED') && define('RSA_ENABLED', true);

/**
 * mcrypt 加密 cbc模式
 */
!defined('MCRYPT_ENABLED') && define('MCRYPT_ENABLED', true);

/**
 * Redis 配置
 */ 
!defined('REDIS_ENABLED') && define('REDIS_ENABLED', true);
!defined('REDIS_HOST') && define('REDIS_HOST', '127.0.0.1');
!defined('REDIS_PASSWORD') && define('REDIS_PASSWORD', '123456');
!defined('REDIS_PORT') && define('REDIS_PORT', '6379');
!defined('REDIS_DATABASE') && define('REDIS_DATABASE', '0');

/**#@+
 * 数据库配置
 */
!defined('DB_HOST') && define('DB_HOST', '127.0.0.1');
!defined('DB_PORT') && define('DB_PORT', '3306');
!defined('DB_USERNAME') && define('DB_USERNAME', 'root');
!defined('DB_PASSWORD') && define('DB_PASSWORD', 'root');
!defined('DB_DATABASE') && define('DB_DATABASE', 'databasename');

/**#@+
 * OSS 配置
 */
!defined('OSS_ACCESS_ID') && define('OSS_ACCESS_ID', 'ID');
!defined('OSS_ACCESS_KEY') && define('OSS_ACCESS_KEY', 'KEY');
!defined('OSS_ENDPOINT') && define('OSS_ENDPOINT', '*.COM');
!defined('OSS_TEST_BUCKET') && define('OSS_TEST_BUCKET', 'NAME');

/**
 * 极光推送
 */
!defined('JPUSH_APP_KEY') && define('JPUSH_APP_KEY', '123456789');
!defined('JPUSH_MASTER_SECRET') && define('JPUSH_MASTER_SECRET', '987654321');

/**
 * WebScoket Config
 */ 
!defined('WEBSCOKET_ENABLED') && define('WEBSCOKET_ENABLED', false);
!defined('WEBSCOKET_HOST') && define('WEBSCOKET_HOST', '127.0.0.1');
!defined('WEBSCOKET_PORT') && define('WEBSCOKET_PORT', '9502');

/**
 * 设备来源
 */
$devices = array('phone', 'tablet', 'desktop', 'android', 'apple');

if (!empty($_SERVER['HTTP_X_DEVICE']) && in_array($_SERVER['HTTP_X_DEVICE'], $devices)) {
    $device = $_SERVER['HTTP_X_DEVICE'];
} elseif (isset($_GET['device']) && in_array($_GET['device'], $devices)) {
    $device = $_GET['device'];
} elseif (isset($_SERVER['HTTP_USER_AGENT'])) {
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/ipad|tablet|kindle/i', $ua)) {
        $device = 'tablet';
    } elseif (preg_match('/mobile/i', $ua)) {
        $device = 'phone';
    } elseif (preg_match('/android/i', $ua)) {
        $device = 'tablet';
    } elseif (preg_match('/phone|pod/i', $ua)) {
        $device = 'phone';
    } elseif (preg_match('/windows nt/i', $ua) && preg_match('/touch/i', $ua)) {
        $device = 'tablet';
    } elseif (preg_match('/pad/i', $ua)) {
        $device = 'tablet';
    } else {
    	$device = "desktop";
    }
} else {
    $device = "desktop";
}
!defined('DEVICE') && define('DEVICE', $device);
