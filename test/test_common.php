<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/15
 * Time: 上午10:22
 */
$path = realpath(__DIR__.'/../public/index.php');
require "{$path}";
debug('time: ',myDate(),myDate('2018-10-15 10:54:57','digital'));
debug(Articles::findFirstById(1));