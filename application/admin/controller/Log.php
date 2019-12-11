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
use app\admin\model\Log AS LogModel;
use app\lonny\controller\Plugin\PHPExcel;
use app\admin\controller\Plugin\Perm;

class Log extends Common 
{
    public function table($export = false)
    {
        Perm::check("log:view");

        $where = array();

        $username = $this->param('username');
        if ($username) {
            $where[] = sprintf("username LIKE '%%%s%%'", $username);
        }

        $operation = $this->param('operation');
        if ($operation) {
            $where['operation'] = $operation;
        }

        $method = $this->param('method');
        if ($method) {
            $where[] = sprintf("method LIKE '%%%s%%'", $method);
        }

        $createTimeFrom = $this->param('createTimeFrom');
        if ($createTimeFrom) {
            $where[] = sprintf("createtime >= %d", strtotime(date("Y-m-d 00:00:00", strtotime($createTimeFrom))));
        }
        $createTimeTo = $this->param('createTimeTo');
        if ($createTimeTo) {
            $where[] = sprintf("createtime <= %d", strtotime(date("Y-m-d 23:59:59", strtotime($createTimeTo))));
        }

        $field = array('id, username, operation, method, params, ip, createtime');

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

        $total = collection(LogModel::field('COUNT(*)')->where($where)->select())->getOne();

        $data = collection(LogModel::field($field)->where($where)->order($order)->limit($limit)->select())->getAll();

        foreach ($data as $key => $row) {
            foreach ($row as $ke => $value) {
                $row[$ke] = $value === null ? "" : $value;
            }

            $row['createtime'] = $row['createtime'] ? date("Y-m-d H:i:s", $row['createtime']) : '';

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

        exit(Func::appJson($result, true));

        return false;
    }

    public function excel()
    {
        Perm::check("log:export");

        $title = array(
            'username' => "操作人",
            'operation' => "操作类型",
            'method' => "操作方法",
            'params' => "方法参数",
            'ip' => "IP地址",
            'createtime' => "创建时间",
        );

        PHPExcel::export($title, $this->table(true));

        return false;
    }

    public function delete()
    {
        Perm::check("log:delete");

        $result = array('code' => 100, 'message' => '');

        $ids = explode(",", $this->param('ids'));
        $ids = array_unique($ids);
        $ids = array_filter($ids);
        $ids = implode(",", $ids);

        if (!$ids) {
            $result['message'] = "请选择要删除的记录";
            exit(Func::appJson($result));
        }

        $status = LogModel::where('id', 'IN', $ids)->delete();
        if (!$status) {
            $result['message'] = "删除失败";
            exit(Func::appJson($result));
        }

        $result['code'] = 200;
        $result['message'] = "删除成功";

        exit(Func::appJson($result));

        return false;
    }
}