<?php

/**
 * Program: EasyImage2.0
 * Author: Icret
 * Date: 2022/3/13 20:11
 * For: 源图保护解密
 */

require_once __DIR__ . '/function.php';

$real_path = null;

if (isset($_GET['key'])) {
    $hide_original = $_GET['key'];

    // 解密并收敛路径，防止解密结果被构造成路径穿越
    $error = null;
    $hidePath = resolve_image_path(urlHash($hide_original, 1, 'hide'), $error);

    // 拒绝越界与穿越
    if (in_array($error, array('traversal', 'outside'), true)) {
        http_response_code(403);
        exit('No Premitted');
    }

    $real_path = $hidePath === false ? null : APP_ROOT . $hidePath;
}

// 文件不存在
if ($real_path === null || !is_file($real_path)) {
    $real_path = APP_ROOT . '/public/images/404.png';
}

// 获取文件后缀
$ex = pathinfo($real_path, PATHINFO_EXTENSION);

// 设置头
header("Content-Type: image/" . $ex . ";text/html; charset=utf-8");

//输出文件
echo file_get_contents($real_path);

exit;
