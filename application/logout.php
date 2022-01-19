<?php
require_once 'header.php';

$config_user = $config['user'];

if (isset($_COOKIE[$config_user ]) and $_COOKIE[$config_user] == $config['password']) {
    setcookie("admin", null, time() - 1, '/');
    header("Refresh:2;url=../index.php");
    echo '
    <script>
        new $.zui.Messager("退出成功", {
			type: "success", // 定义颜色主题 
			icon: "ok-sign" // 定义消息图标
        }).show();
        // 延时2s跳转
        window.setTimeout("window.location=\'../index.php\'",2000);
    </script>
        ';
} else {
    echo '
        <script>
        new $.zui.Messager("尚未登录", {
			type: "danger", // 定义颜色主题 
			icon: "exclamation-sign" // 定义消息图标
        }).show();
        // 延时2s跳转
        window.setTimeout("window.location=\'./login.php\'",2000);
        </script>
        ';
}

require_once 'footer.php';
