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

use app\lonny\controller\Plugin\Func;
use think\Session;
use app\admin\model\Role AS RoleModel;
use app\admin\model\Admin AS AdminModel;
use think\Db;

class Chat extends Common 
{
    public function init()
    {
        $field = 'id, name AS groupname';
        $group = collection(RoleModel::field($field)->select())->getAll();

        $friend = array();
        foreach ($group as $key => $row) {
            $row['avatar'] = WEB_HOST . WEB_BASE . "web/img/default.jpg";

            $uids = collection(Db::table('t_admin_role')->field('uid')->where('role_id', $row['id'])->select())->getColumn();
            if ($uids) {
                $field = 'id, nickname, username, headimg, note';
                $where = array(
                    'status' => 1,
                    sprintf("id <> %d", Session::get('user_auth.uid')),
                    sprintf("id IN(%s)", implode(",", $uids)),
                );
                $re = collection(AdminModel::field($field)->where($where)->select())->getAll();
                if ($re) {
                    $friend_row = array(
                        'groupname' => $row['groupname'],
                        'id' => $row['id'],
                        'list' => array(),
                    );

                    foreach ($re as $ke => $r) {
                        $l = array(
                            "username" => $r['nickname'] ?: $r['username'],
                            "id"       => $r['id'],
                            "avatar"   => $r['headimg'] ?: WEB_HOST . WEB_BASE . "web/img/default.jpg",
                            "sign"     => $r['note'],
                        );

                        $friend_row['list'][] = $l;
                    }

                    $friend[] = $friend_row;
                }
            }

            $group[$key] = $row;
        }

        $data = array(
        	'code' => 200,
        	'message' => "",
        	'data' => array(
        		'mine' => array(
        			'username' => Session::get('user_auth.nickname'),
        			'id' => Session::get('user_auth.uid'),
        			'status' => "online",
        			'sign' => Session::get('user_auth.note'),
        			'avatar' => Session::get('user_auth.headimg'),
        		),
        		'friend' => $friend,
        		'group' => $group,
        	),
        );

        exit(Func::appJson($data));

        return false;
    }
}