<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/8
 * Time: 下午4:16
 */

use Swoole\Table;

class SwooleTable
{
    public $cache_table = null;

    public $row;

    public static $tables = array();

    public function __construct($conflict_proportion)
    {
        $row = getConfig('swoole_table_row');
        $this->row = $row;
        $this->cache_table = new Swoole\Table($row, $conflict_proportion);
    }

    static function getInstance($conflict_proportion = 0.2)
    {
        $pid = posix_getpid();
        if (isset(self::$tables[$pid])) {
            return self::$tables[$pid];
        } else {
            $table = new self($conflict_proportion);
            self::$tables[$pid] = $table;
            return $table;
        }
    }

    function getMemorySize()
    {
        return $this->cache_table->momorySize;
    }

    function __call($name, $arguments)
    {
        switch ($name) {
            case 'create':
                $result = call_user_func([$this->cache_table, $name]);
                break;
            default:
                debug('memory: ',$this->cache_table->memorySize);
                $result = call_user_func([$this->cache_table, $name], ...$arguments);
                break;
        }
        return $result;
    }
}