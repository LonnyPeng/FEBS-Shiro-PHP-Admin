<?php
/**
 *                    _ooOoo_
 *                   o8888888o
 *                   88" . "88
 *                   (| -_- |)
 *                   O\  =  /O
 *                ____/`---'\____
 *              .'  \\|     |//  `.
 *             /  \\|||  :  |||//  \
 *            /  _||||| -:- |||||-  \
 *            |   | \\\  -  /// |   |
 *            | \_|  ''\---/''  |   |
 *            \  .-\__  `-`  ___/-. /
 *          ___`. .'  /--.--\  `. . __
 *       ."" '<  `.___\_<|>_/___.'  >'"".
 *      | | :  `- \`.;`\ _ /`;.`/ - ` : | |
 *      \  \ `-.   \_ __\ /__ _/   .-` /  /
 * ======`-.____`-.___\_____/___.-`____.-'======
 *                    `=---='
 * ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
 *          佛祖保佑       永无BUG
 */

namespace app\admin\controller;

use app\lonny\controller\Plugin\Func;

class Alioss extends Common 
{
    public function policy()
    {
    	$expire = time() + 3600;
    	$policyText = array(
    		"expiration" => date("Y-m-d\TH:i:s\.000\Z", $expire), //设置该Policy的失效时间
    		"conditions" => array(
    			array("content-length-range", 0, 1048576000), // 设置上传文件的大小限制
    		),
    	);

    	$policy = base64_encode(json_encode($policyText));
    	$signature = base64_encode(hash_hmac('sha1', $policy, OSS_ACCESS_KEY, true));

    	$result = array(
    		'code' => 200,
    		'data' => array(
    			'accessid' => OSS_ACCESS_ID,
    			'host' => OSS_ENDPOINT,
    			'policy' => $policy,
    			'signature' => $signature,
    			'expire' => $expire,
    		),
    	);

    	exit(Func::appJson($result));

    	return false;
    }
}