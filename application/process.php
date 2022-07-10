<?php

require_once __DIR__ . '/function.php';
require_once __DIR__ . '/WaterMask.php';

// 压缩图片与图片鉴黄
function compress($absolutePath)
{
    global $config;
    // 压缩图片 后压缩模式，不影响前台输出速度
    if ($config['compress']) {
        if (!isAnimatedGif($absolutePath)) {
            require_once __DIR__ . '/compress/Imagick/class.Imgcompress.php';
            $percent = $config['compress_ratio'] / 100; // 压缩率
            $img = new Imgcompress($absolutePath, $percent);
            $img->compressImg($absolutePath);
            // 释放
            ob_flush();
            flush();
        }
    }
}

// 设置水印
function water($source)
{
    global $config;

    // 文字水印
    if ($config['watermark'] == 1) {
        // 过滤gif
        if (!isAnimatedGifWebp($source)) {
            $arr = [
                #  水印图片路径（如果不存在将会被当成是字符串水印）
                'res' => $config['waterText'],
                #  水印显示位置
                'pos' => $config['waterPosition'],
                #  不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                'name' => $source,
                'font' => APP_ROOT . $config['textFont'],
                'fontSize' => $config['textSize'],
                'color' => str_replace(array('rgba', '(', ')'), '', $config['textColor']),
            ];
            Imgs::setWater($source, $arr);
        }
    }

    // 图片水印
    if ($config['watermark'] == 2) {
        // 过滤gif
        if (!isAnimatedGifWebp($source)) {
            $arr = [
                #  水印图片路径（如果不存在将会被当成是字符串水印）
                'res' => APP_ROOT . $config['waterImg'],
                #  水印显示位置
                'pos' => $config['waterPosition'],
                #  不指定name(会覆盖原图，也就是保存成thumb.jpeg)
                'name' => $source,
            ];
            Imgs::setWater($source, $arr);
        }
    }
}

function process_checkImg($imgurl)
{
    global $config;
    // 图片违规检查
    if ($config['checkImg'] == 1) {
        checkImg($imgurl, 1);
    }

    if ($config['checkImg'] == 2) {
        checkImg($imgurl, 2);
    }
}

/**
 * 写日志
 * 
 * 格式：
 * {
 *  上传图片名称{
 *      source:源文件名称,
 *      date:上传日期(Asia/Shanghai),
 *      ip:上传者IP,port:IP端口,
 *      user_agent:上传者浏览器信息,
 *      path:文件相对路径,
 *      size:文件大小(格式化),
 *      md5:文件MD5,
 *      checkImg:鉴黄状态,
 *      form:上传方式web/API ID
 *  }
 * }
 * 
 * $filePath 文件相对路径
 * $sourceName 源文件名称
 * $absolutePath 图片的绝对路径
 * $fileSize 图片的大小
 * $form 来源如果是网页上传直接显示网页,如果是API上传则显示ID
 */
function write_log($filePath, $sourceName, $absolutePath, $fileSize, $from = "web")
{
    global $config;

    $checkImg = $config['checkImg'] == true ? "Enabled" : "Disabled";

    // $name = trim(basename($filePath), " \t\n\r\0\x0B"); // 当前图片名称
    $log = array(basename($filePath) => array(             // 以上传图片名称为Array
        'source'     => $sourceName,                       // 原始文件名称
        'date'       => date('Y-m-d H:i:s'),               // 上传日期
        'ip'         => real_ip(),                         // 上传IP
        'port'       => $_SERVER['REMOTE_PORT'],           // IP端口
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],       // 浏览器信息
        'path'       => $filePath,                         // 文件相对路径
        'size'       => getDistUsed($fileSize),            // 文件大小(格式化)
        'md5'        => md5_file($absolutePath),           // 文件的md5
        'checkImg'   => $checkImg,                         // 鉴黄状态
        'from'       => $from,                             // 图片上传来源
    ));

    // 创建日志文件夹
    if (!is_dir(APP_ROOT . '/admin/logs/upload/')) {
        mkdir(APP_ROOT . '/admin/logs/upload', 0755, true);
    }

    // logs文件组成
    $logFileName = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';

    // 创建logs文件
    if (!is_file($logFileName)) {
        file_put_contents($logFileName, '<?php $logs=Array();?>');
    }

    // 引入logs
    include $logFileName;

    // // 写入禁止浏览器直接访问
    // if (filesize($logFileName) == 0) {
    //     $php_exit = '<?php /** {图片名称{source:源文件名称,date:上传日期(Asia/Shanghai),ip:上传者IP,port:IP端口,user_agent:上传者浏览器信息,path:文件相对路径,size:文件大小(格式化),md5:文件MD5,checkImg:鉴黄状态,form:上传方式web/API ID}} */ exit;? >';
    //     file_put_contents($logFileName, $php_exit);
    // }

    // $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    // file_put_contents($logFileName, PHP_EOL . $log, FILE_APPEND | LOCK_EX);

    $log = array_replace($logs, $log);
    cache_write($logFileName, $log, 'logs');
}
