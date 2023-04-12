<?php

/** 禁止直接访问 */
defined('APP_ROOT') ?: exit;

/**
 * 统计文件
 *
 * 递归函数实现遍历指定文件下的目录与文件数量
 * $dirname=指定目录，$dirnum=目录总数，$filen=文件总数
 */

require_once __DIR__ . '/../config/config.php';

$dirn = 0; //目录数
$filen = 0; //文件数

//用来统计一个目录下的文件和目录的个数
function total_files($file)
{
    global $dirn;
    global $filen;
    $dir = opendir($file);
    while ($filename = readdir($dir)) {
        if ($filename != "." && $filename != "..") {
            $filename = $file . "/" . $filename;
            if (is_dir($filename)) {
                $dirn++;
                total_files($filename);  //递归，就可以查看所有子目录
            } else {
                $filen++;
            }
        }
    }
    closedir($dir);
}

$total_file_path = APP_ROOT . $config['path']; // 获取用户自定义的上传目录
$totalJsonMD5 =  strval(md5_file(APP_ROOT . '/config/config.php')); // 以config.php文件的md5命名
$totalJsonName = APP_ROOT  . "/admin/logs/counts/total-files-$totalJsonMD5.php";       // 文件绝对目录

function creat_json() // 创建json文件
{
    global $dirn;
    global $filen;
    global $totalJsonName;
    global $config;
    global $total_file_path;
    global $totalJsonMD5;

    total_files($total_file_path);
    $usage_space = getDistUsed(getDirectorySize(APP_ROOT . $config['path']));
    $todayUpload = getFileNumber(APP_ROOT . config_path()); // 今日上传数量
    $yestUpload = getFileNumber(APP_ROOT . $config['path'] . date("Y/m/d/", strtotime("-1 day"))); // 昨日上传数量

    $totalJsonInfo = [
        'filename'    => $totalJsonMD5,                      // 统计文件名称
        'date'        => date('YmdH'),                       // 识别日期格式
        'total_time'  => date('Y-m-d H:i:s'),                // 统计时间
        'dirnum'      => $dirn,                              // 文件夹数量
        'filenum'     => $filen,                             // 文件数量
        'usage_space' => $usage_space,                       // 占用空间
        'todayUpload' => $todayUpload,                       // 今日上传数量
        'yestUpload'  => $yestUpload                         // 昨日上传数量
    ];
    $totalJsonInfo = json_encode($totalJsonInfo, true);
    if (is_dir(APP_ROOT . '/admin/logs/counts/')) {
        file_put_contents($totalJsonName, $totalJsonInfo);
    } else {
        mkdir(APP_ROOT . '/admin/logs/counts/', 0777, true);  // 创建cache目录
        file_put_contents($totalJsonName, $totalJsonInfo);
    }
}
function read_total_json($total) // 读取json文件
{
    global $totalJsonFile;
    global $totalJsonName;
    global $config;
    $cache_freq = $config['cache_freq'];

    if (file_exists($totalJsonName)) {
        $totalJsonFile = file_get_contents($totalJsonName);
        $totalJsonFile = json_decode($totalJsonFile, true);
    } else {
        creat_json();
        $totalJsonFile = file_get_contents($totalJsonName);
        $totalJsonFile = json_decode($totalJsonFile, true);
    }

    if ((date('YmdH') - $totalJsonFile['date']) > $cache_freq) {
        creat_json();
        $totalJsonFile = file_get_contents($totalJsonName);
        $totalJsonFile = json_decode($totalJsonFile, true);
    }

    return $totalJsonFile[$total];
}
