<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午5:30
 */

use Phalcon\Loader;
use Phalcon\Mvc\Application;
use Phalcon\Di\FactoryDefault;

$define = realpath(__DIR__.'/../app/config/defined.php');
require "{$define}";

require APP_ROOT.'/app/config/services.php';
require APP_ROOT.'/vendor/autoload.php';
$loader = new Loader();
$loader->registerDirs(
    [
        APP_ROOT . '/app/controllers',
        APP_ROOT . '/app/models',
        APP_ROOT . '/app/views',
        APP_ROOT . '/app/tasks',
        APP_ROOT . '/app/libs'
    ]
)->register();

$application = new Application($di);
try {
    $response = $application->handle();
    $response->send();
} catch (Exception $e) {
    echo "[" . __FILE__ . ':' . __LINE__ . "]Exception: " . $e->getMessage() . PHP_EOL;
}

