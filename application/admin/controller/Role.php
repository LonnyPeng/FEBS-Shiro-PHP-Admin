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

use app\admin\model\Role AS RoleModel;
use app\lonny\controller\Plugin\Func;
use think\Db;
use app\lonny\controller\Plugin\PHPExcel;
use app\admin\controller\Plugin\Perm;
use app\admin\controller\Plugin\Log;

class Role extends Common 
{
    public function table($export = false)
    {
        Perm::check("role:view");

        $where = array();

        $name = $this->param('name');
        if ($name) {
            $where[] = sprintf("name LIKE '%%%s%%'", $name);
        }

    	$field = array("id,name,remark,createtime");
        
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

        $total = collection(RoleModel::field('COUNT(*)')->where($where)->select())->getOne();

    	$data = collection(RoleModel::field($field)->where($where)->order($order)->limit($limit)->select())->getAll();
        foreach ($data as $key => $row) {
            $row['createtime'] = $row['createtime'] ? date("Y-m-d H:i:s", $row['createtime']) : '';

            $menuIds = collection(Db::table('t_role_menu')->field('menu_id')->where('role_id', $row['id'])->select())->getColumn();
            if ($menuIds) {
                $row['menuIds'] = implode(",", $menuIds);
            } else {
                $row['menuIds'] = "";
            }

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
        Perm::check("role:export");

        $title = array(
            'name' => "角色名称",
            'remark' => "角色描述",
            'createtime' => "创建时间",
        );

        PHPExcel::export($title, $this->table(true));

        return false;
    }

    public function add()
    {
        Perm::check("role:add");

    	$result = array('code' => 100, 'message' => '');

        $name = $this->param('name');
        if (!$name) {
            $result['message'] = "角色名称 不能为空";
            exit(Func::appJson($result));
        }

        $id = collection(RoleModel::field('id')->where('name', $name)->select())->getOne();
        if ($id) {
            $result['message'] = "角色名称 已存在";
            exit(Func::appJson($result));
        }

        $remark = $this->param('remark');

        $menuIds = explode(",", $this->param('menuIds'));
        $menuIds = array_unique($menuIds);
        $menuIds = array_filter($menuIds);

        $data = array(
            'name' => $name,
            'remark' => $remark,
            'createtime' => time(),
        );
        $roleId = RoleModel::insertGetId($data);
        if (!$roleId) {
            $result['message'] = "新增失败";
            exit(Func::appJson($result));
        }

        if ($menuIds) {
            foreach ($menuIds as $menuId) {
                $data = array(
                    'role_id' => $roleId,
                    'menu_id' => $menuId,
                );

                Db::table('t_role_menu')->insert($data);
            }
        }

        Log::run(Log::ADD);

        $result['code'] = 200;
        $result['message'] = "新增成功";

        exit(Func::appJson($result));

        return false;
    }

    public function update()
    {
        Perm::check("role:update");

    	$result = array('code' => 100, 'message' => '');

        $roleId = $this->param('id');
        if (!$roleId) {
            $result['message'] = "ID 不能为空";
            exit(Func::appJson($result));
        }

        $name = $this->param('name');
        if (!$name) {
            $result['message'] = "角色名称 不能为空";
            exit(Func::appJson($result));
        }

        $id = collection(RoleModel::field('id')->where('name', $name)->select())->getOne();
        if ($id != $roleId) {
            $result['message'] = "角色名称 已存在";
            exit(Func::appJson($result));
        }

        $remark = $this->param('remark');

        $menuIds = explode(",", $this->param('menuIds'));
        $menuIds = array_unique($menuIds);
        $menuIds = array_filter($menuIds);

        $data = array(
            'name' => $name,
            'remark' => $remark,
            'updatetime' => time(),
        );
        $status = RoleModel::where('id', $roleId)->update($data);
        if (!$status) {
            $result['message'] = "修改失败";
            exit(Func::appJson($result));
        }

        Db::table('t_role_menu')->where('role_id', $roleId)->delete();

        if ($menuIds) {
            foreach ($menuIds as $menuId) {
                $data = array(
                    'role_id' => $roleId,
                    'menu_id' => $menuId,
                );

                Db::table('t_role_menu')->insert($data);
            }
        }

        Log::run(Log::UPDATE);

        $result['code'] = 200;
        $result['message'] = "修改成功";

        exit(Func::appJson($result));

        return false;
    }

    public function delete()
    {
        Perm::check("role:delete");
        
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

        $status = RoleModel::where('id', 'IN', $ids)->delete();
        if (!$status) {
            $result['message'] = "删除失败";
            exit(Func::appJson($result));
        }

        Db::table('t_role_menu')->where('role_id', 'IN', $ids)->delete();

        Log::run(Log::DELETE);

        $result['code'] = 200;
        $result['message'] = "删除成功";

        exit(Func::appJson($result));

        return false;
    }

    public function select()
    {
        $data = collection(RoleModel::field("id,name")->select())->getAll();

        exit(Func::appJson($data));

        return false;
    }

    public function selectMultiple()
    {
        $where = array();

        $name = $this->param('name');
        if ($name) {
            $where[] = sprintf("name LIKE '%%%s%%'", $name);

            $id = explode(",", $this->param('id'));
            $id = array_unique($id);
            $id = array_filter($id);
            if ($id) {
                $where[] = sprintf("id IN(%s)", implode(",", $id));
            }
        }

        $where = array(implode(" OR ", $where));
        $data = collection(RoleModel::field("id,name")->where($where)->select())->getAll();

        $result = array(
            'code' => "200",
            'data' => $data,
        );

        exit(Func::appJson($result));

        return false;
    }
}