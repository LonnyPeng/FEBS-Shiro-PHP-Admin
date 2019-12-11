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
use app\admin\model\Admin AS AdminModel;
use think\Session;
use think\Db;
use app\lonny\controller\Plugin\PHPExcel;
use app\admin\controller\Plugin\Perm;
use app\admin\controller\Plugin\Log;
use think\Url;

class User extends Common 
{
	public function __construct()
	{
		parent::__construct();
	}

    public function login()
    {
    	if (Func::isAjax()) {
    		$result = AdminModel::login([
    			'username' => Func::param('username'),
    			'password' => $this->RSA->decryptByPrivateKey(Func::param('password'), true),
    		]);

    		exit(Func::appJson($result));
    	} else {
    		return $this->fetch();
    	}
    }

    public function regist()
    {
        $username = $this->param('username');
        $password = $this->param('password');

        $result = AdminModel::regist($username, $this->RSA->decryptByPrivateKey($password, true));
        exit(Func::appJson($result));

        return false;
    }

    public function info()
    {
        $result = array(
            'code' => 200,
            'data' => Session::get('user_auth'),
        );
        exit(Func::appJson($result));

        return false;
    }

    public function logout()
    {
        AdminModel::logout();

        $this->redirect('user/login');

        return false;
    }

    public function table($export = false)
    {
        Perm::check("user:view");

        $where = array(
            sprintf("status IN(%s)", implode(",", array_keys(AdminModel::$statusMap))),
        );

        $username = $this->param('username');
        if ($username) {
            $where[] = sprintf("username LIKE '%%%s%%'", $username);
        }

        $phone = $this->param('phone');
        if ($phone) {
            $where[] = sprintf("phone LIKE '%%%s%%'", $phone);
        }

        $sex = $this->param('sex');
        if ($sex !== "") {
            $where['sex'] = $sex;
        }

        $status = $this->param('status');
        if ($status !== "") {
            $where['status'] = $status;
        }

        $createTimeFrom = $this->param('createTimeFrom');
        if ($createTimeFrom) {
            $where[] = sprintf("createtime >= %d", strtotime(date("Y-m-d 00:00:00", strtotime($createTimeFrom))));
        }
        $createTimeTo = $this->param('createTimeTo');
        if ($createTimeTo) {
            $where[] = sprintf("createtime <= %d", strtotime(date("Y-m-d 23:59:59", strtotime($createTimeTo))));
        }

        $roleId = $this->param('roleId');
        if ($roleId) {
            $uids = collection(Db::table('t_admin_role')->field('uid')->where('role_id', '=', $roleId)->select())->getColumn();
            if ($uids) {
                $where[] = sprintf("id IN(%s)", implode(",", $uids));
            } else {
                $where[] = "1=0";
            }
        }

        $field = array(
            'id,username,nickname,realname,idcard,sex,phone,email',
            'location1,location2,location3,location4,location5,address',
            'note,status,createtime,logintime,updatetime',
        );

        $orders = array();
        $order = $this->param('order');
        $orderField = $this->param('field');
        if ($order && $orderField) {
            $orders[] = sprintf("%s %s", $orderField, strtoupper($order));

        }
        $orders[] = 'id DESC';
        $order = implode(",", $orders);

        if ($export) {
            $limit = '';
        } else {
            $pageNum = $this->param('pageNum') ?: 1;
            $pageSize = $this->param('pageSize') ?: 20;

            $limit = sprintf("%d, %d", ($pageNum - 1) * $pageSize, $pageSize);
        }

        $total = collection(AdminModel::field('COUNT(*)')->where($where)->select())->getOne();

        $data = collection(AdminModel::field($field)->where($where)->order($order)->limit($limit)->select())->getAll();

        foreach ($data as $key => $row) {
            foreach ($row as $ke => $value) {
                $row[$ke] = $value === null ? "" : $value;
            }

            $row['createtime'] = $row['createtime'] ? date("Y-m-d H:i:s", $row['createtime']) : '';
            $row['logintime'] = $row['logintime'] ? date("Y-m-d H:i:s", $row['logintime']) : '从未登录过系统';
            $row['updatetime'] = $row['updatetime'] ? date("Y-m-d H:i:s", $row['updatetime']) : '';
            $row['roleName'] = implode(",", AdminModel::roleName($row['id']));
            $row['sexName'] = AdminModel::$sexMap[$row['sex']];
            $row['statusName'] = AdminModel::$statusMap[$row['sex']];

            $address = Location::name(array(
                $row['location1'], $row['location2'], $row['location3'],
                $row['location4'], $row['location5'],
            ));
            $row['addressName'] = implode(" ", $address) . " " . $row['address'];

            $data[$key] = $row;
        }

        if ($export) {
            return $data;
        }

        $result = array(
            'code' => "200",
            'data' => array(
                'total' => $total,
                'rows' => $data,
            ),
        );

        exit(Func::appJson($result));

        return false;
    }

