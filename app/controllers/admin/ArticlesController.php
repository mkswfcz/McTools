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
        $result = \Articles::createArticles();
        echo 'create: article namespace' . PHP_EOL;
    }

    function indexAction()
    {
        $article = \Articles::findFirstById(1);
        if ($article) {
            $json = $article->toJson();
        }
        debug($this->session->get('admin_id'));
        $this->view->build = 'this is namespace/articles/index';
    }

    function testAction()
    {
        $user_name = $this->session->get('user_name');
        $this->view->test = 'namespace/testAction' . $user_name;
        $article = \Articles::findLast();
        debug($article);
    }
}