<?php

/**
 * 必应每日图片代理（本地缓存）
 * 原实现直接 readfile() 远程 HTTP 地址且不校验响应，存在超时/内容不可控问题；
 * 现改为 curl + TLS 校验 + 图片内容校验，并统一缓存到图片存储目录的 cache 子目录。
 */
include_once __DIR__ . '/function.php';

$cacheDir = APP_ROOT . $config['path'] . 'cache/';
$cacheFile = $cacheDir . date('Ymd') . '.jpg';

header('Content-Type: image/jpeg');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=3600');

$output = function ($file = null) {
    if (is_string($file) && is_file($file)) {
        readfile($file);
        exit;
    }
    http_response_code(404);
    $fallback = APP_ROOT . '/public/images/404.png';
    if (is_file($fallback)) {
        header('Content-Type: image/png');
        readfile($fallback);
    }
    exit;
};

if (is_file($cacheFile)) {
    $output($cacheFile);
}

if (!is_dir($cacheDir)) {
    @mkdir($cacheDir, 0755, true);
}

/**
 * 拉取远程内容
 * @param string $url
 * @param int $timeout
 * @return string|false
 */
$fetch = function ($url, $timeout = 5) {
    if (!function_exists('curl_init')) {
        return false;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => $timeout,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT      => 'EasyImage/' . APP_VERSION,
    ));
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if (PHP_VERSION_ID < 80500) {
        curl_close($ch);
    }
    return ($body !== false && $code === 200) ? $body : false;
};

$api = $fetch('https://cn.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1');
$data = is_string($api) ? json_decode($api, true) : null;
$relative = is_array($data) && isset($data['images'][0]['url']) ? $data['images'][0]['url'] : '';

// 只接受站内相对路径，避免被响应内容带到任意主机
if (!is_string($relative) || strpos($relative, '/') !== 0 || strpos($relative, '//') === 0) {
    $output();
}

$image = $fetch('https://cn.bing.com' . $relative, 8);
if (!is_string($image) || strlen($image) < 100 || @getimagesizefromstring($image) === false) {
    $output();
}

if (@file_put_contents($cacheFile, $image, LOCK_EX) === false) {
    // 缓存目录不可写时仍直接输出本次结果
    echo $image;
    exit;
}

$output($cacheFile);