    public function excel()
    {
        Perm::check("user:export");

        $title = array(
            'username'    => "用户名",
            'nickname'    => "昵称",
            'sexName'     => "性别",
            'realname'    => "真实姓名",
            'idcard'      => "身份证",
            'roleName'    => "角色",
            'phone'       => "手机",
            'email'       => "邮箱",
            'addressName' => "地址",
            'note'        => "备注",
            'statusName'  => "状态",
            'logintime'   => "最后登录时间",
            'updatetime'  => "最后修改时间",
            'createtime'  => "创建时间",
        );

        PHPExcel::export($title, $this->table(true));

        return false;
    }

    public function add()
    {
        Perm::check("user:add");
        
        $result = AdminModel::add($this->paramAll());
        if ($result['code'] == 200) {
            $uid = $result['data']['uid'];

            $roleId = explode(",", $this->param('roleId'));
            $roleId = array_unique($roleId);
            $roleId = array_filter($roleId);
            if ($roleId) {
                foreach ($roleId as $rId) {
                    $data = array(
                        'uid' => $uid,
                        'role_id' => $rId,
                    );

                    Db::table('t_admin_role')->insert($data);
                }
            }

            Log::run(Log::ADD);
        }

        exit(Func::appJson($result));

        return false;
    }

    public function update()
    {
        Perm::check("user:update");

        $result = array('code' => 100, 'message' => '');

        $id = $this->param('id');
        if (!$id) {
            $result['message'] = "ID 不能为空";
            exit(Func::appJson($result));
        }

        $nickname  = $this->param('nickname');
        $realname  = $this->param('realname');
        $idcard    = $this->param('idcard');
        $phone     = $this->param('phone');
        $email     = $this->param('email');
        $status    = $this->param('status');
        $sex       = $this->param('sex');
        $location1 = $this->param('location1');
        $location2 = $this->param('location2');
        $location3 = $this->param('location3');
        $address   = $this->param('address');
        $note      = $this->param('note');

        if ($idcard && !Func::isCard($idcard)) {
            $result['message'] = '身份证格式错误';
            return $result;
        }

        if ($phone && !Func::isPhone($phone)) {
            $result['message'] = '手机格式错误';
            return $result;
        }

        if ($email && !Func::isEmail($email)) {
            $result['message'] = '邮箱格式错误';
            return $result;
        }

        if (!isset(AdminModel::$statusMap[$status])) {
            $result['message'] = '状态错误';
            return $result;
        }

        if (!isset(AdminModel::$sexMap[$sex])) {
            $sex = 3;
        }

        $roleId = explode(",", $this->param('roleId'));
        $roleId = array_unique($roleId);
        $roleId = array_filter($roleId);

        $data = array(
            'nickname'   => $nickname,
            'realname'   => $realname,
            'idcard'     => $idcard,
            'phone'      => $phone,
            'email'      => $email,
            'status'     => $status,
            'sex'        => $sex,
            'location1'  => $location1,
            'location2'  => $location2,
            'location3'  => $location3,
            'address'    => $address,
            'note'       => $note,
            'updatetime' => time(),
        );
        $status = AdminModel::where('id', $id)->update($data);
        if (!$status) {
            $result['message'] = "修改失败";
            exit(Func::appJson($result));
        }

        Db::table('t_admin_role')->where('uid', $id)->delete();

        if ($roleId) {
            foreach ($roleId as $rId) {
                $data = array(
                    'uid' => $id,
                    'role_id' => $rId,
                );

                Db::table('t_admin_role')->insert($data);
            }
        }

        Log::run(Log::UPDATE);

        $result['code'] = 200;
        $result['message'] = "修改成功";

        exit(Func::appJson($result));

        return false;
    }

    public function password()
    {
        Perm::check("user:password:reset");

        $result = AdminModel::password($this->param('ids'));
        if ($result['code'] == 200) {
            Log::run(Log::UPDATE);
        }

        exit(Func::appJson($result));

        return false;
    }

