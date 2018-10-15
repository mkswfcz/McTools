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
    } else {
        $log_text = $content;
    }
    $traces = debug_backtrace();
    $real_traces = current($traces);

    $logger = Phalcon\Di::getDefault()->get('logger');
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);
    $log = "[{$file}=>{$real_traces['line']}]" . $log_text;
    $logger->log($log_type, $log);
    return $log;
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

function isNull($var)
{
    if (empty($var) || is_null($var)) {
        return true;
    }
    return false;
}

function getKey($value, $array = array(), $default = null)
{
    $key = array_search($value, $array);
    if (!$key) {
        return $default;
    }
    return $key;
}

function getValue($key, $array = array(), $default = null)
{
    if (isset($array[$key])) {
        return $array[$key];
    }
    return $default;
}

function myDate($time = '', $format = 'Ymd')
{
    if ($format == 'Ymd') {
        if (isNull($time)) {
            $time = time();
        }
        return date('Y-m-d H:i:s ', $time);
    }
    if ($format == 'digital') {
        return strtotime($time);
    }
}

function post($uri, $headers = array(), $params = array())
{
    $response = \Httpful\Request::post($uri, $params, \Httpful\Mime::FORM)
        ->addHeaders($headers)
        ->autoParse()
        ->send();
    $body = '';
    if(!$response->hasErrors()){
        $body = $response->raw_body;
    }
    return [
        'header' => $response->headers,
        'body' => $body,
        'http_code' => $response->code,
        'is_error' => $response->hasErrors()
    ];
}

function get($uri, $params = array(), $headers = array())
{
    if (isNull($params)) {
        $headers['Content-Type'] = [\Httpful\Mime::FORM];
    }
    $response = \Httpful\Request::get($uri)
        ->timeoutIn(10)
        ->autoParse()
        ->send();
    $header = $response->headers;
    $body = $response->raw_body;
    return [
        'headers' => $header,
        'body' => $body,
        'http_code' => $response->code,
        'is_error' => $response->hasErrors()
    ];
}