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

namespace app\lonny\controller;

class Index extends Common 
{
	/**
	 * [update_api_docs 更新接口文档]
	 * @Author   Lonny
	 * @Email    lonnypeng@baogongpo.com
	 * @DateTime 2019-10-30
	 * @return   [type]                  [description]
	 */
    public function update_api_docs()
    {
    	$run_api_path = ROOT . "application/api/controller/";
    	$save_api = ROOT . "download/api_docs/swagger.json";

        $swagger = \Swagger\scan($run_api_path);
        $swagger->saveAs($save_api);

        $data = file_get_contents($save_api);

        return json_decode($data, true);
    }
}