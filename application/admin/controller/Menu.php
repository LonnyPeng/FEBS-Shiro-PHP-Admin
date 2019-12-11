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
use app\admin\model\Menu AS MenuModel;
use app\lonny\controller\Plugin\PHPExcel;
use think\Session;
use app\admin\controller\Plugin\Perm;
use app\admin\controller\Plugin\Log;

class Menu extends Common 
{
    public function index()
    {
        Perm::check("menu:view");

    	$data = array(
    		"id" => 0,
    		"icon" => "",
    		"href" => "",
    		"title" => "",
    		"state" => "",
    		"checked" => false,
    		"attributes" => "",
    		"parentId" => "",
    		"hasParent" => false,
    		"hasChild" => false,
    		"childs" => array(),
    		"data" => ""
    	);

    	$childs = $this->getChild(0);
    	if ($childs) {
    		$data['hasChild'] = true;
    		$data['childs'] = $childs;
    	}

        exit(Func::appJson(array('data' => $data)));
    }

    public function excel()
    {
        Perm::check("menu:export");

    	$where = array(
    		'status' => 1,
            sprintf("id IN(%s)", implode(",", Session::get('memuIds'))),
    	);

    	$field = "name,url,perms,icon, (CASE WHEN type = 1 THEN '按钮' ELSE '菜单' END) AS type_name";
    	$model = MenuModel::field($field)->where($where);

    	$menuName = $this->param('menuName');
    	if ($menuName) {
    		$model->where('name', 'LIKE', "%{$menuName}%");
    	}
    	$order = 'sort ASC, id DESC';
    	$result = collection($model->order($order)->select())->getAll();

    	$title = array(
    		'name' => "名称",
    		'url' => "URL",
    		'perms' => "权限",
    		'icon' => "图标",
    		'type_name' => "类型",
    	);

    	PHPExcel::export($title, $result);

    	return false;
    }

    public function getChild($fid = 0, $type = false)
    {
    	$where = array(
    		'status' => 1,
    		'fid' => $fid,
            sprintf("id IN(%s)", implode(",", Session::get('memuIds'))),
    	);

    	if (!$type) {
    		$where['type'] = 0;
    	}

    	$model = MenuModel::where($where);

    	$menuName = $this->param('menuName');
    	if ($menuName) {
    		$model->where('name', 'LIKE', "%{$menuName}%");
    	}
    	$order = 'sort ASC, id DESC';
    	$result = collection($model->order($order)->select())->getAll();
    	if (!$result) {
    		return array();
    	}

    	$data = array();
    	foreach ($result as $row) {
    		$r = array(
    			"id" => $row['id'],
    			"icon" => $row['icon'],
    			"href" => $row['url'],
    			"title" => $row['name'],
    			"state" => "",
    			"checked" => false,
    			"attributes" => "",
    			"parentId" => "",
    			"hasParent" => false,
    			"hasChild" => false,
    			"childs" => array(),
    			"data" => $row
    		);

    		$childs = $this->getChild($row['id'], $type);
    		if ($childs) {
    			$r['hasChild'] = true;
    			$r['childs'] = $childs;
    		}

    		$data[] = $r;
    	}

    	return $data;
    }

    public function tree()
    {
    	$result = array(
    		'code' => 200, 
    		'data' => $this->getChild(0, true),
    	);

	    exit(Func::appJson($result));
    }

    public function add()
    {
        Perm::check("menu:add");

    	$result = array('code' => 100, 'message' => '');

    	$fid = $this->param('fid');

    	$name = $this->param('name');
    	if (!$name) {
    		$result['message'] = "名称 不能为空";
    		exit(Func::appJson($result));
    	}

    	$type = $this->param('type');
    	$icon = $this->param('icon');
    	$url = $this->param('url');
    	$perms = $this->param('perms');
    	$sort = $this->param('sort');

    	$data = array(
    		'fid' => $fid,
    		'type' => $type,
    		'name' => $name,
    		'icon' => $icon,
    		'url' => $url,
    		'perms' => $perms,
    		'sort' => $sort,
    	);

    	$status = MenuModel::insert($data);
    	if ($status) {
            Log::run(Log::ADD);

    		$result['code'] = 200;
    		$result['message'] = "新增成功";
    	} else {
    		$result['message'] = "新增失败";
    	}

    	exit(Func::appJson($result));

    	return false;
    }

    public function update()
    {
        Perm::check("menu:update");

    	$result = array('code' => 100, 'message' => '');

    	$id = $this->param('id');
    	if (!$id) {
    		$result['message'] = "ID 不能为空";
    		exit(Func::appJson($result));
    	}

    	$name = $this->param('name');
    	if (!$name) {
    		$result['message'] = "名称 不能为空";
    		exit(Func::appJson($result));
    	}

    	$icon = $this->param('icon');
    	$url = $this->param('url');
    	$perms = $this->param('perms');
    	$sort = $this->param('sort');

    	$data = array(
    		'name' => $name,
    		'icon' => $icon,
    		'url' => $url,
    		'perms' => $perms,
    		'sort' => $sort,
    	);

    	$status = MenuModel::where('id', $id)->update($data);
    	if ($status) {
            Log::run(Log::UPDATE);

    		$result['code'] = 200;
    		$result['message'] = "修改成功";
    	} else {
    		$result['message'] = "修改失败";
    	}

    	exit(Func::appJson($result));

    	return false;
    }

    public function delete()
    {
        Perm::check("menu:delete");
        
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

    	$status = MenuModel::where('id', 'IN', $ids)->update($data);
    	if ($status) {
            Log::run(Log::DELETE);
            
    		$result['code'] = 200;
    		$result['message'] = "删除成功";
    	} else {
    		$result['message'] = "删除失败";
    	}

    	exit(Func::appJson($result));

    	return false;
    }
}