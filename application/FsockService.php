<?php

/**
 * 异步 执行程序
 * param string $path 异步url 地址
 * param array $postData 传递的参数
 * param string $method 请求方式
 * param string $url 请求地址
 * return bool
 */
function request_asynchronous($path, $method = "POST", $postData = array(), $url = '')
{
    if (empty($path)) {
        return false;
    }

    if ($url) {
        $matches = parse_url($url);
        $host = $matches['host'];
        //$path = $matches['path'] ? $matches['path'] . ($matches['query'] ? '?' . $matches['query'] : '') : '/';
        if ($matches['scheme'] == 'https') { //判断是否使用HTTPS
            $transports = 'ssl://';  //如使用HTTPS则使用SSL协议
            $port = !empty($matches['port']) ? $matches['port'] : 443; //如使用HTTPS端口使用443
        } else {
            $transports = 'tcp://'; //如没有使用HTTPS则使用tcp协议
            $port = !empty($matches['port']) ? $matches['port'] : 80; //如没有使用HTTPS则使用80端口
        }
    } else {
        $port = 443;
        $transports = 'ssl://';
        $host = $_SERVER['HTTP_HOST'];
    }

    $errNo = 0;
    $errStr = '';
    $timeout = 60;
    $fp = '';
    if (function_exists('fsockopen')) {
        $fp = fsockopen(($transports . $host), $port, $errno, $errStr, $timeout);
    } elseif (function_exists('pfsockopen')) {
        $fp = pfsockopen($transports . $host, $port, $errNo, $errStr, $timeout);
    } elseif (function_exists('stream_socket_client')) {
        $fp = stream_socket_client($transports . $host . ':' . $port, $errNo, $errStr, $timeout);
    }

    if (!$fp) {
        return false;
    }

    stream_set_blocking($fp, 0); //开启非阻塞模式
    stream_set_timeout($fp,  3); //设置超时时间（s）

    $date = [];
    if ($postData) {
        //处理文件
        foreach ($postData as $key => $value) {
            if (is_array($value)) {
                $date[$key] = serialize($value);
            } else {
                $date[$key] = $value;
            }
        }
    }

    if ($method == "GET") {
        $query = $date ? http_build_query($date) : '';
        $path .= "?" . $query;
    } else {
        $query = json_encode($date);
    }

    //http消息头
    $out = $method . " " . $path . " HTTP/1.1\r\n";
    $out .= "HOST: " . $host . "\r\n";
    if ($method == "POST") {
        $out .= "Content-Length:" . strlen($query) . "\r\n";
    }
    $out .= "Accept: application/json, text/plain, */*\r\n";
    $out .= "Access-Control-Allow-Credentials: true\r\n";
    $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out .= "Connection: Close\r\n\r\n";
    if ($method == "POST") {
        $out .= $query;
    }

    fputs($fp, $out);
    usleep(20000);
    //忽略执行结果
    /*while (!feof($fp)) {
        echo fgets($fp, 128);
    }*/
    fclose($fp);

    return true;
}
/*
$p = array(
    'test'=>1222,
    'test1'=>2222,
    );
var_dump(request_asynchronous('/test.php', 'GET', $p, 'https://png.cm'));
*/