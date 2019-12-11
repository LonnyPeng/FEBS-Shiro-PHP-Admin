<?php
 
namespace app\push\controller;

use JPush\Exceptions\JPushException;
 
class JpushClient
{
	const APP_KEY = JPUSH_APP_KEY;
	const MASTER_SECRET = JPUSH_MASTER_SECRET;

	public static function run()
	{
		$client = new \JPush(self::APP_KEY, self::MASTER_SECRET);

		return true;
	}
}