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
use app\lonny\controller\Plugin\PHPExcel;
use app\lonny\controller\Plugin\Pdf;
use app\admin\model\Eximport AS EximportModel;
use app\lonny\controller\Plugin\Func;
use app\admin\controller\Plugin\Perm;
use app\admin\controller\Plugin\Log;

class Eximport extends Files 
{
	public function __construct()
	{
		parent::__construct();
	}

	public function table($export = false)
	{
		Perm::check("others:eximport:view");

	    if ($export) {
	        $limit = '';
	    } else {
	        $pageNum = $this->param('pageNum') ?: 1;
	        $pageSize = $this->param('pageSize') ?: 20;

	        $limit = sprintf("%d, %d", ($pageNum - 1) * $pageSize, $pageSize);
	    }

	    $total = collection(EximportModel::field('COUNT(*)')->select())->getOne();

	    $data = collection(EximportModel::limit($limit)->select())->getAll();

	    foreach ($data as $key => $row) {
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

	    exit(Func::appJson($result));

	    return false;
	}

	public function import()
	{
		Perm::check("eximport:import");

		$result = array('code' => 100, 'message' => '');

		$titleMap = array(
			'field1' => "字段1",
			'field2' => "字段2",
			'field3' => "字段3",
		);

		if (!isset($_FILES['file'])) {
			$result['message'] = 'file 不能为空';
			exit(Func::appJson($result));
		}

		$file = $_FILES['file'];

		if ($file['error']) {
			$result['message']= $this->fileError[$file['error']];
			exit(Func::appJson($result));
		} elseif (!in_array($file['type'], $this->fileType)) {
			$result['message']= sprintf('格式错误，请选择 %s', implode("、", array_keys($this->fileType)));
			exit(Func::appJson($result));
		} elseif ($file['size'] < $this->fileSize['min'] || $file['size'] > $this->fileSize['max']) {
			$result['message']= sprintf('大小错误（最小：%s; 最大：%s ）', Func::convert($this->fileSize['min']), Func::convert($this->fileSize['max']));
			exit(Func::appJson($result));
		}

		$ext = array_search($file['type'], $this->fileType);
		$data = PHPExcel::import($file['tmp_name'], $ext);
		if (!$data) {
			$result['message'] = 'file 内容不能为空';
			exit(Func::appJson($result));
		}

		$fileTitle = reset($data);
		if (!$fileTitle) {
			$result['message'] = 'file 内容标题不能为空';
			exit(Func::appJson($result));
		}

		$titleData = array();
		foreach ($titleMap as $key => $title) {
			if (!in_array($title, $fileTitle)) {
				$result['message'] = sprintf('file 内容%s标题不能为空', $title);
				exit(Func::appJson($result));
			}

			$titleData[$key] = array_search($title, $fileTitle);
		}
	    
	    $start_time = time();

	    $error = array();
	    $success = array();
	    foreach ($data as $key => $row) {
	    	if ($key < 2) {
	    		continue;
	    	}

	    	$r = array(
	    		'createtime' => time(),
	    	);
	    	foreach ($titleData as $field => $value) {
	    		if (!isset($row[$value])) {
	    			$error[] = array(
	    				'row' => $key,
	    				'errorMessage' => sprintf("%s标题内容不能为空", $titleMap[$field]),
	    			);

	    			continue;
	    		}

	    		$r[$field] = $row[$value];
	    	}

	    	$status = EximportModel::insert($r);
	    	if ($status) {
	    		$success[] = array(
	    			'row' => $key,
	    			'createtime' => date("Y-m-d H:i:s"),
	    		);
	    	} else {
	    		$error[] = array(
	    			'row' => $key,
	    			'errorMessage' => '保存失败',
	    		);
	    	}
	    }

	    $data = array(
	    	'data' => $success,
	    	'error' => $error,
	    	'time' => Func::remainingTime(time() - $start_time) ?: '1秒',
	    );

	    Log::run(Log::IMPORT);

	    $result['code'] = "200";
	    $result['message'] = "导入成功";
	    $result['data'] = $data;

	    exit(Func::appJson($result));

	    return false;
	}

	public function importResult()
	{
		return $this->fetch();
	}

	public function exportExcel()
	{
		Perm::check("eximport:export:excel");

	    $title = array(
	        'field1' => "字段1",
	        'field2' => "字段2",
	        'field3' => "字段3",
	        'createtime' => "导入时间",
	    );

	    PHPExcel::export($title, $this->table(true));

	    return false;
	}

	public function exportPdf()
	{
		Perm::check("eximport:export:pdf");

		$title = array(
		    'id' => "ID",
		    'field1' => "字段1",
		    'field2' => "字段2",
		    'field3' => "字段3",
		    'createtime' => "导入时间",
		);
		foreach ($title as $key => $value) {
			$title[$key] = "<td>{$value}</td>";
		}
		$title = sprintf("<tr>%s</tr>", implode("", $title));

		$data = $this->table(true);

		foreach ($data as $key => $row) {
			foreach ($row as $ke => $value) {
				$row[$ke] = "<td>{$value}</td>";
			}

			$data[$key] = sprintf("<tr>%s</tr>", implode("", $row));
		}

		$html = sprintf("<table>%s%s</table>", $title, implode("", $data));

		Pdf::export($html);

		return false;
	}

    public function template()
    {
    	Perm::check("eximport:template");
    	
    	$title = array(
    	    'field1' => "字段1",
    	    'field2' => "字段2",
    	    'field3' => "字段3",
    	);

    	$data = array();
    	for($i=0; $i<10; $i++) {
    		$data[] = array(
    			'field1' => mt_rand(10, 99),
    			'field2' => mt_rand(10, 99),
    			'field3' => mt_rand(10, 99),
    		);
    	}

    	PHPExcel::export($title, $data);

    	return false;
    }
}