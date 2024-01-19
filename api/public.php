<?php

/**
 * 图床公共信息查询APi
 * 2022年2月22日11:41:38
 * @author Icret
 */
require_once '../app/chart.php';

// 检查是否开启查询
if ($config['public'] === 0) die('开放数据接口已关闭!');

// 获得get值
$show =  (empty($_GET['show'])) ? die('没有参数!') : htmlspecialchars($_GET['show']);

// 检查是否在允许范围内
if (!in_array($show, $config['public_list'])) die('没有权限或参数错误!');

// 根据请求返回值
switch ($show) {
        // 统计时间
    case 'time':
        echo read_total_json('total_time');
        break;

        // 今日上传
    case 'today':
        echo read_total_json('todayUpload');
        break;

        // 昨日上传
    case 'yesterday':
        echo read_total_json('yestUpload');
        break;

        // 总空间
    case 'total_space':
        echo getDistUsed(disk_total_space('.'));
        break;

        // 已用空间
    case 'used_space':
        echo getDistUsed(disk_total_space('.') - disk_free_space('.'));
        break;

        // 剩余空间
    case 'free_space':
        echo getDistUsed(disk_free_space('/'));
        break;

        // 图床使用空间
    case 'image_used':
        echo read_total_json('usage_space');
        break;

        // 文件数量
    case 'file':
        echo read_total_json('filenum');
        break;

        // 文件夹数量
    case 'dir':
        echo read_total_json('dirnum');
        break;
    case 'month':
        foreach (read_chart_total()['number'] as $value)
            echo $value;
        break;

    default:
        return read_chart_total();
        break;
}
