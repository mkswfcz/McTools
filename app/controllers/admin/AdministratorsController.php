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
        $user_name = $this->request('username');
        $password = $this->request('password');
        $result['username'] = $user_name;
        $result['password'] = $password;
        $result['role'] = 'admin';
        if (\Administrators::register($user_name, $password, 'admin')) {
            return $this->respJson(0, '创建成功', $result);
        } else {
            return $this->respJson(-1, '已存在', []);
        }
    }
}