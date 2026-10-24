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

    if ($hidePath !== false && is_file(APP_ROOT . $hidePath)) {
        // 安全：仅回显存储目录内的图片类型，与 down.php 的白名单/黑名单口径一致
        $hide_ext = strtolower(pathinfo($hidePath, PATHINFO_EXTENSION));
        $filter_extensions = array_map('trim', explode(',', strtolower($config['extensions'])));
        $filter_other = array('php', 'json', 'log', 'lock');
        if (in_array($hide_ext, $filter_extensions, true) && !in_array($hide_ext, $filter_other, true)) {
            $real_path = APP_ROOT . $hidePath;
        } else {
            http_response_code(403);
            exit('No Premitted');
        }
    }
}

// 不存在或非法：回 404，不回显用户输入路径，仅给固定提示图
if ($real_path === null) {
    http_response_code(404);
    $real_path = APP_ROOT . '/public/images/404.png';
}

// 按真实扩展名给出正确的图片 MIME，不再拼接 text/html
$ex = strtolower(pathinfo($real_path, PATHINFO_EXTENSION));
$mime_map = array(
    'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jfif' => 'image/jpeg',
    'png' => 'image/png', 'gif' => 'image/gif', 'bmp' => 'image/bmp',
    'webp' => 'image/webp', 'ico' => 'image/x-icon', 'tif' => 'image/tiff',
    'tga' => 'image/x-tga', 'svg' => 'image/svg+xml',
);
$mime = isset($mime_map[$ex]) ? $mime_map[$ex] : 'application/octet-stream';

header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
// 安全：svg 可内嵌脚本，同源直接打开即执行 → 以 CSP sandbox 禁用脚本（不影响 <img> 渲染）
if ($ex === 'svg') {
    header('Content-Security-Policy: sandbox');
}
header('Content-Length: ' . filesize($real_path));

//输出文件
readfile($real_path);

exit;
