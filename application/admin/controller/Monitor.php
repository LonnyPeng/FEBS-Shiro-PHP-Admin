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
use app\lonny\controller\Plugin\Redis AS RedisService;

class Monitor extends Common 
{
    public function log()
    {
        return $this->fetch();
    }

    public function loginLog()
    {
        return $this->fetch();
    }

    public function redisTerminal()
    {
    	$osName = PHP_OS;

    	return $this->fetch('', array('osName' => $osName));
    }

    public function server()
    {
        $mysql = Db::query("SELECT VERSION()");
        $redis = RedisService::init();
        if ($redis) {
            $redis = $redis->info();
            $redis = $redis['redis_version'];
        }
        
    	$data = array(
    		'Os' => array('name' => 'Os', 'title' => "系统名称", 'value' => PHP_OS),
    		'PHP' => array('name' => 'PHP', 'title' => 'PHP版本', 'value' => PHP_VERSION),
    		'Mysql' => array('name' => 'Mysql', 'title' => 'Mysql版本', 'value' => reset($mysql[0])),
    		'Redis' => array('name' => 'Redis', 'title' => 'Redis版本', 'value' => $redis),
    	);

    	return $this->fetch('', $data);
    }
}