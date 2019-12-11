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
use app\admin\model\Word AS WordModel;

class Word extends Common 
{
    public function index()
    {
    	$where = array('status' => 1);

    	$first = $this->param('first');
    	if ($first) {
    		$where['first'] = $first;
    	}

    	$field = "fayin,first,value";
    	$order = "CONVERT(value USING GBK) ASC";
    	$data = collection(WordModel::field($field)->where($where)->orderRaw($order)->select())->getAll();

    	exit(Func::appJson($data));

    	return false;
    }
}