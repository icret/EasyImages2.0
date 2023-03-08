<?php

/**
 * 压缩状态页面
 */
require_once 'header.php';
require_once APP_ROOT . '/app/compress/function.compress.php';

// 检测登录
if (!is_who_login('admin')) {
    checkLogin();
    echo '<div class="alert alert-info">未登陆~~</div>';
    exit(require_once APP_ROOT . '/app/footer.php');
}
// 文件夹压缩
if (isset($_POST['folder'])) {

    $getFolder = urldecode($_POST['folder']);

    $source = $_POST['folder'];

    $type = $_POST['type'];

    $folder =  '..' . $config['path'] . $getFolder;

    if (!is_dir($folder)) {
        exit($folder . '<script> new $.zui.Messager("没有这个文件夹!", {
			type: "danger", // 定义颜色主题 
			icon: "exclamation-sign" // 定义消息图标
			}).show();</script>');
    }

    // 压缩前
    $sizeBefor = getDirectorySize($folder);

    compress($folder, $type, $source);

    echo '
    <script> new $.zui.Messager("压缩完毕!", {
		type: "success", // 定义颜色主题 
		icon: "ok-sign" // 定义消息图标
	}).show();</script>';
} else {
    $folder = 0;
    $sizeBefor = 0;
}
// 压缩后
$sizeAfter = getDirectorySize($folder);
?>
<h2 style="text-align:center">压缩完毕</h2>
<h4 style="text-align:center;">压缩前：<font color="red"><?php echo getDistUsed($sizeBefor); ?></font>压缩后：<font color="green"><?php echo getDistUsed($sizeAfter); ?></font>
</h4>
<pre>
无论使用哪种压缩均为不可逆操作,并且非常占用硬件资源。
如机器配置过低可能会导致CPU、内存飙升!
<font color="red">Imgcompress</font> 自带压缩为轻微有损压缩图片 此压缩有可能使图片变大,特别是小图片!也有一定概率改变图片方向。
<font color="red">Imgcompress</font> 对自身机器要求高,如图片过多会导致脚本崩溃或者超时(已经预处理超时和脚本崩溃处理,但是有概率重现)!
<font color="red">TinyPng</font> 是 https://tinify.cn/ 提供的API,需要自行申请,对服务器要求较低,但是对网络要求高!如在国内可能导致非常慢而超时崩溃(已预处理,但是有概率重现)。
获取TinyPng key https://tinify.cn/developers 并填入 <font color="red">API 设置->TinyPng Key</font> 文件。
</pre>
<script>
    // 更改网页标题
    document.title = "图片压缩"
</script>
<?php
/** 底部广告 */
if ($config['ad_bot']) echo $config['ad_bot_info'];
/** 引入底部 */
include APP_ROOT . '/app/footer.php';
