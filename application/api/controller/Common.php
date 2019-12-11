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

namespace app\api\controller;

use app\lonny\controller\Common AS Com;
use app\lonny\controller\Plugin\Func;

/**
 * @SWG\Swagger(
 *     swagger="2.0",
 *     schemes={"http"},
 *     host="www.tp5.com",
 *     basePath="/api",
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="Api 文档",
 *         description="Api 文档"
 *     ),
 *     @SWG\Tag(
 *     	   name="Index",
 *     	   description="Home Page",
 *     ),
 *     @SWG\Tag(
 *     		name="User",
 *     		description="User"
 *     )
 * )
 */
class Common extends Com
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * [echoRsa 统一返回接口]
	 */
	public function echoRsa($data = [])
	{
		if (RSA_ENABLED && in_array(DEVICE, ['android', 'apple'])) {
			\think\Config::set([
				'default_return_type'    => 'html',
			]);

			return $this->RSA->encryptByPrivateKey(Func::appJson($data));
		} else {
			return $data;
		}
	}

	/**
	 * [echoMcrypt 统一返回接口]
	 */
	public function echoMcrypt($data = [])
	{
		if (MCRYPT_ENABLED && in_array(DEVICE, ['android', 'apple'])) {
			\think\Config::set([
				'default_return_type'    => 'html',
			]);

			return $this->Mcrypt->encrypt(Func::appJson($data));
		} else {
			return $data;
		}
	}
}