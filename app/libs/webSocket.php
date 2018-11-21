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
//            'daemonize' => 1,
            'worker_num' => 1,
//            'log_file' => APP_ROOT . '/logs/ws.log'
        ]);
        $this->server->on('open', [$this, 'onOpen']);
        $this->server->on('message', [$this, 'onMessage']);
        $this->server->on('workerStart', [$this, 'workerStart']);
        $this->server->on('close', [$this, 'onClose']);

        echoTip("Server start: {$point}");
        $this->server->start();
    }

    function isConnect($server, $fd)
    {
        return $server->connection_info($fd);
    }

    function onOpen(swoole_websocket_server $server, $request)
    {
        echoTip("ws server: handshake success with fd: {$request->fd}");
    }

    function apply($server, $fd, $nick_name)
    {
        $chat_list = 'chat_user_list';
        $redis = McRedis::getInstance(self::$redis_endpoint);

        $redis->sadd($chat_list, $fd);
        $redis->set($fd, $nick_name . '_' . $fd);
        $server->push($fd, json_encode(['msg_type' => '系统消息', 'error_code' => 0, 'error_reason' => '申请成功,您的昵称:' . $nick_name]));

        $exists_users = $redis->smembers($chat_list);
        if (count($exists_users) > 0) {
            foreach ($exists_users as $user_fd) {
                if ($user_fd != $fd && $this->isConnect($server, $user_fd)) {
                    $server->push($user_fd, json_encode(['msg_type' => '系统消息', 'error_reason' => "{$nick_name} 加入群聊!"]));
                }
            }
        }
    }

    function speak($server, $fd, $content)
    {
        $redis = McRedis::getInstance(self::$redis_endpoint);
        $nick_name = $redis->get($fd);
        $u_fds = $redis->smembers('chat_user_list');
        foreach ($u_fds as $u_fd) {
            if ($this->isConnect($server, $u_fd)) {
                $server->push($u_fd, json_encode(['msg_type' => "{$nick_name}", 'error_reason' => "{$content}"]));
            }
        }
    }

    function handleChat($server, $fd, $data)
    {
        $new_data = json_decode($data, true);
        if ($new_data) {
            if (isset($new_data['nick_name'])) {
                $this->apply($server, $fd, $new_data['nick_name']);
            }
            debug('speak: ', $data);
        } else {
            $this->speak($server, $fd, $data);
        }
    }

    function onMessage(swoole_websocket_server $server, $frame)
    {
        echoTip("ws receive from {$frame->fd}:{$frame->data}, opcode:{$frame->opcode}, fin:{$frame->finish}");
//        self::sub($server, $frame->fd, $frame->data);
        $this->handleChat($server, $frame->fd, $frame->data);
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
            if (false === $info) {
                foreach ($fds as $fd) {
                    $redis->zrem($channel_key, $fd);
                }
            }
            foreach ($fds as $fd) {
                if ($this->isConnect($server, $fd)) {
                    $server->push($fd, json_encode($data));
                }
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
        if ($redis->sismember('chat_user_list', $fd)) {
            $redis->srem('chat_user_list', $fd);
            $redis->del($fd);
        }

        $channel_keys = $redis->smembers('message_type_channel_list');
        if (count($channel_keys) > 0) {
            foreach ($channel_keys as $channel_key) {
                $redis->zrem($channel_key, $fd);
            }
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
            return false;
        }

        $channel_key = self::getChannelKey($data['message_type']);
        $redis->sadd('message_type_channel_list', $channel_key);
        $success_response = json_encode(['error_code' => 0, 'error_reason' => "{$data['operate']} {$data['message_type']} success"]);
        $fail_response = json_encode(['error_code' => -1, 'error_reason' => "{$data['operate']} {$data['message_type']} failed"]);
        debug('operate: ', $data['operate'], $fd);
        if ('sub' == $data['operate']) {
            $result = $redis->zadd($channel_key, time(), $fd);
            debug('sub: ', $result);
            if ($result && $redis->z) {
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