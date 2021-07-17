<?php
require_once 'header.php';

if (isset($_COOKIE['admin']) and $_COOKIE['admin'] == md5($config['password'])) {
    setcookie("admin", null, time() - 1, '/');
    header("Refresh:2;url=../index.php");
    echo '
    <script>
        new $.zui.Messager("退出成功", {type: "success" // 定义颜色主题 
        }).show();
        // 延时2s跳转
        window.setTimeout("window.location=\'../index.php\'",2000);
    </script>
        ';
} else {
    echo '
        <script>
        new $.zui.Messager("尚未登录", {type: "danger" // 定义颜色主题 
        }).show();
        // 延时2s跳转
        window.setTimeout("window.location=\'./login.php\'",2000);
        </script>
        ';
}

require_once 'footer.php';
