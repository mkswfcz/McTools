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

        $admin = \Administrators::register($user_name, $password, 'admin');
        if ($admin) {
            $this->session->set('admin_id', $admin->id);
            return $this->respJson(0, '登录成功',['redirect_url'=>$admin->redirect_url]);
        } else {
            $cond['conditions'] = 'username = :u_name: and password=:pwd:';
            $cond['bind'] = ['u_name' => $user_name, 'pwd' => md5($password)];
            $admin = \Administrators::findFirstBy($cond);
            if ($admin) {
                $id = $this->session->get('admin_id');
                if ($id === $admin->id) {
                    return $this->respJson(-1, '已登录');
                }
                $this->session->set('admin_id', $admin->id);
                return $this->respJson(0, '登录成功',['redirect_url'=>$admin->redirect_url]);
            }
            return $this->respJson(-1, '账号或密码错误');
        }
    }

    function logoutAction()
    {
        $this->session->set('admin_id', null);
        $this->response->redirect('/admin');
    }
}