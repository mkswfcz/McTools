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

define('ROOT_DIR', realpath(__DIR__ . '/../'));
require ROOT_DIR."/app/config/acl.php";
require ROOT_DIR.'/app/config/services.php';

$loader = new Loader();
$loader->registerDirs(
    [
        ROOT_DIR . '/app/controllers',
        ROOT_DIR . '/app/models',
        ROOT_DIR . '/app/views',
        ROOT_DIR.'app/tasks'
    ]
)->register();


$application = new Application($di);
try {
    $response = $application->handle();
    $response->send();
} catch (Exception $e) {
    echo "[" . __FILE__ . ':' . __LINE__ . "]Exception: " . $e->getMessage() . PHP_EOL;
}

