<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/11
 * Time: 上午11:31
 */
return new Phalcon\Config([
    'database' => [
        'adapter' => 'PostgresSQL',
        'host' => 'localhost',
        'username' => 'postgres',
        'password' => '',
        'port' => 5432,
        'dbname' => 'mc_tools'
    ]
]);