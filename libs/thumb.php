<?php
// +----------------------------------------------------------------------

// | 把大图缩略到缩略图指定的范围内，不留白（原图会剪切掉不符合比例的右边和下边）

// | https://www.php.cn/php-ask-458473.html
// +----------------------------------------------------------------------

require_once __DIR__ . '/function.php';
require_once __DIR__ . '/class.thumb.php';

$src = isset($_GET['img']) ? APP_ROOT . $_GET['img'] : APP_ROOT . '/public/images/404.png'; // 原图路径
$width = isset($_GET['width']) ? $_GET['width'] : 200; // 预生成缩略图的宽
$height = isset($_GET['height']) ? $_GET['height'] : 200; // 预生成缩略图的高
Thumb::show($src, $width, $height);
