<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/13
 * Time: 上午10:45
 */
use Phalcon\Di\FactoryDefault;
use Phalcon\Logger\Adapter\File as LoggerAdapterFile;
use Phalcon\Logger\Formatter\Line as LoggerFormatterLine;
use Phalcon\Config\Adapter\Ini;
use Phalcon\Mvc\view;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use Phalcon\Db\Adapter\Pdo\Postgresql as PostgreSQLPdo;


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


$di->set('db', function () {
    $config = $this->get('config');
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

$di->set('logger',function (string $file=null,array $line = null){
    $config = $this->get('config');
    $logger = $config->logger;
    $line = $logger->line;

    $loggerFormatterLine = new LoggerFormatterLine($line->format,$line->dateFormat);
    $file = $logger->file;
    if(!file_exists($file)){
        file_put_contents($file,'');
    }

    $loggerAdapterFile = new LoggerAdapterFile($file);
    $loggerAdapterFile->setFormatter($loggerFormatterLine);
    return $loggerAdapterFile;
});