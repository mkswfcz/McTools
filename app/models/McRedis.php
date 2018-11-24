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
        if ('ssdb' == $type) {
            $this->_type = $type;
            $this->_cache = new SSDB($host, $port, $timeout);
        }
    }

    #127.0.0.1:6379
    static function getInstance($point)
    {
        if (!isset(self::$_map[$point])) {
            list($type, $endpoint) = explode('//', $point);
            list($host, $port) = explode(':', $endpoint);

            $redis = new self($host, $port, 5, $type);
            self::$_map[$endpoint] = $redis;

            return $redis;
        }
        return self::$_map[$point];
    }

    function __call($name, $arguments)
    {
        #... 将数组和可遍历对象展开为函数参数
        return call_user_func([$this->_cache, $name], ...$arguments);
    }

    function isRedis()
    {
        return 'redis' == $this->_type;
    }

    function isSsdb()
    {
        return 'ssdb' == $this->_type;
    }

    function lock($source, $ttl = 10)
    {
        $lock = 'cache_lock_' . $source;
        while (true) {
            $result = $set = $expire = false;
            if ($this->isRedis()) {
                $result = $this->set($lock, time() + $ttl, ['nx', 'ex' => $ttl]);
            } elseif ($this->isSsdb()) {
                return false;
//                if (!$this->exists($lock)) {
//                    $set = $this->set($lock, time() + $ttl);
//                    $expire = $this->expire($lock, $ttl);
//                    debug($lock, $set, $expire);
//                }
            } else {
                return false;
            }

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