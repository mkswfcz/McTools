<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:05
 */

namespace admin;
class ArticlesController extends \BaseController
{
    function createAction()
    {
        //$result = Articles::createArticles();
        echo 'create: article admin' . PHP_EOL;
    }

    function indexAction()
    {
        $article = \Articles::findFirstById(1);
        $json = $article->toJson();
        $this->view->build = 'this is admin/articles/index';
    }

    function testAction()
    {
        $this->view->test = 'admin/testAction';
    }
}