<?php

/**
 * Program: EasyImage2.0
 * Author: Icret
 * Date: 2022/3/13 20:11
 * For: 源图保护解密
 */

require_once __DIR__ . '/function.php';

if (isset($_GET['key'])) {
    $hide_original = $_GET['key'];
    $real_path =  APP_ROOT . urlHash($hide_original, 1, crc32($config['hide_key']));
} else {
    $real_path = APP_ROOT . '/public/images/404.png';
}

// 文件不存在
if (!is_file($real_path)) {
    $real_path = APP_ROOT . '/public/images/404.png';
}

// 获取文件后缀
$ex = pathinfo($real_path, PATHINFO_EXTENSION);

// 设置头
header("Content-Type: image/" . $ex . ";text/html; charset=utf-8");

//输出文件
echo file_get_contents($real_path);

exit;
