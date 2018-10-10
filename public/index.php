<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午5:30
 */

use Phalcon\Loader;
use Phalcon\Mvc\view;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;

define('ROOT_DIR', realpath(__DIR__ . '/../'));

$loader = new Loader();
$loader->registerDirs(
    [
        ROOT_DIR . '/app/controllers',
        ROOT_DIR . '/app/models',
        ROOT_DIR . '/app/views'
    ]
)->register();

$di = new FactoryDefault();
$di->set('view', function () {
    $view = new View();
    $view->setViewsDir(ROOT_DIR . '/app/views');
    return $view;
});

$application = new Application($di);
try {
    $response = $application->handle();
    $response->send();
} catch (Exception $e) {
    echo "[" . __FILE__ . __LINE__ . "]Exception: " . $e->getMessage() . PHP_EOL;
}