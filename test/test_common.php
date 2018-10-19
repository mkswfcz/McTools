<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/15
 * Time: 上午10:22
 */
require "test_base.php";

debug('time: ', myDate(), myDate('2018-10-15 10:54:57', 'digital'));
$article = Articles::findFirstById(1);
$reflect = new ReflectionClass($article);
$methods = $reflect->getMethods();
//foreach ($methods as $method) {
//    var_dump($method->name);
//}

$volt = new voltFun();
$volt::dirLink(['title1'=>['u1'=>'#','u2'=>'#'],'title2'=>['x1'=>'#','x2'=>'#'],'title3'=>['z1'=>'#','z2'=>'#']]).PHP_EOL;