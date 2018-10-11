<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/11
 * Time: 下午3:56
 */

use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Cli\Console;

define('VERSION', '1.0.0.0');
$di = new Cli();

$loader = new \Phalcon\Loader();
$loader->registerDirs([
    __DIR__ . '/app/tasks'
])->register();

$config_file = __DIR__ . '/app/config/config.php';
if (file_exists($config_file)) {
    $config = require __DIR__ . '/app/config/config.php';
    $di->set('config', $config);
}

$console = new Phalcon\Cli\Console();
$console->setDi($di);
//$di->setShared('console', $console);

$arguments = array();
foreach ($argv as $k => $arg) {
    if (1 == $k) {
        $arguments['task'] = $arg;
    } elseif (2 == $k) {
        $arguments['action'] = $arg;
    } elseif ($k >= 3) {
        $arguments['params'][] = $arg;
    }
}

define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

try {
    $console->handle($arguments);
} catch (\Phalcon\Exception $e) {
    echo "[" . __FILE__ . ':' . __LINE__ . "]Exception: " . $e->getMessage() . PHP_EOL;
    exit(255);
}