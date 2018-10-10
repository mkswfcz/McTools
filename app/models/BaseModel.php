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
}