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

    static function findLast($conditions = array())
    {
        if (empty($conditions)) {
            $conditions['order'] = 'id desc';
            $conditions['limit'] = 1;
        }
        return self::find($conditions);
    }
}