    public function passwordSelf()
    {
        $id = $this->uid;
        if (!$id) {
            Http::headerStatus('404');
        }

        $oldPassword = $this->param('oldPassword');
        $newPassword = $this->param('newPassword');

        $result = AdminModel::passwordSelf($id, $this->RSA->decryptByPrivateKey($oldPassword, true), $this->RSA->decryptByPrivateKey($newPassword, true));

        exit(Func::appJson($result));

        return false;
    }

    public function delete()
    {
        Perm::check("user:delete");

        $result = array('code' => 100, 'message' => '');

        $ids = explode(",", $this->param('ids'));
        $ids = array_unique($ids);
        $ids = array_filter($ids);
        $ids = implode(",", $ids);

        if (!$ids) {
            $result['message'] = "请选择要删除的记录";
            exit(Func::appJson($result));
        }

        $data = array(
            'status' => 0,
        );

        $status = AdminModel::where('id', 'IN', $ids)->delete();
        if (!$status) {
            $result['message'] = "删除失败";
            exit(Func::appJson($result));
        }

        Db::table('t_admin_role')->where('uid', 'IN', $ids)->delete();

        Log::run(Log::DELETE);

        $result['code'] = 200;
        $result['message'] = "删除成功";

        exit(Func::appJson($result));

        return false;
    }

    public function profile()
    {
        $id = $this->uid;
        if (!$id) {
            Http::headerStatus('412');
        }

        $info = AdminModel::detail($id);
        if (!$info) {
            Http::headerStatus('404');
        }

        $info['qrcode'] = Url::build('QRcode/index', array('q' => md5(time())), '', true);

        return $this->fetch('', $info);
    }

    public function profileUpdate()
    {
        if (Func::isPost()) {
            $result = array('code' => 100, 'message' => '');

            $nickname  = $this->param('nickname');
            $realname  = $this->param('realname');
            $idcard    = $this->param('idcard');
            $phone     = $this->param('phone');
            $email     = $this->param('email');
            $sex       = $this->param('sex');
            $location1 = $this->param('location1');
            $location2 = $this->param('location2');
            $location3 = $this->param('location3');
            $address   = $this->param('address');
            $note      = $this->param('note');

            if ($idcard && !Func::isCard($idcard)) {
                $result['message'] = '身份证格式错误';
                return $result;
            }

            if ($phone && !Func::isPhone($phone)) {
                $result['message'] = '手机格式错误';
                return $result;
            }

            if ($email && !Func::isEmail($email)) {
                $result['message'] = '邮箱格式错误';
                return $result;
            }

            if (!isset(AdminModel::$sexMap[$sex])) {
                $sex = 3;
            }

            $data = array(
                'nickname'   => $nickname,
                'realname'   => $realname,
                'idcard'     => $idcard,
                'phone'      => $phone,
                'email'      => $email,
                'sex'        => $sex,
                'location1'  => $location1,
                'location2'  => $location2,
                'location3'  => $location3,
                'address'    => $address,
                'note'       => $note,
                'updatetime' => time(),
            );
            $status = AdminModel::where('id', $this->uid)->update($data);
            if ($status) {
                $result['code'] = 200;
                $result['message'] = "修改成功";
            } else {
                $result['message'] = "修改失败";
            }

            exit(Func::appJson($result));

            return false;
        }

        return $this->fetch();
    }

    public function themeUpdate()
    {
        $result = array('code' => 100, 'message' => '');

        $theme  = $this->param('theme');
        $isTab  = $this->param('isTab');

        $config = array(
            'theme' => $theme,
            'isTab' => $isTab,
        );

        $data = array(
            'config' => Func::appJson($config),
        );
        $status = AdminModel::where('id', $this->uid)->update($data);
        if ($status) {
            $result['code'] = 200;
            $result['message'] = "修改成功";
        } else {
            $result['message'] = "修改失败";
        }

        exit(Func::appJson($result));

        return false;
    }

    public function avatar()
    {
        $headimg = $this->param('headimg');
        if ($headimg) {
            $result = array('code' => 100, 'message' => '');

            $data = array(
                'headimg' => $headimg,
            );

            $status = AdminModel::where('id', $this->uid)->update($data);
            if ($status) {
                Session::set('user_auth.headimg', $headimg);

                $result['code'] = 200;
                $result['message'] = "修改成功";
            } else {
                $result['message'] = "修改失败";
            }

            exit(Func::appJson($result));

            return false;
        }

        return $this->fetch();
    }
}