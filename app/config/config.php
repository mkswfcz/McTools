<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/11
 * Time: 上午11:31
 */
define('APP_DIR', realpath(__DIR__ . '/../'));
return new Phalcon\Config([
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
        'file' => APP_DIR . '/logs/main.log'
    ]
]);