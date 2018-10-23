<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/23
 * Time: 上午11:37
 */
class McRedis
{
    private $_type;
    private $_cache;
    private static $_map = array();

    function __construct($host, $port, $timeout = 5, $type = 'redis')
    {
        if ('redis' == $type) {
            $this->_type = $type;
            $this->_cache = new Redis();
            $this->_cache->connect($host, $port, $timeout);
        }
    }

    #127.0.0.1:6379
    static function getInstance($endpoint)
    {
        if (!isset(self::$_map[$endpoint])) {
            list($host, $port) = explode(':', $endpoint);
            $redis = new self($host, $port);
            self::$_map[$endpoint] = $redis;
            return $redis;
        }
        return self::$_map[$endpoint];
    }

    function __call($name, $arguments)
    {
        #... 将数组和可遍历对象展开为函数参数
        return call_user_func([$this->_cache,$name ], ...$arguments);
    }

    function lock($source, $ttl = 10)
    {
        $lock = 'cache_lock_' . $source;
        while (true) {
            $result = $this->set($lock, time() + $ttl, ['nx', 'ex' => $ttl]);
            if (!$result) {
                usleep(10);
            } else {
                break;
            }
        }
        return $lock;
    }

    function unlock($lock)
    {
        $this->del($lock);
    }


}