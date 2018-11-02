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
        debug('request: ', $this->request());
        debug('params: article: ', $title, $content);
        if (isNull($title) || isNull($content)) {
            return $this->respJson(-1, '标题或内容为空');
        }
        if ($article->save()) {
            return $this->respJson(0, '创建成功', ['redirect_url' => '/admin/articles']);
        }
        return $this->respJson(-1, '创建失败');
    }

    function indexAction()
    {
        $articles = \Articles::find();
        $this->view->articles = $articles;
    }

    function testAction()
    {
        $user_name = $this->session->get('user_name');
        $this->view->test = 'namespace/testAction' . $user_name;
        $article = \Articles::findLast();
        debug($article);
    }
}