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
    function newAction()
    {

    }

    function createAction()
    {
        $title = $this->request('title');
        $content = $this->request('content');
        $article = new \Articles();
        $article->title = $title;
        $article->content = $content;
        if ($article->save()) {
            return $this->respJson(0, '创建成功');
        }
        return $this->respJson(-1,'创建失败');
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