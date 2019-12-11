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
use OSS\OssClient;

class Upload extends Images 
{
    public function index()
    {
    	$result = array('code' => 100, 'message' => '', 'data' => array());

    	if (!isset($_FILES['file'])) {
    		$result['message'] = 'file 不能为空';
    		exit(Func::appJson($result));
    	}

    	$file = $_FILES['file'];

    	if ($file['error']) {
    		$result['message']= $this->imgError[$file['error']];
    		exit(Func::appJson($result));
    	} elseif (!in_array($file['type'], $this->imgType)) {
    		$result['message']= sprintf('格式错误，请选择 %s', implode("、", array_keys($this->imgType)));
    		exit(Func::appJson($result));
    	} elseif ($file['size'] < $this->imgSize['min'] || $file['size'] > $this->imgSize['max']) {
    		$result['message']= sprintf('大小错误（最小：%s; 最大：%s ）', Func::convert($this->imgSize['min']), Func::convert($this->imgSize['max']));
    		exit(Func::appJson($result));
    	}

    	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    	$dirFile = sprintf("Tmp/%s.%s", date("Y/m/d/") . md5(microtime() . $file['name']), $ext);

    	$ossClient = new OssClient(OSS_ACCESS_ID, OSS_ACCESS_KEY, OSS_ENDPOINT);

    	try {
    		$ossClient->uploadFile(OSS_TEST_BUCKET, $dirFile, $file['tmp_name']);
    	} catch(OssException $e) {
    		$result['message'] = $e->getMessage();
    		exit(Func::appJson($result));
    	}

    	$url = sprintf("https://%s.%s/%s", OSS_TEST_BUCKET, OSS_ENDPOINT, $dirFile);

    	$result['code'] = 200;
    	$result['data'] = $url;

    	exit(Func::appJson($result));

    	return false;
    }
}