<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:05
 */
class ArticlesController extends BaseController
{
    function createAction()
    {
//        $result = Articles::createArticles();
        echo 'create: article' . PHP_EOL;
    }

    function indexAction()
    {
        $article = Articles::findFirstById(1);
        $json = $article->toJson();
        $str = json_encode($json);
        $this->log($str);
    }
}