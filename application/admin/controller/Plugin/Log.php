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

use app\admin\model\Log AS LogModel;
use app\lonny\controller\Plugin\Func;
use think\Request;
use think\Session;

class Log
{
    const VIEW   = 'view';
    const ADD    = 'add';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const IMPORT = 'import';
    const EXPORT = 'export';

    protected static $map = array(
        'add', 'update', 'delete', 
        'view', 'import', 'export',
    );

    public static function run($action = '', $params = null)
    {
        if (!in_array($action, self::$map)) {
            return false;
        }

        if (!$params) {
            $params = input("param.");
        }

        $data = array(
            "username" => Session::get('user_auth.username'),
            "operation" => $action,
            "method" => Request::instance()->baseUrl(),
            "params" => Func::appJson($params),
            "ip" => get_client_ip(),
            "createtime" => time(),
            "location" => "",
        );
        
        return LogModel::insert($data);
    }
}