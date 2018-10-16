<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/16
 * Time: 下午8:27
 */
class HandlerController extends BaseController
{
    function route404Action()
    {
        $this->view->disable();
        echo '404 NotFound (Controller Not Exists)';
    }
}