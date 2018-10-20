<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/20
 * Time: 下午3:44
 */
namespace front;
class InitController extends \BaseController
{
    function indexAction()
    {
        $this->view->tag = 'Welcome!';
    }
}