<?php

use app\admin\controller\Plugin\Perm;

if (!function_exists('image')) {
	function image($str = '', $base = 'static') {
		$str = (string) $str;

		if (!$str) {
			return false;
		}

		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/";
		$image_dir = $public_dir . $web_dir;

		if (!file_exists($image_dir . $str)) {
			return false;
		}

		$str = WEB_HOST . WEB_BASE . $web_dir . $str;
		if (WEB_ENABLED) {
			$str .= "?v=" . WEB_VERSION;
		}

		return $str;
	}
}

if (!function_exists('css')) {
	function css($data = [], $base = 'static') {
		$data = (array) $data;

		if (!$data) {
			return '';
		}

		$csss = [];
		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/";
		$css_dir = $public_dir . $web_dir;

		foreach ($data as $value) {
			$value = explode(",", $value);
			foreach ($value as $val) {
				if (!preg_match("/\.css$/i", $val)) {
					$val .= ".css";
				}

				if (!file_exists($css_dir . $val)) {
					continue;
				}

				$href = WEB_HOST . WEB_BASE . $web_dir . $val;
				if (WEB_ENABLED) {
					$href .= "?v=" . WEB_VERSION;
				}

				$csss[] = sprintf('<link rel="stylesheet" type="text/css" href="%s" charset="UTF-8" />', $href);
			}
		}

		return implode(PHP_EOL, $csss) . PHP_EOL;
	}
}

if (!function_exists('js')) {
	function js($data = [], $base = 'static') {
		$data = (array) $data;

		if (!$data) {
			return '';
		}

		$jss = [];
		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/";
		$js_dir = $public_dir . $web_dir;

		foreach ($data as $value) {
			$value = explode(",", $value);
			foreach ($value as $val) {
				if (!preg_match("/\.js$/i", $val)) {
					$val .= ".js";
				}

				if (!file_exists($js_dir . $val)) {
					continue;
				}

				$src = WEB_HOST . WEB_BASE . $web_dir . $val;
				if (WEB_ENABLED) {
					$src .= "?v=" . WEB_VERSION;
				}

				$jss[] = sprintf('<script type="text/javascript" src="%s" charset="UTF-8" /></script>', $src);
			}
		}

		return implode(PHP_EOL, $jss) . PHP_EOL;
	}
}

if (!function_exists('get_client_ip')) {
	/**
	 * 获取客户端IP地址
	 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
	 * @return mixed
	 */
	function get_client_ip($type = 0) {
	    $type       =  $type ? 1 : 0;
	    static $ip  =   NULL;
	    if ($ip !== NULL) {
	    	return $ip[$type];
	    } 

	    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	        $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
	        $pos = array_search('unknown', $arr);
	        if(false !== $pos) {
	        	unset($arr[$pos]);
	        } 

	        $ip = trim($arr[0]);
	    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
	        $ip = $_SERVER['HTTP_CLIENT_IP'];
	    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
	        $ip = $_SERVER['REMOTE_ADDR'];
	    }

	    // IP地址合法验证
	    $long = sprintf("%u",ip2long($ip));
	    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);

	    return $ip[$type];
	}
}

if (!function_exists('has_perm')) {
	function has_perm($str = '') {
		return Perm::has($str);
	}
}
