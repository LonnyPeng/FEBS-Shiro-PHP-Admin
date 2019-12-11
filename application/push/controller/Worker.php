<?php
 
namespace app\push\controller;
 
use think\worker\Server;
use app\lonny\controller\Plugin\Func;
use app\lonny\controller\Plugin\Redis;
 
class Worker extends Server
{
    protected $socket = null;

    private static $typeMap = array(
        'login', 'send',
    );

    private static $names = array();

    public function __construct()
    {
        if (!WEBSCOKET_ENABLED) {
            die('暂未开启配置');
        }

        self::$names = Redis::getHash("WorkerUser");

        $this->socket = sprintf("websocket://%s:%s", WEBSCOKET_HOST, WEBSCOKET_PORT);

        parent::__construct();
    }
 
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection = '', $data = '')
    {
        $result = array('code' => 100, 'message' => '', 'data' => array());

        $data = json_decode($data, true);
        if (!$data 
            || !isset($data['type']) 
            || !in_array($data['type'], self::$typeMap)) {
            $result['message'] = "type 非法参数";
            $connection->send(Func::appJson($result));

            return false;
        }

        $type = $data['type'];
        if ($type == 'login') {
            if (!isset($data['uid'])) {
                $result['message'] = "uid 非法参数";
                $connection->send(Func::appJson($result));

                return false;
            }

            self::$names[$data['uid']] = $connection->id;

            Redis::setHash("WorkerUser", self::$names);
        } elseif ($type == 'send') {
            if (!isset($data['data'])) {
                $result['message'] = "uid 非法参数";
                $connection->send(Func::appJson($result));

                return false;
            }

            $data = $data['data'];

            if (!isset(self::$names[$data['to']['id']])) {
                $result['message'] = "{$data['to']['name']} 未登录";
                $connection->send(Func::appJson($result));

                return false;
            }

            $result['code'] = 200;
            $result['message'] = '';
            $result['data'] = array(
                "username" => $data['mine']['username'],
                "avatar" => $data['mine']['avatar'],
                "id" => (int) $data['mine']['id'],
                "type" => $data['to']['type'],
                "content" => $data['mine']['content'],
                "mine" => true,
                "timestamp" => time()
            );

             $this->worker->connections[self::$names[$data['to']['id']]]->send(Func::appJson($result, true));
        }
    }
 
    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        //
    }
 
    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        //
    }
    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }
 
    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        //
    }
}