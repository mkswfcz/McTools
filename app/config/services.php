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
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\Url as UrlProvider;
use Phalcon\Db\Adapter\Pdo\Mysql as MysqlPdo;
use Phalcon\Db\Adapter\Pdo\Postgresql as PostgreSQLPdo;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Events\Event;
use Phalcon\Mvc\Router;


$di = new FactoryDefault();


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

    if (!is_dir($dir)) {
        mkdir($dir);
    }
    $logfile = $dir . $file;
    if (!file_exists($logfile)) {
        file_put_contents($logfile, '');
    }

    $loggerAdapterFile = new LoggerAdapterFile($logfile);
    $loggerAdapterFile->setFormatter($loggerFormatterLine);
    return $loggerAdapterFile;
});

#循环调度控制转发volt,循环调度优先级
$di->set('dispatcher', function () {
    $eventsManager = new Manager();
    #bind 循环调度事件
    $eventsManager->attach(
        'dispatch:beforeDispatchLoop', function (Event $event, $dispatcher) {
        $clazz = $dispatcher->getHandlerClass();
        $class = explode('\\', $clazz);
        var_dump('clazz: ', $class);
        if (isset($class[1])) {

        }
//        if (!class_exists($clazz)) {
//            $dispatcher->forward(['controller' => 'Handler', 'action' => 'route404']);
//        }
    });

    $eventsManager->attach(
        'dispatch:beforeExecuteRoute', function (Event $event, $dispatcher) {
        $clazz = $dispatcher->getHandlerClass();
        $handler = $this->getShared($clazz);
        if (method_exists($handler, 'beforeAction')) {
            $handler->beforeAction($dispatcher);
            $handler->view->disable();
        }
    });
    $eventsManager->attach('dispatch:afterDispatchLoop', function (Event $event, $dispatcher) {
        $clazz = $dispatcher->getHandlerClass();
        $handler = $this->getShared($clazz);
        if (method_exists($handler, 'afterAction')) {
            $handler->afterAction($dispatcher);
        }
    });
    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});

$di->set('router', function () {
    $router = new Router();
    $uri = $router->getRewriteUri();
    list($namespace, $controller, $action) = parseUri($uri);
    debug('uri: ', $uri, $namespace, $controller, $action);
    $router->add(
        $uri,
        [
            'namespace' => $namespace,
            'controller' => $controller,
            'action' => $action,
        ]
    );
    return $router;
});

$di->set('view', function () {
    $view = new View();
    $view->setViewsDir(APP_ROOT . '/app/views/');
    $view->registerEngines([
        ".volt" => function ($view, $di) {
            $volt = new  Volt($view, $di);
            $volt->setOptions([
                'compiledPath' => function ($templatePath) {
                    $dirName = dirname($templatePath);
                    $file = str_replace($dirName . '/', '', $templatePath);
                    $path = APP_ROOT . '/app/cache/volt/';
                    if (!is_dir($path)) {
                        mkdir($path, 0777, true);
                    }
                    $compiled_file = $path . str_replace('/', '_', $dirName) . $file . '.php';
                    return $compiled_file;
                },
                'compileAlways' => false
            ]);
            return $volt;
        }
    ]);
    return $view;
});


#load config—files
$dirs = $di->get('config')->get('dirs');
foreach ($dirs as $key => $dir) {
    $files_stream = scandir($dir);
    foreach ($files_stream as $file) {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if ($file != 'services.php' && $file != 'defined.php' && $extension == 'php' && $extension != 'ini') {
            $source_file = $dir . $file;
            require "{$source_file}";
        }
    }
}

