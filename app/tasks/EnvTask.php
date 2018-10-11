<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/11
 * Time: 下午4:20
 */
class EnvTask extends \Phalcon\Cli\Task
{
    public function helpAction()
    {
        echo 'Env Require: '.PHP_EOL;
        echo 'Php7.2.1(fpm) & Phalcon & Nginx & mysql/postgres & redis & ssdb...'.PHP_EOL;
    }
}