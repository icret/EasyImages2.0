<?php
require_once __DIR__ . '/function.php';

/**
 * 统计
 */

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

$chart_total_fileName = 'total_chart_' . md5_file(APP_ROOT . '/config/config.php');  // 以config.php文件的md5命名
$total_contents = APP_ROOT . $config['path'];                                       // 获取用户自定义的上传目录
$chart_total_file = $total_contents . 'cache/total_chart_' . $chart_total_fileName . '.php';    // 文件绝对目录

function write_chart_total()
{
    global $total_contents;
    global $chart_total_file;

    $count_day = getLatelyTime('month');

    for ($i = 0; $i < count($count_day); $i++) {
        // 统计每日上传数量
        $count_contents['chart_data'][] = [$count_day[$i] => getFileNumber($total_contents . $count_day[$i])];
    }

    for ($i = 0; $i < count($count_day); $i++) {
        // 统计每日占用空间
        $count_contents['chart_disk'][] = [$count_day[$i] => getDirectorySize($total_contents . $count_day[$i])];
    }

    $count_contents['total_time'] = date('Y-m-d H:i:s'); // 统计时间
    $count_contents['date'] = date('Ymd');              // 校对时间

    $count_contents = json_encode($count_contents, true); // serialize存储文件
    file_put_contents($chart_total_file, $count_contents);  // 存储文件
}

function read_chart_total()
{
    global $chart_total_file;

    if (is_file($chart_total_file)) {

        $chart_total_file = file_get_contents($chart_total_file);
        $chart_total_file = json_decode($chart_total_file, true);


        if ($chart_total_file['date'] !== date('Ymd')) {
            write_chart_total();
        } else {
            for ($i = 0; $i < count($chart_total_file['chart_data']); $i++) {
                // 读取每日上传数量
                foreach ($chart_total_file['chart_data'][$i] as $key => $value) {
                    $chart_data_date[] = '"' . $key . '" ,';
                    $chart_data_num[] = '"' . $value . '" ,';
                    //echo $key . '<br/>';
                    //echo $value . '<br/>';
                }
                foreach ($chart_total_file['chart_disk'][$i] as $value) {
                    $value = round($value / 1024 / 1024, 2);

                    $chart_total_disk[] = '"' . $value . '" ,';
                }
            }

            return array('date' => $chart_data_date, 'number' => $chart_data_num, 'disk' => $chart_total_disk,'total_time'=>$chart_total_file['total_time']);
        }
    } else {
        write_chart_total();
    }


    /*
    switch ($name) {
        case 'date':
            return $chart_data_date;
            break;
        case 'number':
            return $chart_data_num;
        case 'disk':
            return $chart_total_disk;
            break;
        default:
            return $chart_data_date;
    }
    */
}
/*

$char_data = read_chart_total();
$chart_date =  $char_data['date'];
$chart_number = $char_data['number'];
$chart_disk =  $char_data['disk'];
var_dump($char_data['disk']);
var_dump($char_data['number']);

*/