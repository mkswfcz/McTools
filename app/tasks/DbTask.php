<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/22
 * Time: 上午10:11
 */
class DbTask extends Phalcon\Cli\Task
{
    function testAction()
    {
        $article = Articles::findLast();
        debug($article);
    }
}