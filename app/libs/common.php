<?php

function isNumericArray($array)
{
    $keys = array_keys($array);
    $length = count($array);
    $check = range(0, $length - 1);
    return $keys == $check;
}

function clog($content, $log_type, $location = '')
{
    $log_text = '';
    if (is_array($content)) {
        if (!isNumericArray($content)) {
            foreach ($content as $key => $value) {
                $log_text .= is_string($value) ? $key . '=>' . $value . ' ' : json_encode($key) . '=>' . json_encode($value) . '  ';
            }
        } else {
            foreach ($content as $value) {
                $log_text .= is_string($value) ? $value : json_encode($value) . ' ';
            }
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
    if (empty($location)) {
        $location = "[{$file}=>{$real_traces['line']}]";
    }
    $log = $location . $log_text;
    $logger->log($log_type, $log);
    return $log;
}

function debug()
{
    $traces = debug_backtrace();
    $real_traces = current($traces);
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);

    $messages = func_get_args();
    $print = clog($messages, Phalcon\Logger::DEBUG, "[{$file}=>{$real_traces['line']}]");
    echo $print . PHP_EOL;
}

function info()
{
    $traces = debug_backtrace();
    $real_traces = current($traces);
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);

    $messages = func_get_args();
    $print = clog($messages, Phalcon\Logger::INFO, "[{$file}=>{$real_traces['line']}]");
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
    if (!$response->hasErrors()) {
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

function isCli()
{
    return php_sapi_name() == 'cli';
}

function getConfig($key)
{
    $di = \Phalcon\Di::getDefault();
    $config = $di->get('config');
    return $config->$key;
}

function getAppName()
{
    $project_dir = realpath(__DIR__ . '/../../../');
    return str_replace([$project_dir, '/'], '', APP_ROOT);
}

function camelize($uncamelize_word, $separator = '_')
{
    $uncamelize_word = str_replace($separator, ' ', $uncamelize_word);
    $uncamelize_word = str_replace(' ', '', ucwords($uncamelize_word));
    return ltrim($uncamelize_word);
}

function uncamelize($camelize_word, $separator = '_')
{
    $camelize_word = lcfirst($camelize_word);
    return strtolower(preg_replace('/([a-z])([A-Z])/', '$1' . $separator . '$2', $camelize_word));
}