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
    $view->setViewsDir(APP_ROOT. '/app/views');
    return $view;
});

$di->set('config', function () {
    $config = require APP_ROOT . "/app/config/config.php";
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

$di->set('logger', function (string $file = null, array $line = null) {
    $config = $this->get('config');
    $logger = $config->logger;
    $line = $logger->line;

    $loggerFormatterLine = new LoggerFormatterLine($line->format, $line->dateFormat);
    $dir = $logger->dir;
    $file = $logger->file;

    if(!is_dir($dir)){
        mkdir($dir);
    }
    $logfile =$dir.$file;
    var_dump($logfile);
    if (!file_exists($logfile)) {
//        file_put_contents($logfile, '');
    }

    $loggerAdapterFile = new LoggerAdapterFile($logfile);
    $loggerAdapterFile->setFormatter($loggerFormatterLine);
    return $loggerAdapterFile;
});

$dirs = $di->get('config')->get('dirs');
foreach ($dirs as $key => $dir) {
    $files_stream = scandir($dir);
    foreach ($files_stream as $file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($file != 'services.php' && $file!= 'defined.php' && $extension == 'php' && $extension != 'ini') {
            $source_file = $dir . $file;
            require "{$source_file}";
        }
    }
}
