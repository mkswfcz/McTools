<?php

function clog($content, $log_type)
{
    $log_text = '';
    if (is_array($content)) {
        foreach ($content as $value) {
            $log_text .= json_encode($value) . '  ';
        }
    } elseif (is_object($content)) {
        $log_text = json_encode($content);
    }else{
        $log_text = $content;
    }
    $traces = debug_backtrace();
    $real_traces = current($traces);

    $logger = Phalcon\Di::getDefault()->get('logger');
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);
    $log = "[{$file}=>{$real_traces['line']}]" . $log_text;
    $logger->log($log_type, $log);
    return $log_text;
}

function debug()
{
    $messages = func_get_args();
    $print = clog($messages, Phalcon\Logger::DEBUG);
    echo $print . PHP_EOL;
}

function info()
{
    $messages = func_get_args();
    $print = clog($messages, Phalcon\Logger::INFO);
    echo $print . PHP_EOL;
}

