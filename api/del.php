<?php

/**
 * 删除文件页面
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/header.php';
require_once '../libs/function.php';
if (empty($_REQUEST)) {
    echo '
    <script>
    new $.zui.Messager("没有要删除的图片！", {type: "danger" // 定义颜色主题 
    }).show();
    // 延时3.5s跳转			
    window.setTimeout("window.location=\'/../ \'",3500);
    </script>
    ';
} elseif (isset($_GET['url'])) {
    //$img = isset($_GET['hash'])?:$_GET['url'];
    $img = $_GET['url'];
    //echo '<img data-toggle="lightbox" data-image="' . $img  . '" src="' . $img  . '" alt="简单图床-EasyImage" class="img-thumbnail">';
    echo '<a href="' . $img . '" target="_blank"><img src="' . $img  . '" alt="简单图床-EasyImage" class="img-thumbnail"></a>';
}

// 解密删除
if (isset($_GET['hash'])) {
    $delFile = $_GET['hash'];
    $delFile = urlHash($delFile, 1);
    getDel($delFile);
}

// 检查登录后再处理url删除请求
if (is_online()) {
    if (isset($_GET['url'])) {
        getDel($_GET['url']);
    }
} else {
    if (isset($_GET['url'])) {
        echo '
			<script>
            new $.zui.Messager("请登录后再删除", {type: "danger" // 定义颜色主题 
            }).show();
            // 延时2s跳转			
        window.setTimeout("window.location=\'/../libs/login.php \'",2000);
            </script>
			';
    }
}

require_once '../libs/footer.php';
