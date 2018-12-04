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
        'dir' => APP_ROOT . '/logs/',
        'file' => 'main.log'
    ],

    'cache' => [
        'redis' => [
            'endpoint' => 'redis//127.0.0.1:6379',
        ],
        'ssdb' => [
            'endpoint' => 'ssdb//127.0.0.1:8888'
        ],
        'ws_redis' => ['endpoint' => '127.0.0.1:6389']
    ],

    'css' => [
        'bootstrap.min.css'
    ],

    'js' => [
        'jquery.min.js',
        'bootstrap.min.js',
        'vue.min.js',
        'crypto-js.js',
        'jquery.form.js'
    ],

    'default_page' => [
        'admin' => [
            'controller' => 'administrators',
            'action' => 'index'
        ],
        'front' => [
            'controller' => 'init',
            'action' => 'index'
        ]
    ],

    'async' => ['default' => 10],

    'role_default' => 'root'
]);