<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:05
 */
namespace admin;
class ArticlesController extends BaseController
{
    function createAction()
    {
//        $result = Articles::createArticles();
        echo 'create: article admin' . PHP_EOL;
    }

    function indexAction()
    {
        $article = \Articles::findFirstById(1);
        $json = $article->toJson();
        debug('admin index');
        $this->view->build=23;
//        debug($json);
//        info(date('Y-m-d H:i:s', time()), $json);
//        debug(get('https://www.okex.com/api/v1/ticker.do'));
//        debug(post('https://www.okex.com/api/v1/userinfo.do'));
    }
}