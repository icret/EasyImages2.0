<?php

/**
 * 下载文件
 * https://www.php.cn/php-weizijiaocheng-394566.html
 */
//获取要下载的文件名

if (empty($_GET['dw'])) {
    exit('No File');
}

$dw = '../' . $_GET['dw'];

//设置头信息
header('Content-Disposition:attachment;filename=' . basename($dw));
header('Content-Length:' . filesize($dw));

//读取文件并写入到输出缓冲
readfile($dw);
exit;
