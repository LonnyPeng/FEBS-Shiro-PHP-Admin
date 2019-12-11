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

namespace app\admin\controller\Plugin;

use app\lonny\controller\Plugin\Http;
use think\Session;
use app\lonny\controller\Plugin\Func;

class Perm 
{
    public static function check($permission = "")
    {
    	$data = explode(",", $permission);
    	if ($data) {
    		$permissionSet = Session::get('permissionSet');

    		$has_arr = array();
    		foreach ($data as $value) {
    			if (in_array($value, $permissionSet)) {
    				$has_arr[] = 1;
    			}
    		}

    		if (array_sum($has_arr) > 0) {
    			return true;
    		}
    	}

    	if (Func::isAjax() || Func::isPost()) {
    		$result = array(
    			'code' => 403,
    			'message' => "对不起，您暂无该操作权限。",
    		);

    		exit(Func::appJson($result));
    	} else {
    		Http::headerStatus(301);
    		exit("对不起，您暂无该操作权限。");
    	}
    }
    
    public static function has($permission = "")
    {
        $data = explode(",", $permission);
        if (!$data) {
        	return false;
        }

        $permissionSet = Session::get('permissionSet');

        $has_arr = array();
        foreach ($data as $value) {
        	if (in_array($value, $permissionSet)) {
        		$has_arr[] = 1;
        	}
        }

        return array_sum($has_arr) > 0;
    }
}