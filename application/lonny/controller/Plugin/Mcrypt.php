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

/**
 * mcrypt 加密
 */
class Mcrypt
{
	/**
	 * [encrypt 加密]
	 * @Author   Lonny
	 * @Email    lonnypeng@baogongpo.com
	 * @DateTime 2019-10-31
	 * @param    [type]                  $str [description]
	 * @return   [type]                         [description]
	 */
	public static function encrypt($str = '')
	{
	    $size  = mcrypt_get_block_size('des', 'ecb');
	    $str = self::pkcs5_pad($str, $size);

	    $key = TOKEN;
	    $td = mcrypt_module_open('des', '', 'ecb', '');
	    $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

	    @mcrypt_generic_init($td, $key, $iv);

	    $data = mcrypt_generic($td, $str);

	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);

	    $data = base64_encode($data);

	    return $data;
	}

	/**
	 * [decrypt 解密]
	 * @Author   Lonny
	 * @Email    lonnypeng@baogongpo.com
	 * @DateTime 2019-10-31
	 * @param    [type]                  $encrypted [description]
	 * @return   [type]                             [description]
	 */
	public static function decrypt($encrypted = '')
	{
	    $encrypted = base64_decode($encrypted);
	    
	    $key = TOKEN;
	    $td = mcrypt_module_open('des', '', 'ecb', '');

	    //使用MCRYPT_DES算法,cbc模式
	    $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	    $ks = mcrypt_enc_get_key_size($td);

	    @mcrypt_generic_init($td, $key, $iv);

	    //初始处理
	    $decrypted = mdecrypt_generic($td, $encrypted);

	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);

	    $y = self::pkcs5_unpad($decrypted);

	    return $y;
	}

	public static function pkcs5_pad($text = '', $blocksize = '')
	{
	    $pad = $blocksize - (strlen($text) % $blocksize);

	    return $text . str_repeat(chr($pad), $pad);
	}

	public static function pkcs5_unpad($text = '')
	{
	    $pad = ord($text{strlen($text) - 1});

	    if ($pad > strlen($text)) {
	        return false;
	    }

	    if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
	        return false;
	    }

	    return substr($text, 0, -1 * $pad);
	}
}