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
        $this->session->set('user_name','tom');
        $this->view->build = 'this is admin/articles/index';
    }

    function testAction()
    {
        $user_name = $this->session->get('user_name');
        $this->view->test = 'admin/testAction'.$user_name;
    }
}