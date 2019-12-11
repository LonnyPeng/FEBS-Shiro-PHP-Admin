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

use \app\lonny\controller\Common AS Com;
use app\admin\model\Admin AS AdminModel;
use think\Loader;
use think\Session;

class Common extends Com
{
	public $uid = 0;

	public function __construct()
	{
		parent::__construct();

		$controllername = Loader::parseName($this->request->controller());
		$actionname = strtolower($this->request->action());

		if ($controllername == 'user' && in_array($actionname, array('login', 'regist'))) {
			if ($actionname == 'login') {
				if (AdminModel::isLogin()) {
					$this->uid = Session::get('user_auth.uid');

					$this->redirect('febs/index');
				}
			}
		} else {
			if (!AdminModel::isLogin()) {
				$this->redirect('user/login');
			} else {
				$this->uid = Session::get('user_auth.uid');
			}
		}
	}
}