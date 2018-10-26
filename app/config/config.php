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
            'endpoint' => '127.0.0.1:6379'
        ]
    ],
    'remote_static' => [
        'js' =>
            [
                'crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/aes.js',
                'crypto-js.googlecode.com/svn/tags/3.0.2/build/rollups/md5.js',
                'crypto-js.googlecode.com/svn/tags/3.0.2/build/components/pad-zeropadding.js'
            ]
    ],
    'css' => [
        'bootstrap.min.css'
    ],
    'js' => [
        'jquery.min.js',
        'bootstrap.min.js',
        'vue.min.js'
    ],
    'default_page' => [
        'admin' => [
            'controller' => 'init',
            'action' => 'index'
        ],
        'front' => [
            'controller' => 'init',
            'action' => 'index'
        ]
    ],
    'async' => ['default' => 10],

]);