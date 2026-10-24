<?php

/**
 * 下载文件
 * https://www.php.cn/php-weizijiaocheng-394566.html
 */
//获取要下载的文件名
require_once __DIR__ . '/function.php';

// 空GET
if (empty($_GET)) {
    exit('No file path');
}

// 获取下载路径（收敛到存储目录，防止路径穿越）
$error = null;
$dw = false;
if (isset($_GET['dw'])) {
    $dw = resolve_image_path($_GET['dw'], $error);
} elseif (isset($_GET['history'])) {
    // 历史上传记录的路径
    $dw = resolve_image_path($_GET['history'], $error);
}

// 拒绝越界与穿越
if (in_array($error, array('traversal', 'outside'), true)) {
    http_response_code(403);
    exit('No Premitted');
}

// 检查文件是否存在
if ($dw === false || !is_file(APP_ROOT . $dw)) {
    http_response_code(404);
    exit('No File');
}

// 过滤下载非指定上传文件格式
$dw_extension = pathinfo($dw, PATHINFO_EXTENSION);
$filter_extensions = explode(',', $config['extensions']);

// 过滤下载其他格式
$filter_other = array('php', 'json', 'log', 'lock');

// 先过滤后下载
if (in_array($dw_extension, $filter_extensions) && !in_array($dw_extension, $filter_other)) {
    //设置头信息
    // 文件名加引号并剥除可能破坏头的引号/换行（纵深防御）
    $dw_name = str_replace(array('"', "\r", "\n"), '', basename($dw));
    header('Content-Disposition:attachment;filename="' . $dw_name . '"');
    header('Content-Length:' . filesize(APP_ROOT . $dw));
    //读取文件并写入到输出缓冲
    readfile(APP_ROOT . $dw);
    exit;
} else {
    exit('Downfile Type Error');
}
