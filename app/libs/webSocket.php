<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/3
 * Time: 下午5:20
 */
class webSocket
{
    public $server;

    static public $redis_endpoint;


    public function __construct($point)
    {
        $this->server = new swoole_websocket_server("0.0.0.0", $point);
        $cache = getConfig('cache')->ws_redis;
        self::$redis_endpoint = $cache->endpoint;

        $this->server->set([
            'daemonize' => 1,
            'worker_num' => 1,
            'log_file'=>APP_ROOT.'/logs/ws.log'
        ]);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->on('close', [$this, 'onClose']);

        echoTip("Server start: {$point}");
        $this->server->start();
    }

    function onOpen(swoole_websocket_server $server, $request)
    {
        echoTip("ws server: handshake success with fd: {$request->fd}");
    }

    function onMessage(swoole_websocket_server $server, $frame)
    {
        echoTip("ws receive from {$frame->fd}:{$frame->data}, opcode:{$frame->opcode}, fin:{$frame->finish}");
        self::sub($server, $frame->fd, $frame->data);
    }

    function workerStart($server, $work_id)
    {
        $redis = McRedis::getInstance(self::$redis_endpoint);

        list($host, $point) = explode(':', self::$redis_endpoint, 2);
        $client = new swoole_redis;
        $client->on('message', function (swoole_redis $client, $messages) use ($server, $redis) {
            list($method, $redis_channel, $data) = $messages;
            $data = json_decode($data, true);
            $channel = $data['message_type'];
            $channel_key = self::getChannelKey($channel);
            $fds = $redis->zrange($channel_key, 0, -1);
            $info = $server->getClientList();
            debug('info: ', $info);
            foreach ($fds as $fd) {
                debug('fd: ', $fd);
                $server->push($fd, json_encode($data));
            }
        });

        $client->connect($host, $point, function (swoole_redis $client, $result) {
            if ($result) {
                echoTip("swoole redis connect success");
                $client->subscribe('msg_0');
            }
        });
    }

    function onClose($server, $fd)
    {
        $redis = McRedis::getInstance(self::$redis_endpoint);
        $channel_keys = $redis->smembers('message_type_channel_list');
        foreach ($channel_keys as $channel_key) {
            $redis->del($channel_key);
        }
        echoTip("client $fd closed!");
    }

    static function getChannelKey($channel)
    {
        $app_name = basename(APP_ROOT);
        return $app_name . '_' . $channel . '_channel';
    }

    static function sub($server, $fd, $data)
    {
        $data = json_decode($data, true);

        $redis = McRedis::getInstance(self::$redis_endpoint);
        if (!isset($data['message_type']) || !isset($data['operate'])) {
            $result = json_encode(['error_code' => -1, 'error_reason' => '缺少参数']);
            $server->push($fd, $result);
        }

        $channel_key = self::getChannelKey($data['message_type']);
        $redis->sadd('message_type_channel_list', $channel_key);
        $success_response = json_encode(['error_code' => 0, 'error_reason' => "{$data['operate']} {$data['message_type']} success"]);
        $fail_response = json_encode(['error_code' => -1, 'error_reason' => "{$data['operate']} {$data['message_type']} failed"]);
        debug('operate: ', $data['operate']);
        if ('sub' == $data['operate']) {
            $result = $redis->zadd($channel_key, time(), $fd);
            debug('sub: ', $result);
            if ($result) {
                $server->push($fd, $success_response);
            } else {
                $server->push($fd, $fail_response);
            }
        } elseif ('unSub' == $data['operate']) {
            $result = $redis->zrem($channel_key, time(), $fd);
            debug('unsub: ', $result);
            if ($result) {
                $server->push($fd, $success_response);
            } else {
                $server->push($fd, $fail_response);
            }
        } else {
            $server->push($fd, json_encode(['error_code' => -1, 'error_reason' => '参数错误']));
        }
    }
}