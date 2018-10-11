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
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use Phalcon\Db\Adapter\Pdo\Postgresql as PostgreSQLPdo;
use Phalcon\Config\Adapter\Ini;

define('ROOT_DIR', realpath(__DIR__ . '/../'));
require ROOT_DIR."/app/config/acl.php";
$loader = new Loader();
$loader->registerDirs(
    [
        ROOT_DIR . '/app/controllers',
        ROOT_DIR . '/app/models',
        ROOT_DIR . '/app/views',
    ]
)->register();

$di = new FactoryDefault();
$di->set('view', function () {
    $view = new View();
    $view->setViewsDir(ROOT_DIR . '/app/views');
    return $view;
});

$di->set('config', function () {
    $config = require ROOT_DIR."/app/config/config.php";
    return $config;
});

$ini_file = ROOT_DIR . '/app/config/config.ini';
if (file_exists($ini_file)) {
    $config = new Ini($ini_file);
}

$di->set('db', function () use ($config) {
    $database = $config->database;
    $conn = [
        'host' => $database->host,
        'username' => $database->username,
        'password' => $database->password,
        'dbname' => $database->dbname
    ];
    $adapter = $database->adapter;
    switch ($adapter) {
        case 'Mysql':
            return new MysqlPdo($conn);
            break;
        case 'PostgresSQL':
            $conn['port'] = $database->port;
            return new PostgreSQLPdo($conn);
            break;
    }
});


$application = new Application($di);
try {
    $response = $application->handle();
    $response->send();
} catch (Exception $e) {
    echo "[" . __FILE__ . ':' . __LINE__ . "]Exception: " . $e->getMessage() . PHP_EOL;
}

