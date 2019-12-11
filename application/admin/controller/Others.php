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

class Others extends Common 
{
    public function icon()
    {
        return $this->fetch();
    }

    public function form()
    {
        return $this->fetch();
    }

    public function formGroup()
    {
    	return $this->fetch();
    }

    public function tools()
    {
    	return $this->fetch();
    }

    public function others()
    {
    	return $this->fetch();
    }

    public function apexLine()
    {
    	return $this->fetch();
    }

    public function apexArea()
    {
    	return $this->fetch();
    }

    public function apexColumn()
    {
    	return $this->fetch();
    }

    public function apexRadar()
    {
    	return $this->fetch();
    }

    public function apexBar()
    {
    	return $this->fetch();
    }

    public function apexMix()
    {
    	return $this->fetch();
    }

    public function mapBase()
    {
    	return $this->fetch();
    }

    public function mapSatellite()
    {
        return $this->fetch();
    }

    public function mapTrain()
    {
        return $this->fetch();
    }

    public function eximport()
    {
    	return $this->fetch();
    }

    public function upload()
    {
        return $this->fetch();
    }

    public function qrcode()
    {
        $data = array(
            'headimg' => Session::get('user_auth.headimg'),
        );

        return $this->fetch('', $data);
    }

    public function word()
    {
        return $this->fetch();
    }
}