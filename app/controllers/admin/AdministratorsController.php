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

    function bindMailAction()
    {
        $id = $this->session->get('admin_id');
        $admin = \Administrators::findFirstById($id);
        $this->view->admin = $admin;
    }


    function sendMailAction()
    {
        $id = $this->session->get('admin_id');
        $mail = $this->request('mail_address');
        $type = $this->request('type');
        $redis = \McRedis::getInstance('127.0.0.1:6389');
        if (in_array($type, ['bind', 'find'])) {
            $rand_code = mt_rand(1000, 9999);
            $key = 'mail_' . $type . '_' . $id;
            $redis->set($key, $rand_code);
            \Mailer::send('MCTools: ' . $type, $mail, "验证码: {$rand_code}");
        }
    }

    function loginAction()
    {
        $result = [];
        $user_name = $this->request('username');
        $password = $this->request('password');
        $result['username'] = $user_name;
        $result['password'] = $password;
        $result['role'] = 'admin';
        debug('params: login', $user_name, $password);
        $admin = \Administrators::register($user_name, $password, 'admin');
        if ($admin) {
            $this->session->set('admin_id', $admin->id);
            return $this->respJson(0, '登录成功', ['redirect_url' => $admin->redirect_url]);
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
                return $this->respJson(0, '登录成功', ['redirect_url' => $admin->redirect_url]);
            }
            return $this->respJson(-1, '账号或密码错误');
        }
    }

    function updatePasswordAction()
    {

    }

    function logoutAction()
    {
        $this->session->set('admin_id', null);
        $this->response->redirect('/admin');
    }
}