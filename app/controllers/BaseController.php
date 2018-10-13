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
    function beforeExecuteRoute($dispatcher)
    {
        $action = $dispatcher->getActionName();
        $controller = $dispatcher->getControllerName();
        if (!$this->request('role')) {
            $role = 'admin';
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
        $acl->setDefaultAction(Acl::DENY);
        $acl_list = require ROOT_DIR . '/app/config/acl.php';
        foreach ($acl_list as $role => $list) {
            $role_object = new Role($role);
            $acl->addRole($role_object);
            foreach ($list as $controller => $action) {
                $resource = new Resource(strtolower($controller));
                $acl->addResource($resource, $action);
                $acl->allow($role, strtolower($controller), $action);
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

    function log($content)
    {
        if (!is_string($content)) {
            $content = json_encode($content);
        }
        $app_root = realpath(__DIR__ . '/../../') . '/';
        $traces = debug_backtrace();
        $real_traces = current($traces);

        $logger = $this->getDi()->get('logger');
        $file = str_replace($app_root, '', $real_traces['file']);
        $log_text = "[{$file}=>{$real_traces['line']}]" . $content;
        $logger->info($log_text);
    }
}