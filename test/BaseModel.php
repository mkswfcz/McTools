<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/19
 * Time: 下午9:36
 */

class BaseModel
{
    public static $connections_map = array();

    public static $cache_map = array();

    public static $properties = array();

    public static $cache_object_hash = true;

    public $snapshot_hash_object;

    function __construct()
    {
        foreach (self::$properties as $property => $type) {
            $this->$property = null;
        }
    }

    function getProperties()
    {
        return self::$properties;
    }

    static function getRedis($endpoint)
    {
        #127.0.0.1:9876
        list($host, $port) = explode(':', $endpoint);
        $index = 'redis_connect_' . posix_getpid();

        if (isset(self::$cache_map[$index])) {
            return self::$cache_map[$index];
        }

        $redis = new Redis();
        try {
            $redis->connect($host, $port);
            self::$cache_map[$index] = $redis;
            return $redis;
        } catch (Exception $e) {
            self::echoLog($e->getMessage());
        }

    }

    static function getDbConnect()
    {
        $handle = "host=127.0.0.1 port=5432 dbname=otceasy_development user=postgres password=";
        $index = 'pg_connect_' . posix_getpid();
        if (isset(self::$connections_map[$index])) {
            return self::$connections_map[$index];
        }

        $db_connect = pg_connect($handle);
        if (!$db_connect) {
            echo "connect failed!\n";
        } else {
            self::$connections_map[$index] = $db_connect;
            return $db_connect;
        }
    }

    static function uncamelize($camelize_word, $separator = '_')
    {
        $camelize_word = lcfirst($camelize_word);
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', $camelize_word));
    }

    static function echoLog($log)
    {
        if (is_array($log) || is_object($log)) {
            $log = json_encode($log, JSON_UNESCAPED_UNICODE);
        }
        echo $log . "\n";
    }


    static function getDataBaseColumns()
    {
        $table = self::uncamelize(get_called_class());
        if (isset(self::$properties[$table])) {
            return self::$properties[$table];
        }
        $connect = self::getDbConnect();
        $sql = "SELECT a.attnum,a.attname AS field,t.typname AS type,a.attlen AS length,a.atttypmod AS lengthvar,a.attnotnull AS notnull
            FROM pg_class c,pg_attribute a,pg_type t WHERE c.relname = '$table' and a.attnum > 0 and a.attrelid = c.oid and a.atttypid = t.oid
            ORDER BY a.attnum";
        $res = pg_query($connect, $sql);
        $construct = pg_fetch_all($res);
        $columns = [];
        foreach ($construct as $value) {
            $columns[$value['field']] = $value['type'];
            if (!isset(self::$properties[$table][$value['field']])) {
                self::$properties[$table][$value['field']] = $value['type'];
            }
        }
        return $columns;
    }

    function save()
    {
        if ($this->id) {
            $data = self::find(['id' => $this->id]);
            if (count($data) > 1) {

            }
            #sql update
        } else {
            self::echoLog('sql create');
        }
    }

    function setSnapshotHash($object)
    {
        $this->snapshot_hash_object = $object;
    }

    function execute($sql)
    {
        $db_connect = self::getDbConnect();
        $res = pg_query($db_connect, $sql);
        $data = pg_fetch_all($res);
        return $data;
    }

    static function find($params)
    {
        $connect = self::getDbConnect();

        $clazz = get_called_class();
        $database_model = self::uncamelize($clazz);
//        self::echoLog('clazz: ' . $database_model);

        $database_columns = self::getDataBaseColumns();
        $properties = array_keys($database_columns);

        $query_param = '';
        foreach ($params as $property => $value) {
            if (in_array($property, $properties)) {
                $query_param .= ' ' . $property . '=' . '\'' . $value . '\' and';
            }
        }
        $query_param = substr($query_param, 0, strlen($query_param) - 3);
        $sql = "select * from $database_model where" . $query_param;
        if (isset($params['order'])) {
            $sql .= ' order by ' . $params['order'];
        }
        if (isset($params['limit'])) {
            $sql .= ' limit ' . $params['limit'];
        }
        self::echoLog('sql: ' . $sql);
        $res = pg_query($connect, $sql);
        $data = pg_fetch_all($res);

        $objects = [];
        foreach ($data as $value) {
            $object = new $clazz();
            $object->setSnapshotHash($value);

            foreach ($value as $property => $v) {
                $object->$property = $v;
            }
            $objects[] = $object;
        }
        return $objects;
    }

}