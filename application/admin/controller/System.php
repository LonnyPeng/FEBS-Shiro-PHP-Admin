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

use app\lonny\controller\Plugin\Http;
use app\admin\model\Admin AS AdminModel;
use think\Db;
use app\lonny\controller\Plugin\Func;

class System extends Common 
{
    public function user()
    {
        return $this->fetch();
    }

    public function password()
    {
        return $this->fetch();
    }

    public function userAdd()
    {
        return $this->fetch();
    }

    public function userDetail()
    {
        $id = $this->param('id');
        if (!$id) {
            Http::headerStatus('412');
        }

        $info = AdminModel::detail($id);
        if (!$info) {
            Http::headerStatus('404');
        }

        return $this->fetch('', $info);
    }

    public function userUpdate()
    {
        $id = $this->param('id');
        if (!$id) {
            Http::headerStatus('412');
        }

        $where = array('id' => $id);
        $field = array(
            'id,username,nickname,realname,idcard,sex,phone,email',
            'location1,location2,location3,location4,location5,address',
            'note,status',
        );

        $info = collection(AdminModel::field($field)->where($where)->select())->getRow();
        if (!$info) {
            Http::headerStatus('404');
        }

        $roleId = collection(Db::table('t_admin_role')->field('role_id')->where('uid', $info['id'])->select())->getColumn();

        $info['roleId'] = implode(",", $roleId);

        return $this->fetch('', array('user' => Func::appJson($info)));
    }

    public function role()
    {
        return $this->fetch();
    }

    public function menu()
    {
        return $this->fetch();
    }
}