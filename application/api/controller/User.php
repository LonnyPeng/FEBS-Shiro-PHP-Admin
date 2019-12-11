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

class User extends Common 
{
    /**
     * @SWG\Get(
     *     tags={"User"},
     *     path="/user/login",
     *     summary="登录",
     *     description="返回TOKEN",
     *     @SWG\Response(
     *         response=200,
     *         description="登录信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="code",
     *                 type="string",
     *                 description="代码",
     *             ),
     *             @SWG\Property(
     *                 property="message",
     *                 type="string",
     *                 description="消息",
     *             ),
     *             @SWG\Property(
     *                 property="data",
     *                 type="string",
     *                 description="TOKEN",
     *             ),
     *         )
     *     )
     * )
     */
    public function login()
    {
        $result = [
            'code' => 20000,
            'message' => '',
            'data' => '',
        ];

        $username = $this->param('username');
        $password = $this->param('password');

        $result['data'] = md5($username . time());

        return $this->echoRsa($result);
    }

    /**
     * @SWG\Get(
     *     tags={"User"},
     *     path="/user/info",
     *     summary="获取用户信息",
     *     description="返回用户信息",
     *     @SWG\Response(
     *         response=200,
     *         description="用户信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="roles",
     *                 type="array",
     *                 description="代码",
     *                 @SWG\Items(
     *                     @SWG\Property(
     *                         property="name",
     *                         type="string",
     *                         description="消息"
     *                     )
     *                 )
     *             ),
     *             @SWG\Property(
     *                 property="introduction",
     *                 type="string",
     *                 description="消息",
     *             ),
     *             @SWG\Property(
     *                 property="token",
     *                 type="string",
     *                 description="TOKEN",
     *             ),
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *                 description="name",
     *             )
     *         )
     *     )
     * )
     */
    public function info()
    {
        $result = [
            'code' => 20000,
            'message' => '',
            'data' => [
                'roles' => ['admin'],
                'introduction' => 'I am a super administrator',
                'avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
                'name' => 'Super Admin',
            ],
        ];

        return $this->echoRsa($result);
    }

    /**
     * @SWG\Get(
     *     tags={"User"},
     *     path="/user/logout",
     *     summary="用户退出",
     *     description="用户退出",
     *     @SWG\Response(
     *         response=200,
     *         description="退出信息",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="introduction",
     *                 type="string",
     *                 description="消息",
     *             ),
     *             @SWG\Property(
     *                 property="avatar",
     *                 type="string",
     *                 description="TOKEN",
     *             )
     *         )
     *     )
     * )
     */
    public function logout()
    {
        $result = [
            'code' => 20000,
            'message' => '',
            'data' => 'success',
        ];

        return $this->echoRsa($result);
    }
}
