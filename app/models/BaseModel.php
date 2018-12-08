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

    function getMc()
    {
        return 'mc_tools';
    }

    function isBindModel($property)
    {
        return property_exists($this, $property);
    }

    function __get($name)
    {
        $camel_name = Phalcon\Text::camelize($name);
        $get_method = 'get' . $camel_name;
        if (method_exists($this, $get_method)) {
            return $this->$get_method();
        }
        #bind id to reset obj property
        $model_id = $name . '_id';
        if ($this->isBindModel($model_id)) {
            if (class_exists($camel_name . 's')) {
                $clazz = $camel_name . 's';
            }
            if (class_exists($camel_name . 'es')) {
                $clazz = $camel_name . 'es';
            }
            $model = $clazz::findFirstById($this->$model_id);
            $this->$name = $model;
            return $this->$name;
        }

    }


    function toJson()
    {
        $columns = $this->getColumns();
        $json = [];
        foreach ($columns as $column) {
            $json[$column] = $this->$column;
        }
        $json['mc'] = $this->mc;
        return $json;
    }

    function beforeUpdate()
    {
        $this->updated_at = time();
    }

    static function getMethods($object)
    {
        $reflect = new ReflectionClass($object);
        $methods = $reflect->getMethods();
        return $methods;
    }

    static function isCountAble($object)
    {
        if ($object instanceof Countable) {
            return true;
        }
        return false;
    }

    static function bindModel($class, $name, $arguments)
    {
        debug('params: ', $class, $name, $arguments);
        if (count($arguments) === 1) {
            $arguments = current($arguments);
        }
        $result = call_user_func('parent::' . $name, $arguments);
        if ('findFirst' == $name && !isNull($result)) {
            return $result;
        }
        if (self::isCountAble($result) && count($result) != 0) {
            $objects = [];
            foreach ($result as $value) {
                $object = new $class();
                foreach ($value as $k => $v) {
                    $object->$k = $v;
                }
                $objects[] = $object;
            }
        }
        return false;
    }

    static function __callStatic($name, $arguments)
    {
        $class = get_called_class();
        debug($name, $arguments, strpos($name, 'findFirstBy'));
        if (false !== strpos($name, 'findFirstBy')) {
            $column = uncamelize(strtolower(str_replace('findFirstBy', '', $name)));
            debug('column: ', $column);
            if ($name === 'findFirstBy') {
                return self::bindModel($class, 'findFirst', $arguments);
            }
            $objects = self::bindModel($class, 'findFirst',
                [
                    'conditions' => "{$column} =:{$column}:",
                    'bind' => [strtolower($column) => $arguments[0]]
                ]
            );
            return $objects;
        }
        if (0 === strpos($name, 'find')) {
            $objects = self::bindModel($class, $name, $arguments);
            debug('ob: ', $class);
            return $objects;
        }
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

    static function getSsdb()
    {
        $ssdb = getConfig('cache')->ssdb;
        return McRedis::getInstance($ssdb->endpoint);
    }

    static function findLast($conditions = array())
    {
        if (empty($conditions)) {
            $conditions = ['order' => 'id desc', 'limit' => 1];
        }
        $result = self::find($conditions);
        if (count($result) > 0) {
            return $result[0];
        }
        return false;
    }

    static function findFirst($conditions = array())
    {
        if (empty($conditions)) {
            $conditions = ['order' => 'id asc', 'limit' => 1];
        }
        $result = self::find($conditions);
        if (count($result) > 0) {
            return $result[0];
        }
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