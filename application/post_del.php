<?php

/**
 * 删除文件页面
 */
require_once __DIR__ . '/function.php';

if (!is_online()) {
    exit('Not Logged!');
}

$del_url_array = isset($_POST['del_url_array']) ? $_POST['del_url_array'] : exit;
$del_num = count($del_url_array);
for ($i = 0; $i < $del_num; $i++) {
    getDel($del_url_array[$i], 'url');
}

$path = '/i/cache/';

if (deldir($path)) {
    echo '
    <script> new $.zui.Messager("删除成功！", {type: "success" // 定义颜色主题 
    }).show();</script>';
    header("refresh:1;"); // 1s后刷新当前页面
} else {
    echo '
    <script> new $.zui.Messager("删除失败！", {type: "danger" // 定义颜色主题 
    }).show();</script>';
    header("refresh:1;"); // 1s后刷新当前页面
}