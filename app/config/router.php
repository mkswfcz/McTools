<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/16
 * Time: 下午3:35
 */
function getControllerDir()
{
    return APP_ROOT . '/app/controllers/';
}

function getSpaceDirs($no_prefix = false)
{
    $space_dirs = [];
    $controller_dir = getControllerDir();
    $dirs = glob($controller_dir . '*');
    foreach ($dirs as $dir) {
        if (is_dir($dir)) {
            if ($no_prefix) {
                $dir = str_replace($controller_dir, '', $dir);
            }
            $space_dirs[] = $dir;
        }
    }
    return $space_dirs;
}

function loadRouters()
{
    $dir_names = getSpaceDirs(true);
    $controller_dir = getControllerDir();
    $namespaces['app'] = $controller_dir;
    foreach ($dir_names as $namespace) {
        $space_path = $controller_dir . $namespace . '/';
        $namespaces[$namespace] = $space_path;
    }
    return $namespaces;
}

function parseUri($uri)
{

    $uri_parts = explode('/', $uri);
    $uri_parts = array_filter($uri_parts);
    $count = count($uri_parts);
    $default_page = getConfig('default_page');
    if ($count === 0) {
        return ['front', $default_page->front->controller, $default_page->front->action];
    }
    switch ($count) {
        case 1:
            $space_key = -1;
            $controller_key = 1;
            $action_key = 'index';
            break;
        case 2:
            if (!in_array($uri_parts[1], getSpaceDirs(true))) {
                $space_key = '';
                $controller_key = 1;
                $action_key = 2;
            } else {
                $space_key = 1;
                $controller_key = 2;
                $action_key = 'index';
            }
            break;
        default :
            $space_key = 1;
            $controller_key = 2;
            $action_key = 3;
    }
    $namespace = getValue($space_key, $uri_parts, '');
    $controller = getValue($controller_key, $uri_parts, '');
    $action = getValue($action_key, $uri_parts, '');
    if ($action_key == 'index') {
        $action = 'index';
    }
    debug('controller: ',$controller);
    if (empty($namespace)) {
        if (!class_exists($controller.'Controller')) {
            $namespace = 'front';
        }
        if ($controller == 'admin') {
            $namespace = 'admin';
            $controller = $default_page->admin->controller;
            $action = $default_page->admin->action;
        }
    }
    return [$namespace, $controller, $action];
}

