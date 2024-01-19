<?php
require_once __DIR__ . '/../app/function.php';
clearstatcache();
// 存在程序锁则跳转主页
if (file_exists(APP_ROOT . '/config/install.lock')) {
    exit(header("Location:/../index.php"));
}
$phpEnv = (PHP_VERSION >= 5.6) ? true : false;
$fileinfo = extension_loaded('fileinfo') ? true : false;
$gd = extension_loaded('gd') ? true :  false;
$openssl = extension_loaded('openssl') ? true :  false;

$file = substr(base_convert(fileperms(APP_ROOT . "/app/upload.php"), 10, 8), 3);
if (IS_WIN) {
    $file_php = true;
    $i_wjj =  true;
}
if (!IS_WIN) {
    if ($file == '755') {
        $file_php = true;
    } else {
        $file_php =  false;
    }
    if (is_writable(APP_ROOT . $config['path'])) {
        $i_wjj =  true;
    } else {
        $i_wjj =   false;
    }
}

$userINI = is_file(APP_ROOT . '/.user.ini') ? false : true; // user.ini

function checkPASS($name)
{
    if ($name) {
        echo '<p style="color:green;font-weight: bold"><i class="icon icon-check icon-2x"></i></p>';
    } else {
        echo '<p style="color:red;font-weight: bold"><i class="icon icon-times icon-2x"></p>';
    }
}

?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>EasyIamge 2.0 安装环境检测</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="EasyIamge 2.0 安装环境检测" />
    <meta name="description" content="EasyIamge 2.0 安装环境检测" />
    <link rel="stylesheet" rel="shortcut icon" href="./../favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./../public/static/zui/css/zui.min.css?v1.10.0">
    <link rel="stylesheet" href="./../public/static/nprogress/nprogress.min.css?v1.10.0">
    <script type="application/javascript" src="./../public/static/zui/lib/jquery/jquery-3.6.0.min.js?v3.4.1"></script>
    <script type="application/javascript" src="./../public/static/zui/js/zui.min.js?v1.10.0"></script>
    <script type="application/javascript" src="./../public/static/qrcode/qrcode.min.js?v2.0"></script>
    <script type="application/javascript" src="./../public/static/nprogress/nprogress.min.js"></script>
</head>

<body class="container">
    <!-- install header html end -->

    <h1 class="header-dividing" style="text-align:center">EasyIamge 2.0 安装环境检测</h1>
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th>检查名称</th>
                <th>图床要求</th>
                <th>检测结果</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>PHP</td>
                <td>PHP >= 5.6</td>
                <td><?php checkPASS($phpEnv); ?></td>
            </tr>
            <tr>
                <td>Fileinfo</td>
                <td>必须支持 | 安装后重启PHP</td>
                <td> <?php checkPASS($fileinfo); ?></td>
            </tr>
            <tr>
                <td>GD</td>
                <td>必须支持 | 安装后重启PHP</td>
                <td> <?php checkPASS($gd); ?></td>
            </tr>
            <tr>
                <td>upload.php</td>
                <td>0755可执行权限 | 非windows系统</td>
                <td> <?php checkPASS($file_php); ?></td>
            </tr>
            <tr>
                <td>openssl</td>
                <td>建议支持 | 生成加密删除url,需PHP>7.0</td>
                <td> <?php checkPASS($openssl); ?></td>
            </tr>
            <tr>
                <td>/i</td>
                <td>图片存储目录需可写</code></td>
                <td><?php checkPASS($i_wjj); ?></td>
            </tr>
            <tr>
                <td>.user.ini</td>
                <td>需关闭防跨目录读写文件 | 删除方法:<a href="https://lnmp.org/faq/lnmp-vhost-add-howto.html#user.ini" target="_blank"> LNMP</a> | <a href="https://www.bt.cn/bbs/forum.php?mod=viewthread&tid=36605&page=1#pid122035" target="_blank">BT宝塔</a></td>
                <td><?php checkPASS($userINI); ?></td>
            </tr>
        </tbody>
    </table>
    <?php
    $checkres = array($phpEnv, $fileinfo, $gd, $i_wjj, $file_php, $userINI);

    if (in_array(false, $checkres)) : ?>
        <p class="text-primary">如果使用Liunx主机权限错误可以使用以下命令: </p>
        <h6 class="code">chmod 755 -R <?php echo APP_ROOT; ?></h6>
        <h6 class="code">chown -R www:www <?php echo APP_ROOT; ?></h6>
        <a href="./README/chmod.png" data-toggle="lightbox" class="btn btn-mini btn-primary">宝塔面板赋予权限演示</a>
        <hr />
        <p class="text-danger">如果你不想安装上述PHP扩展或跳过安装过程请删除<code>install</code>目录 <a href="https://www.kancloud.cn/easyimage/easyimage/2635850" target="_blank" class="btn btn-mini btn-primary">跳过安装页面</a></p>
        <a href="./index.php"><button class="btn btn-danger" type="button">请满足上述要求后点击刷新</button></a>
    <?php else : ?>
        <form action="install.php" method="post">
            <input type="hidden" name="check" value="checked" readonly>
            <input type="submit" class="btn btn-primary" value="下一步(1/2)">
        </form>
    <?php endif; ?>
    <!-- install bottom HTML start -->
    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">x</span>
                        <span class="sr-only">关闭</span></button>
                    <h4 class="modal-title icon icon-mobile" style="text-align: center">扫描二维码使用手机上传</h4>
                </div>
                <div class="modal-body" align="center">
                    <input id="text" type="hidden" value="" />
                    <p id="qrcode"></p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger btn-sm" href="" target="_blank">访问</a>
                    <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">关闭</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        // NProgress
        NProgress.configure({
            showSpinner: false
        });
        NProgress.set(0.0);
        NProgress.set(0.5);

        // js二维码 获取当前网址并赋值给id=text的value
        document.getElementById("text").value = window.location.href;
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            width: 200,
            height: 200,
        });

        function makeCode() {
            var elText = document.getElementById("text");
            if (!elText.value) {
                alert("Input a text");
                elText.focus();
                return;
            }
            qrcode.makeCode(elText.value);
        }
        makeCode();
        $("#text").on("blur",
            function() {
                makeCode();
            }).on("keydown",
            function(e) {
                if (e.keyCode == 13) {
                    makeCode();
                }
            });
    </script>
    <footer class="text-muted small col-md-12" style="text-align: center;margin-bottom: 10px">
        <hr>
        <div>
            <!-- 对话框触发按钮 -->
            <a href="#" data-position="center" data-moveable="inside" data-moveable="true" data-toggle="modal" data-target="#myModal">
                <i class="icon icon-qrcode"></i>二维码 </a>
        </div>
        <?php echo 'Copyright © 2018-' . date('Y'); ?>
        <a href="https://png.cm/" target="_blank">EasyImage</a> By
        <a href="https://blog.png.cm/902.html" target="_blank">Icret</a> Version:<a href="https://github.com/icret/EasyImages2.0" target="_blank"><?php echo APP_VERSION; ?></a>
    </footer>
</body>

</html>