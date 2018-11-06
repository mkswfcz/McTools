<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/6
 * Time: 下午2:59
 */
class WsTask extends Phalcon\Cli\Task
{
    function startAction()
    {
        $server = new webSocket(9501);
    }
}