<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:04
 */

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;

class BaseController extends Controller
{
    function parseDispatcher($dispatch)
    {
        $namespace = $dispatch->getNamespaceName();
        $controller = $dispatch->getControllerName();
        $action = $dispatch->getActionName();
        return [$namespace, $controller, $action];
    }

    function getReqIp()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }

    function loadRemoteSource()
    {
        $remote_static = getConfig('remote_static');
        foreach ($remote_static as $type => $uris) {
            if (!isNull($uris)) {
                $method = 'add' . strtoupper($type);
                foreach ($uris as $uri) {
                    $this->assets->$method('//' . $uri);
                }
            }
        }
    }

    function loadPublicStatic()
    {
        $static_types = ['css', 'js'];

        foreach ($static_types as $static_type) {
            $method = 'add' . ucwords($static_type);
            foreach (getConfig($static_type) as $file) {
                $static_path = $static_type . '/' . $file;
                $this->assets->$method($static_path);
                debug('load static public: ', $static_path);
            }
        }
        $this->assets->collection('footer')->addJs('js/mc.js');
    }

    #动态加载静态文件 代优化
    function setStaticFiles($namespace, $controller, $action, $file_type = 'css')
    {
        $method = 'add' . ucwords($file_type);
//        if (!$namespace) {
//            return false;
//        }
        $static_root = $file_type . '/' . $namespace . '/';
        $extension = '.' . $file_type;

        $static_files = [];
        $static_files[] = $static_root . $namespace . $extension;
        $static_files[] = $static_root . $controller . '/' . $controller . $extension;
        $static_files[] = $static_root . $controller . '/' . $action . $extension;

        $dir = APP_ROOT . '/public/' . $file_type . '/' . $namespace . '/' . $controller;
        if (empty($namespace)) {
            $dir = APP_ROOT . '/public/' . $file_type . '/' . $controller;
        }
        $controller_static_dir = realpath($dir);
        debug('dir: ', $namespace, $controller, $action);
        if (is_dir($controller_static_dir)) {
            $files = glob($controller_static_dir . '/*.' . $file_type);
            foreach ($files as $item) {
                $item = str_replace(APP_ROOT . '/public/', '', $item);
                $this->assets->$method($item);
                debug('load static: ', $item);
            }
        }

        foreach ($static_files as $file) {
            $file = str_replace('//', '/', $file);
            if (file_exists($file)) {
                $this->assets->$method($file);
                debug('load static: ', $file);
            }
        }
    }

    function isDefaultPage($dispatcher)
    {
        list($namespace, $controller, $action) = $this->parseDispatcher($dispatcher);
        debug($namespace, $controller, $action);
        if ($namespace == 'admin' && $controller == 'administrators' && $action == 'index') {
            return true;
        }
        return false;
    }

    function beforeAction($dispatcher)
    {
        $this->view->default = false;
        if ($this->isDefaultPage($dispatcher)) {
            $this->view->default = true;
        }
        list($namespace, $controller, $action) = $this->parseDispatcher($dispatcher);
        if (!$this->session->get('admin_id') && !$this->isDefaultPage($dispatcher) && 'admin' == $namespace && $action != 'login') {
            $this->response->redirect('/admin');
            return false;
        }
        debug('beforeAction: ', $this->view->default, $this->session->get('admin_id'));
    }

    function beforeExecuteRoute($dispatcher)
    {
        list($namespace, $controller, $action) = $this->parseDispatcher($dispatcher);

        $default_role = getConfig('role_default');
        $role = $this->request('role', $namespace);
        $role = $role ? $role : $default_role;
        if (!$this->isAllowed($role, $controller, $action)) {
            return $this->respJson(0, 'access not allowed', ['role' => $role]);
        }
        #顺序
//        $this->loadRemoteSource();
        $this->loadPublicStatic();
        $this->setStaticFiles($namespace, $controller, $action);
        $this->setStaticFiles($namespace, $controller, $action, 'js');
        debug('beforeExecuteRoute: ', $controller, $action);
    }

    function isAllowed($role, $controller, $action)
    {
        $acl = $this->getAcl();
        $result = $acl->isAllowed($role, strtolower($controller), $action);
        if ($result != 1) {
            return false;
        }
        return true;
    }

    function respJson($error_code, $error_reason, array $data = array())
    {
        $result['error_code'] = $error_code;
        $result['error_reason'] = $error_reason;
        $result['data'] = $data;
        $json = json_encode($result);
        echo $json;
        return false;
    }

    #acl 实时load
    function getAcl()
    {
        $acl = new AclList();
        $acl->setDefaultAction(Acl::ALLOW);
        $acl_list = require APP_ROOT . '/app/config/acl.php';
        foreach ($acl_list as $role => $list) {
            $role_object = new Role($role);
            $acl->addRole($role_object);
            foreach ($list as $controller => $action) {
                $resource = new Resource(strtolower($controller));
                $acl->addResource($resource, $action);
//                debug($role,$controller,$action);
                $acl->deny($role, strtolower($controller), $action);
            }
        }
        return $acl;
    }

    function request($key = '', $default = null)
    {
        if (empty($key)) {
            return $_REQUEST;
        }
        if (isset($_REQUEST[$key])) {
            if (in_array($key, ['username', 'password', 'title', 'content'])) {
                $value = decode($_REQUEST[$key]);
                if (false == $value) {
                    return $_REQUEST[$key];
                }
                return $value;
            } else {
                return $_REQUEST[$key];
            }
        } else {
            return $default;
        }
    }

    #动态绑定视图
    function afterAction($dispatcher)
    {
        list($namespace, $controller, $action) = $this->parseDispatcher($dispatcher);
//        debug('after', $action, $namespace, $controller);
        #根据路由选择视图,view is_enable?
        $this->view->enable();
        if (!empty($namespace)) {
            $pick_view = $namespace . '/' . $controller . '/' . $action;
            if (file_exists(APP_ROOT . '/app/views/layouts/' . $namespace . '.volt')) {
                $this->view->setTemplateAfter($namespace);
            }
            debug('pick: ', $pick_view);
            $this->view->pick($pick_view);
        } else {
            $pick_view = $controller . '/' . $action;
            debug('pick: ', $pick_view);
            $this->view->pick($pick_view);
        }
    }


}