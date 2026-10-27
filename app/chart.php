<?php
require_once __DIR__ . '/function.php';
require_once __DIR__ . '/total_files.php';

/** 统计最近一个月上传文件数与空间占用 */

// 获取最近一周，一个月，一年 https://www.cnblogs.com/-mrl/p/7680700.html
function getLatelyTime($type = '')
{
    $now = time();
    $result = [];
    if ($type == 'week') {
        //最近一周
        for ($i = 0; $i < 7; $i++) {
            $result[] = date('Y/m/d/', strtotime('-' . $i . ' day', $now));
        }
    } elseif ($type == 'month') {
        //最近一个月
        for ($i = 0; $i < 30; $i++) {
            $result[] = date('Y/m/d/', strtotime('-' . $i . ' day', $now));
        }
    } elseif ($type == 'year') {
        //最近一年
        for ($i = 0; $i < 12; $i++) {
            $result[] = date('Y/m/', strtotime('-' . $i . ' month', $now));
        }
    }
    return $result;
}

$total_contents = APP_ROOT . $config['path'];                                // 获取用户自定义的上传目录
$chart_total_file_md5 = strval(md5_file(APP_ROOT . '/config/config.php'));  // 以config.php文件的md5命名
$chart_total_file = APP_ROOT . "/admin/logs/counts/chart-$chart_total_file_md5.php";       // 文件绝对目录

function write_chart_total()
{
    global $total_contents;
    global $chart_total_file;
    global $chart_total_file_md5;

    $count_day = getLatelyTime('month');

    $count_contents = array();
    $count_contents['filename'] = $chart_total_file_md5; // 文件名称
    $count_contents['total_time'] = date('Y-m-d H:i:s'); // 统计时间
    $count_contents['date'] = date('YmdH');              // 校对时间

    for ($i = 0; $i < count($count_day); $i++) {
        // 统计每日上传数量
        $count_contents['chart_data'][] = [$count_day[$i] => getFileNumber($total_contents . $count_day[$i])];
    }

    for ($i = 0; $i < count($count_day); $i++) {
        // 统计每日占用空间
        $count_contents['chart_disk'][] = [$count_day[$i] => getDirectorySize($total_contents . $count_day[$i])];
    }

    if (!is_dir(APP_ROOT . '/admin/logs/counts/')) {
        mkdir(APP_ROOT . '/admin/logs/counts/', 0755, true);
    }

    $count_contents = json_encode($count_contents, JSON_UNESCAPED_UNICODE);
    if ($count_contents !== false) {
        file_put_contents($chart_total_file, $count_contents, LOCK_EX);  // 存储文件
    }

}

function read_chart_total()
{
    global $chart_total_file;
    global $config;

    $cache_freq = max(1, (int) ($config['cache_freq'] ?? 1));
    $read = function () use ($chart_total_file) {
        $raw = is_file($chart_total_file) ? @file_get_contents($chart_total_file) : false;
        $data = is_string($raw) ? json_decode($raw, true) : null;
        return is_array($data) ? $data : null;
    };

    $data = $read();
    // 用统计时间计算真实缓存时长，避免跨月时 "YmdH" 相减得到错误差值
    $stale = true;
    if (is_array($data) && !empty($data['total_time'])) {
        $timestamp = strtotime((string) $data['total_time']);
        $stale = ($timestamp === false) || (time() - $timestamp > $cache_freq * 3600);
    }
    if ($stale) {
        write_chart_total();
        $data = $read();
    }

    // 缓存损坏/为空时返回空结构，调用方无需再做 isset 判断
    if (!is_array($data) || empty($data['chart_data']) || !is_array($data['chart_data'])
        || empty($data['chart_disk']) || !is_array($data['chart_disk'])) {
        return array('filename' => '', 'date' => array(), 'number' => array(), 'disk' => array(), 'total_time' => '');
    }

    $chart_data_date = $chart_data_num = $chart_total_disk = array();
    foreach ($data['chart_data'] as $day) {
        // 读取每日上传数量
        if (!is_array($day)) {
            continue;
        }
        foreach ($day as $key => $value) {
            $chart_data_date[] = '"' . $key . '" ,';
            $chart_data_num[] = '"' . (int) $value . '" ,';
        }
    }
    foreach ($data['chart_disk'] as $day) {
        if (!is_array($day)) {
            continue;
        }
        foreach ($day as $value) {
            $value = round((float) $value / 1024 / 1024, 2);
            $chart_total_disk[] = '"' . $value . '" ,';
        }
    }

    return array(
        'filename'   => $data['filename'] ?? '',
        'date'       => $chart_data_date,
        'number'     => $chart_data_num,
        'disk'       => $chart_total_disk,
        'total_time' => $data['total_time'] ?? '',
    );
}
