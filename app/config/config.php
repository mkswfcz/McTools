<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/11
 * Time: 上午11:31
 */

return new Phalcon\Config([
    'dirs' => [
        'config' => APP_ROOT . '/app/config/',
        'libs' => APP_ROOT . '/app/libs/'
    ],
    'database' => [
        'adapter' => 'PostgresSQL',
        'host' => 'localhost',
        'username' => 'postgres',
        'password' => '',
        'port' => 5432,
        'dbname' => 'mc_tools'
    ],
    'logger' => [
        'line' => [
            'format' => '[PID ' . posix_getpid() . ' %date%][%type%] %message%',
            'dateFormat' => 'Y-m-d H:i:s',
        ],
        'file' => APP_ROOT. '/app/logs/main.log'
    ]
]);