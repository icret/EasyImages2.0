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

// 获取下载路径
if (isset($_GET['dw'])) {
    $dw = '../' . $_GET['dw'];
    // 检查文件是否存在
    if (!is_file($dw)) {
        exit('No File');
    }
}

// 历史上传记录的路径
if (isset($_GET['history'])) {
    $dw = '../' . $_GET['history'];
    if ($config['hide_path']) {
        $dw = '../' . $config['path'] . $_GET['history'];
    }
}
// 检查文件是否存在
if (!is_file($dw)) {
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
    header('Content-Disposition:attachment;filename=' . basename($dw));
    header('Content-Length:' . filesize($dw));
    //读取文件并写入到输出缓冲
    readfile($dw);
    exit;
} else {
    exit('Downfile Type Error');
}
