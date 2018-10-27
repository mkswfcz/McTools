<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午8:35
 */

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    function initialize()
    {
        $this->setConnectionService("db");
    }

    function toJson()
    {
        $columns = $this->getColumns();
        $json = [];
        foreach ($columns as $column) {
            $json[$column] = $this->$column;
        }
        return $json;
    }

    function getColumns()
    {
        $meta_data = $this->getModelsMetaData();
        $attributes = $meta_data->getAttributes($this);
        return $attributes;
    }

    static function getRedis()
    {
        $redis = getConfig('cache')->redis;
        return McRedis::getInstance($redis->endpoint);
    }

    static function findLast($conditions = array())
    {
        if (empty($conditions)) {
            $conditions['order'] = 'id desc';
            $conditions['limit'] = 1;
        }
        $result = self::query()
            ->orderBy('id desc')
            ->limit(1)
            ->execute();
        return $result;
    }

    /**
     * @param $method
     * @param array $arguments
     * 添加到异步队列
     */
    static function push($method, $arguments = array())
    {
        $class = get_called_class();
        $redis = self::getRedis();
        $task_id = 'task_id_' . uniqid('mc');
        $task['class'] = $class;
        $task['method'] = $method;
        $task['arguments'] = $arguments;
        $result = $redis->set($task_id, json_encode($task));
        if ($result) {
            $redis->zadd('async_task_list_key', time(), $task_id);
            debug('async_push:', $task_id, $task);
        }
    }
}