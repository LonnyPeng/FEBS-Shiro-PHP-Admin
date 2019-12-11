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
use think\Url;
use app\lonny\controller\Plugin\Pay AS LPay;

class Pay extends Common 
{
    public function index()
    {
        $config = array(
            'alipay' => array(
                'web' => array(
                    'out_trade_no' => '12',         // 订单号
                    'total_amount' => '13',         // 订单金额，单位：元，**微信支付，单位：分**
                    'subject' => 'test subject',    // 订单商品标题
                ), //电脑支付
                'wap' => array(
                    'out_trade_no' => '12',         // 订单号
                    'total_amount' => '13',         // 订单金额，单位：元，**微信支付，单位：分**
                    'subject' => 'test subject',    // 订单商品标题
                ), //手机网站支付
                'app' => array(
                    'out_trade_no' => '12',         // 订单号
                    'total_amount' => '13',         // 订单金额，单位：元，**微信支付，单位：分**
                    'subject' => 'test subject',    // 订单商品标题
                ), //APP 支付
                'pos' => array(
                    'out_trade_no' => '12',         // 订单号
                    'total_amount' => '13',         // 订单金额，单位：元
                    'subject' => 'test subject',    // 订单商品标题
                    'auth_code'  => '123456',       // 授权码
                ), //刷卡支付
                'scan' => array(
                    'out_trade_no' => '12',         // 订单号
                    'total_amount' => '13',         // 订单金额，单位：元，**微信支付，单位：分**
                    'subject' => 'test subject',    // 订单商品标题
                ), //扫码支付
                'transfer' => array(
                    'out_biz_no' => '',                      // 订单号
                    'payee_type' => 'ALIPAY_LOGONID',        // 收款方账户类型(ALIPAY_LOGONID | ALIPAY_USERID)
                    'payee_account' => 'demo@sandbox.com',   // 收款方账户
                    'amount' => '10',                        // 转账金额
                ), //帐户转账（可用于平台用户提现）
            ),
            'wechat' => array(
                'mp' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 支付人的 IP
                    'openid' => '',                 // 支付人的 openID
                ), //公众号支付
                'miniapp' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 支付人的 IP
                    'openid' => '',                 // 支付人的 openID
                ), //小程序支付
                'wap' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 支付人的 IP
                ), //H5 支付
                'scan' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 调用 API 服务器的 IP
                    'product_id' => '1',             // 订单商品 ID
                ), //扫码支付
                'pos' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 支付人的 IP
                    'auth_code' => '',              // 授权码
                ), //刷卡支付
                'app' => array(
                    'out_trade_no' => time(),           // 订单号
                    'total_fee' => '1',              // 订单金额，**单位：分**
                    'body' => 'note test',                   // 订单描述
                    'spbill_create_ip' => '192.168.0.1',       // 支付人的 IP
                ), //APP 支付
                'transfer' => array(
                    'partner_trade_no' => '',              //商户订单号
                    'openid' => '',                        //收款人的openid
                    'check_name' => 'NO_CHECK',            //NO_CHECK：不校验真实姓名\FORCE_CHECK：强校验真实姓名
                    're_user_name'=>'张三',              //check_name为 FORCE_CHECK 校验实名的时候必须提交
                    'amount' => 100,                       //企业付款金额，单位为分
                    'desc' => '帐户提现',                  //付款说明
                    'spbill_create_ip' => '192.168.0.1',  //发起交易的IP地址
                ), //企业付款
                'groupredpack' => array(
                    'wxappid' =>'wxaxxxxxxxx',
                    'mch_billno' => 'hb'.time(),
                    'send_name' => '萌点云科技',//商户名称
                    're_openid' => 'ogg5JwsssssssssssCdXeD_S54',//用户openid
                    'total_amount' => 333, // 付款金额，单位分
                    'wishing' => '提前祝你狗年大吉',//红包祝福语
                    'client_ip' => '192.168.0.1',//调用接口的机器Ip地址
                    'total_num' => '3',//红包发放总人数
                    'act_name' => '提前拜年',//活动名称
                    'remark' => '提前祝你狗年大吉，苟富贵勿相忘！', //备注
                    'amt_type' => 'ALL_RAND',//ALL_RAND—全部随机,商户指定总金额和红包发放总人数，由微信支付随机计算出各红包金额
                ), //发放裂变红包
                'redpack' => array(
                    'wxappid' => 'wxaxxxxxxxx',
                    'mch_billno' => 'hb'.time(),
                    'send_name' => '萌点云科技',//商户名称
                    're_openid' => 'ogg5JwsssssssssssCdXeD_S54',//用户openid
                    'total_amount' => 100, // 付款金额，单位分
                    'wishing' => '提前祝你狗年大吉',//红包祝福语
                    'client_ip' => '192.168.0.1',//调用接口的机器Ip地址
                    'total_num' => '1',//红包发放总人数
                    'act_name' => '提前拜年',//活动名称
                    'remark' => '提前祝你狗年大吉，苟富贵勿相忘！', //备注
                ), //发放普通红包
            ),
        );

        $driver = 'wechat';
        $gateway = 'scan';
        
        $result = LPay::index($config[$driver][$gateway], $driver, $gateway);

        return $this->fetch('', array('html' => $result));
    }

    public function notifyUrl()
    {
        $driver = $this->param('driver');
        $gateway = $this->param('gateway');

        LPay::notifyUrl($data, $driver, $gateway);
    }

    public function returnUrl()
    {
        $driver = $this->param('driver');
        $gateway = $this->param('gateway');

        LPay::returnUrl($data, $driver, $gateway);
    }
}