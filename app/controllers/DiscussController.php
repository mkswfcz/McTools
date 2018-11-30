<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/30
 * Time: 下午2:09
 */
class DiscussController extends BaseController
{
    function indexAction()
    {
        $this->response->redirect('/talkclient.html');
    }
}