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
        $this->view->type = 'bind';
    }


    function sendMailAction()
    {
        $id = $this->session->get('admin_id');
        $mail = $this->request('address');
        $type = $this->request('type', 'bind');
        $redis = \Administrators::getRedis();
        if (in_array($type, ['bind', 'find'])) {
            $rand_code = mt_rand(1000, 9999);
            $key = 'mail_' . $type . '_' . $id;
            $redis->set($key, $rand_code);
            $redis->expire($key, 60);
            \Mailer::send('MCTools: ' . $type, $mail, "验证码: {$rand_code}");
            debug('params: ', $id, $mail, $type);
            return $this->respJson(0, '发送成功');
        }
    }

    function authCodeAction()
    {
        $redis = \Administrators::getRedis();
        $admin_id = $this->request('id');
        $type = $this->request('type');
        $receive_code = $this->request('code');
        $send_code = $redis->get('mail_' . $type . '_' . $admin_id);

        debug('params: ', $admin_id, $type, $receive_code, $send_code,$this->request('address'));
        if ($send_code === $receive_code) {
//            return $this->respJson(0, '邮箱绑定成功', ['error_url' => '/admin/init']);
            $admin = \Administrators::findFirstById($admin_id);
            $admin->email = $this->request('mail_address');
            $admin->update();
            return $this->response->redirect('/admin/init');
        }
        return $this->respJson(-1, '绑定失败');
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