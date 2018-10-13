<?php

function clog($content)
{
    if (!is_string($content)) {
        $content = json_encode($content);
    }
    $traces = debug_backtrace();
    $real_traces = current($traces);

    $logger = Phalcon\Di::getDefault()->get('logger');
    $file = str_replace(APP_ROOT.'/', '', $real_traces['file']);
    $log_text = "[{$file}=>{$real_traces['line']}]" . $content;
    $logger->info($log_text);
    return $log_text;
}

function debug($messages)
{
    $print = clog($messages);
    echo $print.PHP_EOL;
}

