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
        $result= Articles::createArticles();
        echo 'Create Result:  '.$result.PHP_EOL;
    }
    function indexAction(){
        $articles = Articles::find();
    }
}