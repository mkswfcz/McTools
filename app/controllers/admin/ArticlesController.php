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

    function editAction()
    {
        $id = $this->request('id');
        $article = \Articles::findFirstById($id);
        debug('edit: ', $id, $article);
        $this->view->article = $article;
    }

    function updateAction()
    {
        $id = $this->request('id');
        $title = $this->request('title');
        $content = $this->request('content');
        $article = \Articles::findFirstById($id);
        $article->title = $title;
        $article->content = $content;
        $article->update();
        $this->response->redirect('/admin/articles');
    }

    function indexAction()
    {
        $current_page = $this->request('page', 0);
        $per_page = $this->request('per_page', 10);
        $conditions['offset'] = $current_page * $per_page;
        $conditions['limit'] = $per_page;
        $conditions['order'] = 'id asc';
        $articles = \Articles::find($conditions);
        $this->view->last_page = $current_page > 0 ? $current_page - 1 : $current_page;
        $this->view->next_page = $current_page + 1;
        debug('page: ', $this->view->last_page, $this->view->next_page, $current_page);
        $this->view->articles = $articles;
    }

    function showAction()
    {
        $id = $this->request('id');
        $article = \Articles::findFirstById($id);
        $this->view->article = $article;
    }

    function testAction()
    {
        $user_name = $this->session->get('user_name');
        $this->view->test = 'namespace/testAction' . $user_name;
        $article = \Articles::findLast();
        debug($article);
    }
}