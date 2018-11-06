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
                $log_text .= is_string($value) ? ' ' . $value . ' ' : json_encode($value) . ' ';
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
        $pid = posix_getpid();
        $location = "[PID {$pid} {$file}=>{$real_traces['line']}]";
    }
    $log = $location . $log_text;
    $logger->log($log_type, $log);
    return $log;
}

function printLog($log)
{
    echo $log . PHP_EOL;
}

function debug()
{
    $traces = debug_backtrace();
    $real_traces = current($traces);
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);

    $messages = func_get_args();
    $pid = posix_getpid();
    $time = date('Y-m-d H:i:s', time());
    $location = "[{$time}][PID {$pid} {$file}=>{$real_traces['line']}]";
    $print = clog($messages, Phalcon\Logger::DEBUG, $location);
    if (php_sapi_name() == 'cli') {
        printLog($print);
    }
}

function info()
{
    $traces = debug_backtrace();
    $real_traces = current($traces);
    $file = str_replace(APP_ROOT . '/', '', $real_traces['file']);

    $messages = func_get_args();
    $pid = posix_getpid();
    $time = date('Y-m-d H:i:s', time());
    $location = "[{$time}][PID {$pid} {$file}=>{$real_traces['line']}]";
    $print = clog($messages, Phalcon\Logger::INFO, $location);
    if (php_sapi_name() == 'cli') {
        printLog($print);
    }
}

function isNull($var)
{
    $var = trim($var);
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

function daemon()
{
    $pid = pcntl_fork();
    if (-1 == $pid) {
        die("First Fork Error!");
    } elseif ($pid) {
        debug('daemon: 父进程(1):' . posix_getpid() . '退出!');
        exit(0);
    }
    posix_setsid();
    debug('daemon: 子进程(1):' . posix_getpid() . '脱离终端!');

    $pid = pcntl_fork();
    if (-1 == $pid) {
        die('Second Fork Error!');
    } elseif ($pid) {
        debug('daemon: 父进程(2):' . posix_getpid() . '退出!');
        exit(0);
    }
    debug('daemon: 守护进程', posix_getpid());
}

function pidDisappear($pid)
{
    $result = ($pid <= 1 || @pcntl_getpriority($pid) === false);
    return $result;
}

function timestamp()
{
    $micro_sec = floatval(microtime());
    $date = date('Y-m-d H:i:s');
    return [$date, $micro_sec];
}

function curlPost($url, $headers = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function curlGet($url, $headers = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (0 == strpos($url, "https://")) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function checkIdCard($id_card)
{
    $id_card = strtoupper($id_card);
    $reg = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    if (!preg_match($reg, $id_card)) {
        return false;
    }
    #15:/6-address/2-96/2-月/2-日/3-顺序号
    if (15 == strlen($id_card)) {
        $reg = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($reg, $id_card, $splits);
        $birth = '19' . $splits[2] . '/' . $splits[3] . '/' . $splits[4];
        if (!strtotime($birth)) {
            return false;
        } else {
            return true;
        }
    } elseif (18 == strlen($id_card)) {
        $reg = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($reg, $id_card, $splits);
        $birth = $splits[2] . '/' . $splits[3] . '/' . $splits[4];
        if (!strtotime($birth)) {
            return false;
        } else {
            #验证校验位
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int)$id_card[$i];
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $check_number = $arr_ch[$n];
            if ($check_number === substr($id_card, 17, 1)) {
                return true;
            }
        }
    }
    return false;
}


function decode($value)
{
    $timestamp = strtotime(date('Y/m/d'));
    $key = md5($timestamp);
    $iv = substr($key, 0, 16);

    debug('params: ', $key, $iv, $timestamp);
    return openssl_decrypt(base64_decode($value), 'aes-256-cbc', md5($key), OPENSSL_RAW_DATA, $iv);
}


function echoTip($str)
{
    echo "{$str}\n";
}

function sys($key, $default = null)
{
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    $value = getenv($key);
    if ($value) {
        return $value;
    }
    return $default;
}

function qr($text, $outfile, $infile = '', $level = 'M', $size = 4, $margin = 3)
{
    \PHPQRCode\QRcode::png($text, $outfile, $level, $size, $margin);
    $QR = $outfile;
    if (file_exists($infile)) {
        $QR = imagecreatefromstring(file_get_contents($QR));
        $logo = imagecreatefromstring(file_get_contents($infile));
        $QR_width = imagesx($QR);

        $logo_width = imagesx($logo);
        $logo_height = imagesy($logo);

        $logo_qr_width = $QR_width / 4;
        $scale = $logo_width / $logo_qr_width;
        $logo_qr_height = $logo_height / $scale;
        $from_width = ($QR_width - $logo_qr_width) / 2;

        imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);
        imagejpeg($QR, APP_ROOT . '/public/qrcode.jpeg');
        return APP_ROOT . '/public/qrcode.jpeg';
    }
    return $QR;
}