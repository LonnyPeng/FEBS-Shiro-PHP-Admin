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

class Files extends Common 
{
	protected $fileType = array(
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xls' => 'application/vnd.ms-excel',
	);
	protected $fileSize = NULL;
	protected $fileError = array(
		0 => "文件上传成功",
		1 => "上传的文件超过了服务器项限制的值",
		2 => "上传文件的大小超过了表单中选项指定的值",
		3 => "文件只有部分被上传",
		4 => "没有文件被上传",
		6 => "找不到临时文件夹",
		7 => "文件写入失败",
	);

	public function __construct()
	{
		parent::__construct();

		$this->fileSize = array(
			'min' => 1, //50Kb
			'max' => 10 * 1024 * 1024, //10M
		);
	}
}