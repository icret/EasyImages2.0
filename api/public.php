<?php

/**
 * 图床公共信息查询API
 * 2024年04月07日 08:00:00
 * @author Icret
 */

// 定义常量以替换魔术字符串
const TIME_KEY = 'total_time';
const TODAY_UPLOAD_KEY = 'todayUpload';
const YESTERDAY_UPLOAD_KEY = 'yestUpload';
const USAGE_SPACE_KEY = 'usage_space';
const FILENUM_KEY = 'filenum';
const DIRNUM_KEY = 'dirnum';

require_once '../app/chart.php';

// 检查是否开启查询
if ($config['public'] === 0) {
    http_response_code(403); // 返回403 Forbidden
    die('开放数据接口已关闭!');
}

// 获取并验证GET参数
$show = isset($_GET['show']) ? trim($_GET['show']) : '';
if (!$show || !in_array($show, $config['public_list'])) {
    http_response_code(400); // 返回400 Bad Request
    die('没有权限或参数错误!');
}

try {
    // 根据请求返回值
    switch ($show) {
        // 统计时间
        case 'time':
            echo read_total_json(TIME_KEY);
            break;

        // 今日上传
        case 'today':
            echo read_total_json(TODAY_UPLOAD_KEY);
            break;

        // 昨日上传
        case 'yesterday':
            echo read_total_json(YESTERDAY_UPLOAD_KEY);
            break;

        // 总空间
        case 'total_space':
            echo getDistUsed(disk_total_space('.'));
            break;

        // 已用空间
        case 'used_space':
            $totalSpace = disk_total_space('.');
            if ($totalSpace !== false && is_numeric($totalSpace)) {
                $freeSpace = disk_free_space('.');
                if ($freeSpace !== false && is_numeric($freeSpace)) {
                    echo getDistUsed($totalSpace - $freeSpace);
                } else {
                    throw new Exception('无法获取磁盘剩余空间');
                }
            } else {
                throw new Exception('无法获取磁盘总空间');
            }
            break;

        // 剩余空间
        case 'free_space':
            $freeSpace = disk_free_space('/');
            if ($freeSpace !== false && is_numeric($freeSpace)) {
                echo getDistUsed($freeSpace);
            } else {
                throw new Exception('无法获取磁盘剩余空间');
            }
            break;

        // 图床使用空间
        case 'image_used':
            echo read_total_json(USAGE_SPACE_KEY);
            break;

        // 文件数量
        case 'file':
            echo read_total_json(FILENUM_KEY);
            break;

        // 文件夹数量
        case 'dir':
            echo read_total_json(DIRNUM_KEY);
            break;
        
        // 修复"month"分支的逻辑
        case 'month':
            $chartTotal = read_chart_total();
            if (isset($chartTotal['number']) && is_array($chartTotal['number'])) {
                foreach ($chartTotal['number'] as $value) {
                    echo $value;
                }
            } else {
                throw new Exception('无法获取图表总数中的“number”数据');
            }
            break;

        default:
            echo read_chart_total();
            break;
    }
} catch (Exception $e) {
    http_response_code(500); // 返回500 Internal Server Error
    die("发生错误: " . $e->getMessage());
}