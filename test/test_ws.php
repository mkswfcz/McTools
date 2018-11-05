<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/5
 * Time: 上午10:10
 */
require "test_base.php";

function ws($argv)
{
    if (isset($argv[1])) {
        $type = $argv[1];
        if ('client' == $type) {
            $client = new wsClient('127.0.0.1', 9051);
            $client->sync();
        } else {
            $server = new webSocket(9501);
        }
    }
}

ws($argv);

