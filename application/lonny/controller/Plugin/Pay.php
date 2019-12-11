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

namespace app\lonny\controller\Plugin;

use Yansongda\Pay\Pay AS YPay;
use think\Url;

class Pay
{
	protected static $config = array(
		'alipay' => array(
			'app_id'         => '201608xxxxxxxxxxx295641', //支付宝提供的 APP_ID
			'ali_public_key' => 'MIIEpAIBAAKCAQEAs6+F2leO', //支付宝公钥
			'private_key'    => '3G6UXlrIEAQ==', //自己的私钥
			'notify_url'     => '', //异步通知
			'return_url'     => '', //同步通知
		),
		'wechat' => array(
			'app_id'      => 'wx7xxxxxxxxxxxa38', //公众号APPID
			'mch_id'      => '15xxxxxxxx441', //微信商户号
			'key'         => 'e10adc3xxxxxxxxxxxxxxxe057f20f883e', //微信支付签名秘钥
			'notify_url'  => '', //异步通知
			'return_url'  => '', //同步通知
			'cert_client' => '', //客户端证书路径
			'cert_key'    => '', //客户端秘钥路径
		),
	);

	protected static $gatewayMap = array(
		'alipay' => array(
			'web' , //电脑支付
			'wap' , //手机网站支付
			'app' , //APP 支付
			'pos' , //刷卡支付
			'scan' , //扫码支付
			'transfer' , //帐户转账（可用于平台用户提现）
		),
		'wechat' => array(
			'mp', //公众号支付
			'miniapp', //小程序支付
			'wap', //H5 支付
			'scan', //扫码支付
			'pos', //刷卡支付
			'app', //APP 支付
			'transfer', //企业付款
			'groupredpack', //发放裂变红包
			'redpack', //发放普通红包
		),
	);

	public static function run($driver = 'wechat', $gateway = 'wap')
	{
		if (!isset(self::$config[$driver])) {
			$driver = 'wechat';
		}

		if (!in_array($gateway, self::$gatewayMap[$driver])) {
			$gateway = reset(self::$gatewayMap[$driver]);
		}

		$param = array(
			'driver' => $driver, 
			'gateway' => $gateway
		);

		self::$config[$driver]['notify_url']   = Url::build('Pay/notifyUrl', $param, '', true);
		self::$config[$driver]['return_url']   = Url::build('Pay/returnUrl', $param, '', true);
		self::$config['wechat']['cert_client'] = ROOT . 'pem/apiclient_cert.pem'; //客户端证书路径
		self::$config['wechat']['cert_key']    = ROOT . 'pem/apiclient_key.pem'; //客户端证书路径

		$pay = new YPay(self::$config);

		return $pay->driver($driver)->gateway($gateway);
	}

	public static function index($data = array(), $driver = 'wechat', $gateway = 'wap')
	{
		$pay = self::run($driver = 'wechat', $gateway = 'wap');

		return $pay->pay($data);
	}

	public static function returnUrl($request = array(), $driver = 'wechat', $gateway = 'wap')
	{
		$pay = self::run($driver = 'wechat', $gateway = 'wap');

	    return $pay->verify($request->all());
	}

	public static function notifyUrl($request = array(), $driver = 'wechat', $gateway = 'wap') 
	{
		$pay = self::run($driver = 'wechat', $gateway = 'wap');

        if ($pay->verify($request->all())) {
            print_r($request);die;
        } else {
            //
        }

        exit("success");
    }
}