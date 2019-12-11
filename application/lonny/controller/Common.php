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

namespace app\lonny\controller;

use \think\Controller;
use \Swagger\Annotations as SWG;
use app\lonny\controller\Plugin\Curl;
use app\lonny\controller\Plugin\Func;
use app\lonny\controller\Plugin\Http;
use app\lonny\controller\Plugin\PHPExcel;
use app\lonny\controller\Plugin\Latlng;

class Common extends Controller
{
	public $Db = NULL;
	public $Redis = NULL;
	public $RSA = NULL;
	public $Mcrypt = NULL;

	public function __construct()
	{
		parent::__construct();

		$this->Db = new Plugin\Db();
		$this->Redis = new Plugin\Redis();
		$this->RSA = new Plugin\RSA();
		$this->Mcrypt = new Plugin\Mcrypt();
	}

	public function param($name = '')
	{
		return Func::param($name);
	}

	public function paramAll()
	{
		return input("param.");
	}
}