<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/18
 * Time: 下午4:41
 */

namespace admin;
class AdministratorsController extends \BaseController
{
    function indexAction()
    {

    }

    function loginAction()
    {
        $result = [];
        $result['username'] = $this->request('username');
        $result['password'] = $this->request('password');
        return $this->respJson(0,'',$result);
    }
}