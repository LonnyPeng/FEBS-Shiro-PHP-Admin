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

namespace app\api\controller;

use app\api\model\Word;

/**
 * 
 */
class Index extends Common 
{
    /**
     * @SWG\Get(
     *     tags={"Index"},
     *     path="/index/index",
     *     summary="获取一些人",
     *     description="返回包含所有人的列表。",
     *     @SWG\Response(
     *         response=200,
     *         description="一个用户列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="firstName",
     *                 type="string",
     *                 description="一个用户列表",
     *             ),
     *             @SWG\Property(
     *                 property="lastName",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="username",
     *                 type="string"
     *             ),
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = [[
            "id" => 1,
            "pid" => -1,
            "hospitalName" => "医院名称",
            "orderTotal" => 0,
            "payOrderTotal" => 0,
            "payCustomerTotal" => 0,
            "orderActualAmount" => 0,
            "pileTotal" => 0,
        ],[
            "id" => 2,
            "pid" => 1,
            "hospitalName" => "医院名称",
            "orderTotal" => 0,
            "payOrderTotal" => 0,
            "payCustomerTotal" => 0,
            "orderActualAmount" => 0,
            "pileTotal" => 0,
        ]];

        $data = array(
            "code" => 200,
            "data" => [
                "total" => 631,
                "rows" => $data
            ]
        );

        return $this->echoRsa($data);
    }

    /**
     * @SWG\Post(
     *     tags={"Index"},
     *     path="/persons",
     *     summary="Creates a person",
     *     description="Adds a new person to the persons list.",
     *     @SWG\Parameter(
     *          name="person",
     *          in="body",
     *          required=true,
     *          description="The person to create.",
     *          @SWG\Schema(
     *              required={"username"},
     *              @SWG\Property(
     *                  property="firstName",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                   property="lastName",
     *                   type="string"
     *              ),
     *              @SWG\Property(
     *                   property="username",
     *                   type="string"
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="一个用户列表",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="firstName",
     *                 type="string",
     *                 description="一个用户列表",
     *             ),
     *             @SWG\Property(
     *                 property="lastName",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="username",
     *                 type="string"
     *             ),
     *         )
     *     )
     * )
     */
    public function word()
    {
    	return Word::all(function ($query) {
    		$query->limit(0, 100);
    	});
    }
}
