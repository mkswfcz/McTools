<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午5:30
 */

use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Router;

$define = realpath(__DIR__ . '/../app/config/defined.php');
require "{$define}";

require APP_ROOT . '/app/config/services.php';
require APP_ROOT . '/vendor/autoload.php';
$loader = new Loader();
$loader->registerDirs(
    [
        APP_ROOT . '/app/controllers',
        APP_ROOT . '/app/models',
        APP_ROOT . '/app/views',
        APP_ROOT . '/app/tasks',
        APP_ROOT . '/app/libs',
        APP_ROOT . '/app/test'
    ]
);

$controller_dir = APP_ROOT . '/app/controllers/';
$dirs = glob($controller_dir . '*');
$namespaces = [];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $name_space = str_replace($controller_dir, '', $dir);
        $namespaces[$name_space] = [$controller_dir . $name_space];
    }
}
$namespaces['app'] = $controller_dir;
if (!empty($namespaces)) {
    var_dump($namespaces);
    $loader->registerNamespaces($namespaces);
}
$loader->register();

$application = new Application($di);

#cli handle IndexController not Found
if (!isCli()) {
    try {
        $response = $application->handle();
        $response->send();
    } catch (Exception $e) {
        debug("Exception :" . $e->getMessage());
    }
}

