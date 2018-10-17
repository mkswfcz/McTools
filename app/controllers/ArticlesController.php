<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:05
 */

class ArticlesController extends BaseController
{
    function beforeAction()
    {
    }

    function createAction()
    {
//        $result = Articles::createArticles();
        debug($this->request('id'));
        echo 'origin create: article1' . PHP_EOL;
    }

    function indexAction()
    {
        $article = \Articles::findFirstById(1);
        $json = $article->toJson();
//        debug($json);
//        var_dump('origin ');
//        info(date('Y-m-d H:i:s', time()), $json);
//        debug(get('https://www.okex.com/api/v1/ticker.do'));
//        debug(post('https://www.okex.com/api/v1/userinfo.do'));
        $this->view->setVar('title','origin create root');
    }
}