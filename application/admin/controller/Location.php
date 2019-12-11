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

use think\Db;
use app\lonny\controller\Plugin\Func;

class Location extends Common 
{
    public function index()
    {
    	$type = $this->param('type');
    	if (!$type || !in_array($type, array(1, 2, 3, 4, 5))) {
    		$type = 1;
    	}

    	$table = "t_location{$type}";
    	$field = 'id,name';
    	$where = array('status' => 1);

    	if ($type != 1) {
    		$fid = $this->param("fid");
    		if ($fid) {
    			$where[sprintf("location%d", $type - 1)] = $fid;
    		} else {
    			$where[] = "1=0";
    		}
    	}

    	$order = "CONVERT(name USING GBK) ASC";
    	$data = collection(Db::table($table)->field($field)->where($where)->orderRaw($order)->select())->getAll();

    	exit(Func::appJson($data));

    	return false;
    }

    public static function name($data = array())
    {
        if (is_string($data)) {
            $data = explode(",", $data);
        }

        $data = array_filter($data);

        if (!$data) {
            return array();
        }

        $result = array();
        foreach ($data as $key => $value) {
            $where = array('id' => $value);
            $table = sprintf("t_location%d", $key + 1);
            $result[] = collection(Db::table($table)->field('name')->where($where)->select())->getOne();
        }

        return $result;
    }
}