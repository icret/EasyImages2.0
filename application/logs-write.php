<?php
require_once __DIR__ . '/function.php';
require_once __DIR__ . '/real_ip.php';
/**
 * 写日志
 * 日志格式：图片名称->源文件名称->上传时间（Asia/Shanghai）->IP地址->浏览器信息->文件相对路径->图片的MD5
 * $filePath 文件相对路径
 * $sourceName 源文件名称
 * $absolutePath 图片的绝对路径
 * $fileSize 图片的大小
 */
function write_log($filePath, $sourceName, $absolutePath, $fileSize, $from = "Web upload")
{
    global $config;
    // 压缩图片 后压缩模式，不影响前台输出速度
    if ($config['compress']) {
        if (!isAnimatedGif($absolutePath)) {
            require_once __DIR__ . '/compress/Imagick/class.Imgcompress.php';
            $img = new Imgcompress($absolutePath, 1);
            $img->compressImg($absolutePath);
            // 释放
            ob_flush();
            flush();
        }
    }

    // 图片违规检查
    if ($config['checkImg']) {
        require_once APP_ROOT . '/config/api_key.php';
        @checkImg($config['imgurl'] . $filePath);
        // 检查通过
        $checkImg = "Images Passed";
    } else {
        // 未开通
        $checkImg = "Check Closed";
    }

    $name = trim(basename($filePath), " \t\n\r\0\x0B"); // 当前图片名称
    $log = array($name => array(
        'source'     => $sourceName,                    // 原始文件名称
        'date'       => date('Y-m-d H:i:s'),            // 上传日期
        'ip'         => real_ip(),                      // 上传ip
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],    // 浏览器信息
        'path'       => $filePath,                      // 文件相对路径
        'size'       => getDistUsed($fileSize),         // 文件大小(格式化)
        'md5'        => md5_file($absolutePath),        // 文件的md5
        'checkImg'   => $checkImg,                      // 图像审查
        'from'       => $from,                          // 图片上传来源
    ));

    $logFileName = APP_ROOT . '/admin/logs/upload/' . date('Y-m') . '.php';

    // 创建日志文件夹
    if (!is_dir(APP_ROOT . '/admin/logs/upload/')) {
        mkdir(APP_ROOT . '/admin/logs/upload', 0755, true);
    }

    // 写入禁止浏览器直接访问
    if (filesize($logFileName) == 0) {
        $php_exit = '<?php /** {当前图片名称{source:源文件名称,date:上传日期(Asia/Shanghai),ip:上传者IP,user_agent:上传者浏览器信息,path:文件相对路径,size:文件大小(格式化),md5:文件MD5,checkImg:图像审查,form:图片上传来源}} */ exit;?>';
        file_put_contents($logFileName, $php_exit);
    }

    $log = json_encode($log, JSON_UNESCAPED_UNICODE);
    file_put_contents($logFileName, PHP_EOL . $log, FILE_APPEND | LOCK_EX);
}
/*
for ($i = 0; $i < 100000; $i++) {
    write_log('/i/2021/11/13/12der8s.jpg', '/i/cache/2021_11_13_12der8s.jpg');
}
*/