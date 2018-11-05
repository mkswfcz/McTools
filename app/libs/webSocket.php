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

        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
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
        self::handle($server, $frame->fd, $frame->data);
    }

    function onClose($server, $fd)
    {
        echoTip("client $fd closed!");
    }

    static function getChannelKey($channel)
    {
        $app_name = basename(APP_ROOT);
        return $app_name . '_' . $channel;
    }

    static function handle($server, $fd, $data)
    {
        if (!isset($data['channel']) || !isset($data['operate'])) {
            $result = json_encode(['error_code' => 0, 'error_reason' => '缺少参数']);
            $server->push($fd, $result);
        }

        list($host, $point) = explode(':', self::$redis_endpoint, 2);

        $client = new swoole_redis;
        $client->connect($host, $point, function (swoole_redis $client, $result) use ($server, $fd, $data) {
            if (!$result) {
                echoTip("swoole connect to redis server failed!");
            }

            $data = json_decode($data, true);
            $channel = $data['channel'];
            $operate = $data['operate'];
            debug($channel, $operate);

            $channel_key = self::getChannelKey($channel);
            if ('sub' == $operate) {
                $client->zadd($channel_key, time(), $fd, function (swoole_redis $client, $result) use ($server, $fd, $channel, $operate) {
                    debug('zadd: ', $result);
                    if ($result) {
                        $server->push($fd, json_encode(['error_code' => 0, 'error_reason' => "{$operate} {$channel} success"]));
                    } else {
                        $server->push($fd, json_encode(['error_code' => -1, 'error_reason' => "{$operate} {$channel} failed"]));
                    }
                });
            } elseif ('unSub' == $operate) {
                $client->zrem($channel_key, $fd, function (swoole_redis $client, $result) use ($server, $fd, $channel, $operate) {
                    debug('zrem: ', $result);
                    if ($result) {
                        $server->push($fd, json_encode(['error_code' => 0, 'error_reason' => "{$operate} {$channel} success"]));
                    } else {
                        $server->push($fd, json_encode(['error_code' => -1, 'error_reason' => "{$operate} {$channel} fail"]));
                    }
                });
            } else {
                $server->push($fd, json_encode(['error_code' => -1, 'error_reason' => '参数错误']));
            }
        });


    }


}