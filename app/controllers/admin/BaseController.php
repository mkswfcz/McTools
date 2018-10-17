<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午6:04
 */

namespace admin;

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;

class BaseController extends Controller
{
    function beforeExecuteRoute($dispatcher)
    {
        $action = $dispatcher->getActionName();
        $controller = $dispatcher->getControllerName();
        if (!$this->request('role')) {
            $role = 'root';
        } else {
            $role = $this->request('role');
        }
        if (!$this->isAllowed($role, $controller, $action)) {
            return $this->respJson(0, 'access not allowed', ['role' => $role]);
        }
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

    function respJson($error_code, string $error_reason, array $data)
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


}