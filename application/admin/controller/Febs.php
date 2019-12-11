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

use think\Session;
use app\admin\model\Admin AS AdminModel;
use app\admin\model\Menu AS MenuModel;
use think\Db;
use app\lonny\controller\Plugin\Http;

class Febs extends Common 
{
    public function index()
    {
        $info = collection(AdminModel::field('*')->where('id', $this->uid)->select())->getRow();
        $config = json_decode($info['config'], true);

        $where = array('ar.uid' => $this->uid);
        $join = array(
            array("t_role r", "r.id = ar.role_id", "LEFT"),
        );

        $roles = collection(Db::table('t_admin_role')->alias('ar')->field('r.id,r.name')->join($join)->where($where)->select())->getPairs();

        $permissionSet = array();
        $memuIds = array();

        if (in_array(Session::get('user_auth.username'), array('admin'))) {
            $where = array(
                'status' => 1
            );

            $data = collection(MenuModel::field('id,perms')->where($where)->select())->getAll();
        } else {
            $where = array();

            if ($roles) {
                $where[] = sprintf("rm.role_id IN(%s)", implode(",", array_keys($roles)));
            } else {
                $where[] = '1=0';
            }

            $join = array(
                array("t_menu m", "m.id = rm.menu_id", "LEFT"),
            );

            $data = collection(Db::table('t_role_menu')->alias('rm')->field('m.id,m.perms')->join($join)->where($where)->select())->getAll();
        }

        $permissionSet = array_column($data, 'perms');
        $permissionSet = array_unique($permissionSet);
        $permissionSet = array_filter($permissionSet);
        $permissionSet = array_values($permissionSet);

        $memuIds = array_column($data, 'id');
        $memuIds = array_unique($memuIds);
        $memuIds = array_filter($memuIds);
        $memuIds = array_values($memuIds);
        if (!$memuIds) {
            Http::headerStatus(403);
        }

    	$data = array(
            "userId"        => $info['id'],
            "username"      => $info['username'],
            "nickname"      => $info['nickname'],
            "realname"      => $info['realname'],
            "idcard"        => $info['idcard'],
            "headimg"       => $info['headimg'] ?: WEB_HOST . WEB_BASE . "web/img/default.jpg",
            "email"         => $info['email'],
            "mobile"        => $info['phone'],
            "location1"     => $info['location1'],
            "location2"     => $info['location2'],
            "location3"     => $info['location3'],
            "address"       => $info['address'],
            "note"          => $info['note'],
            "createTime"    => $info['createtime'] ? date("Y-m-d H:i:s", $info['createtime']) : '',
            "modifyTime"    => $info['updatetime'] ? date("Y-m-d H:i:s", $info['updatetime']) : '',
            "lastLoginTime" => $info['updatetime'] ? date("Y年m月d日 H时i分s秒", $info['updatetime']) : '',
            "sex"           => (string) $info['sex'],
            "theme"         => isset($config['theme']) ? $config['theme'] : "white",
            "isTab"         => isset($config['isTab']) ? $config['isTab'] : "1",
            "roleId"        => implode(",", array_keys($roles)),
            "roleName"      => implode(",", $roles),
            "id"            => $info['id'],
            "roleSet"       => array_values($roles),
            "permissionSet" => $permissionSet,
    	);

        Session::set('permissionSet', $data['permissionSet']);
        Session::set('memuIds', $memuIds);

        if (ADMIN_USER_ENABLED) {
            setcookie('PHPSESSID', session_id(), time() + ADMIN_USER_TIME, '/');
        }

        $ossConfig = array(
            'region' => OSS_ENDPOINT,
            'bucket' => OSS_TEST_BUCKET,
        );

        return $this->fetch('', array('data' => json_encode($data), 'ossConfig' => json_encode($ossConfig)));
    }
}