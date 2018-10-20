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

    #动态加载静态文件
    function setStaticFiles($namespace, $controller, $action, $file_type = 'css')
    {
        $method = 'add' . ucwords($file_type);
        $public_files = glob(APP_ROOT . '/public/' . $file_type);
        foreach ($public_files as $public_file) {
            $this->assets->$method($public_file);
        }
        if (!$namespace) {
            return false;
        }
        $css_root = $file_type . '/' . $namespace . '/';
        $extension = '.' . $file_type;

        $css_files = [];
        $css_files[] = $css_root . $namespace . $extension;
        $css_files[] = $css_root . $controller . '/' . $controller . $extension;
        $css_files[] = $css_root . $controller . '/' . $action . $extension;

        foreach ($css_files as $file) {
            if (file_exists($file)) {
                $this->assets->$method($file);
            }
        }
    }

    function beforeExecuteRoute($dispatcher)
    {
        list($namespace, $controller, $action) = $this->parseDispatcher($dispatcher);
        if (!$this->request('role')) {
            $role = 'root';
        } else {
            $role = $this->request('role');
        }
        if (!$this->isAllowed($role, $controller, $action)) {
            return $this->respJson(0, 'access not allowed', ['role' => $role]);
        }
        $this->setStaticFiles($namespace, $controller, $action);
        $this->setStaticFiles($namespace, $controller, $action, 'js');
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

    function respJson($error_code, string $error_reason, array $data = array())
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

    function request($key, $default = null)
    {
        if (isset($_REQUEST[$key])) {
            return $_REQUEST[$key];
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
            $this->view->setTemplateAfter($namespace);
            $this->view->pick($pick_view);
        } else {
            $pick_view = $controller . '/' . $action;
            $this->view->pick($pick_view);
        }
    }


}