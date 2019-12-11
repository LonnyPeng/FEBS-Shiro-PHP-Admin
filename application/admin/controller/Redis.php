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

use app\lonny\controller\Plugin\Redis AS RedisService;
use app\lonny\controller\Plugin\Func;
use app\admin\controller\Plugin\Perm;

class Redis extends Common 
{
	public $Redis = NULL;

	public function __construct()
	{
		parent::__construct();

        Perm::check("redis:terminal:view");

		$this->Redis = RedisService::init();
		if (!$this->Redis) {
			$result = array(
				'code' => 200,
				'data' => "Redis终端登录失败"
			);
			exit(Func::appJson($result));
		}
	}

    public function keys()
    {
    	$arg = $this->param('arg');

    	$data = $this->Redis->keys($arg);

    	$result = array(
    		'code' => 200,
    		'data' => $data,
    	);

    	exit(Func::appJson($result));

    	return false;
    }

    public function get()
    {
    	$arg = $this->param('arg');

    	$data = $this->Redis->get($arg);

    	$result = array(
    		'code' => 200,
    		'data' => $data,
    	);

    	exit(Func::appJson($result));

    	return false;
    }

    public function set()
    {
    	$result = array('code' => 200, 'data' => '');

    	$args = explode(",", $this->param('arg'));
    	$args = array_filter($args);

    	if (count($args) != 2) {
    		$result['data'] = "参数错误";
    		exit(Func::appJson($result));
    	}

    	$status = $this->Redis->set($args[0], $args[1]);
    	
    	if ($status) {
    		$result['data'] = "OK";
    	} else {
    		$result['data'] = "ERROR";
    	}

    	exit(Func::appJson($result));

    	return false;
    }

    public function del()
    {
    	$result = array('code' => 200, 'data' => '');

    	$arg = $this->param('arg');

    	$status = $this->Redis->del($arg);
    	if ($status) {
    		$result['data'] = "OK";
    	} else {
    		$result['data'] = "ERROR";
    	}

    	exit(Func::appJson($result));

    	return false;
    }

    public function exists()
    {
    	$result = array('code' => 200, 'data' => '');

    	$arg = $this->param('arg');

    	$status = $this->Redis->exists($arg);
    	if ($status) {
    		$result['data'] = "TRUE";
    	} else {
    		$result['data'] = "FALSE";
    	}

    	exit(Func::appJson($result));

    	return false;
    }

    public function pttl()
    {
    	$result = array('code' => 200, 'data' => '');

    	$arg = $this->param('arg');

    	$result['data'] = $this->Redis->pttl($arg);

    	exit(Func::appJson($result));

    	return false;
    }

    public function pexpire()
    {
    	$result = array('code' => 200, 'data' => '');

    	$args = explode(",", $this->param('arg'));
    	$args = array_filter($args);

    	if (count($args) != 2) {
    		$result['data'] = "参数错误";
    		exit(Func::appJson($result));
    	}

    	$status = $this->Redis->pexpire($args[0], $args[1]);
    	
    	if ($status) {
    		$result['data'] = "OK";
    	} else {
    		$result['data'] = "ERROR";
    	}

    	exit(Func::appJson($result));

    	return false;
    }
}