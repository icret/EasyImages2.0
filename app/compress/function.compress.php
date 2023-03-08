<?php

/**
 * 压缩文件函数调用位置
 */
require_once __DIR__ . '/../function.php';
require_once APP_ROOT . '/app/compress/Imagick/class.Imgcompress.php';
require_once APP_ROOT . '/app/compress/TinyImg/TinyImg.php';
require_once APP_ROOT . '/config/api_key.php';

/**
 * @param string $floder 文件夹
 * @param string 压缩方式Imgcompress / TinyPng
 */
function compress($floder, $type = 'Imgcompress', $source = '')
{
    global $config;
    ini_set('max_execution_time', '0');  // 脚本运行的时间（以秒为单位）0不限制

    if ($type == 'Imgcompress') {

        $pic = getFile($floder);    // 文件夹路径
        $percent = $config['compress_ratio'] / 100; // 压缩率
        foreach ($pic as $value) {
            $boxImg = $floder . $value;
            // 跳过动态图片
            if (!isGifAnimated($boxImg)) {
                $img = new Imgcompress($boxImg, $percent);
                $img->compressImg($boxImg);
                echo '<pre>' . $boxImg . '</pre><br />';
                // 释放
                ob_flush();
            }
        }
    }

    if ($type == 'TinyPng') {
        if (empty($config['TinyPng_key'])) {
            exit('请先申请TinyPng key并保存再试!');
        }
        $folder =  '..' . $config['path'] . $source;
        $tinyImg = new TinyImg();
        $key = $config['TinyPng_key'];
        $input = $folder; //这个文件夹下的文件会被压缩
        $output = $folder; //压缩的结果会被保存到这个文件夹中
        $tinyImg->compressImgsFolder($key, $input, $output);
    }
}

/* Test
$floder = 'D:/phpStudy/WWW/i/2021/05/09/';
compress($floder, 'TinyImg');
echo 666;
*/