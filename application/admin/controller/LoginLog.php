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
use app\admin\model\LoginLog AS LoginLogModel;
use app\lonny\controller\Plugin\PHPExcel;
use app\admin\controller\Plugin\Perm;

class LoginLog extends Common 
{
    public function table($export = false)
    {
        Perm::check("loginlog:view");

        $where = array();

        $username = $this->param('username');
        if ($username) {
            $where[] = sprintf("username LIKE '%%%s%%'", $username);
        }

        $createTimeFrom = $this->param('createTimeFrom');
        if ($createTimeFrom) {
            $where[] = sprintf("logintime >= %d", strtotime(date("Y-m-d 00:00:00", strtotime($createTimeFrom))));
        }
        $createTimeTo = $this->param('createTimeTo');
        if ($createTimeTo) {
            $where[] = sprintf("logintime <= %d", strtotime(date("Y-m-d 23:59:59", strtotime($createTimeTo))));
        }

        $field = array('id, username, logintime, ip, browser');
        
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

        $total = collection(LoginLogModel::field('COUNT(*)')->where($where)->select())->getOne();

        $data = collection(LoginLogModel::field($field)->where($where)->order($order)->limit($limit)->select())->getAll();

        foreach ($data as $key => $row) {
            foreach ($row as $ke => $value) {
                $row[$ke] = $value === null ? "" : $value;
            }

            $row['logintime'] = $row['logintime'] ? date("Y-m-d H:i:s", $row['logintime']) : '';

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
        Perm::check("loginlog:export");

        $title = array(
            'username' => "登录用户",
            'ip' => "IP地址",
            'logintime' => "登录时间",
            'browser' => "浏览器",
        );

        PHPExcel::export($title, $this->table(true));

        return false;
    }

    public function delete()
    {
        Perm::check("loginlog:delete");

        $result = array('code' => 100, 'message' => '');

        $ids = explode(",", $this->param('ids'));
        $ids = array_unique($ids);
        $ids = array_filter($ids);
        $ids = implode(",", $ids);

        if (!$ids) {
            $result['message'] = "请选择要删除的记录";
            exit(Func::appJson($result));
        }

        $status = LoginLogModel::where('id', 'IN', $ids)->delete();
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