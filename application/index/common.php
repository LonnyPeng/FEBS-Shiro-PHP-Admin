<?php

if (!function_exists('image')) {
	function image($str = '', $base = 'web') {
		$str = (string) $str;

		if (!$str) {
			return false;
		}

		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/img/";
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
	function css($data = [], $base = 'web') {
		$data = (array) $data;

		if (!$data) {
			return '';
		}

		$csss = [];
		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/css/";
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
	function js($data = [], $base = 'web') {
		$data = (array) $data;

		if (!$data) {
			return '';
		}

		$jss = [];
		$public_dir = ROOT . "public/";
		$web_dir = "{$base}/js/";
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
