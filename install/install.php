<?php
require_once __DIR__ . '/../application/function.php';
// 存在程序锁则跳转主页
if (file_exists(APP_ROOT . '/install/install.lock')) {
    exit(header("Location:/../index.php"));
}

// 验证上一步环境检测
$state = isset($_POST['check']) ? $_POST['check'] : exit(header("Location:index.php"));
if ($state !== 'checked') {
    exit(header("Location:index.php"));
}

?>
<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>EasyIamge 2.0 即将完成安装！</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="EasyIamge 2.0 即将完成安装！" />
    <meta name="description" content="EasyIamge 2.0 即将完成安装！" />
    <link rel="shortcut icon" href="./../public/favicon.ico" type="image/x-icon" />
    <link href="./../public/static/zui/css/zui.min.css?v1.9.2" rel="stylesheet">
    <script src="./../public/static/zui/lib/jquery/jquery-3.4.1.min.js?v3.4.1"></script>
    <script src="./../public/static/zui/js/zui.min.js?v1.9.2"></script>
    <script src="./../public/static/qrcode.min.js?v2.0"></script>
    <style>
        .message {
            font-size: 12px;
            font-weight: bold;
            color: #999;
        }

        .wrong {
            color: red;
        }

        .right {
            color: green;
        }
    </style>
</head>

<body class="container">
    <!-- install header html end -->
    <div class="col-md-12" style="height: 120px;"></div>
    <div class="col-md-12" style="text-align: center;">
        <form class="form-horizontal" action="./contorl.php" method="post">
            <div class="form-group">
                <label class="col-sm-2">网站域名,末尾不加"/"</label>
                <div class="col-md-6 col-sm-10">
                    <input type="url" class="form-control" name="domain" value="<?php echo get_whole_url('/install/install.php'); ?>" required="required" onkeyup="this.value=this.value.replace(/\s/g,'')" placeholder="网站域名与图片链接域名可以不同，比如A域名上传，可以返回B域名图片链接，如果不变的话，下边2个填写成一样的！" title="网站域名与图片链接域名可以不同，比如A域名上传，可以返回B域名图片链接，如果不变的话，下边2个填写成一样的！">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2">图片链接域名,末尾不加"/"</label>
                <div class="col-md-6 col-sm-10">
                    <input type="text" class="form-control" name="imgurl" value="<?php echo get_whole_url('/install/install.php'); ?>" required="required" placeholder="给图片的域名，末尾不加/，如果没有请填写和上边的一样即可" onkeyup="this.value=this.value.replace(/\s/g,'')" placeholder="图片域名">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 ">登录上传和后台管理密码</label>
                <span class="message">请输入8~18位密码</span>
                <div class="col-md-6 col-sm-10 register">
                    <input type="text" class="form-control inp" name="password" value="" required="required" placeholder="请使用英文输入法输入密码并不小于8位数" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 ">确认密码</label>
                <div class="col-md-6 col-sm-10">
                    <input type="text" class="form-control" name="repassword" value="" required="required" placeholder="确认密码" onkeyup="this.value=this.value.replace(/\s/g,'')">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="del_install" value="del"><span style="font-weight: bold;color:red;">删除安装目录</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-success">准备就绪，开始安装！</button>
                </div>
            </div>
        </form>
    </div>



    <script>
        var password = document.querySelector('.inp');
        var message = document.querySelector('.message');

        password.onblur = function() {
            if (this.value.length < 8 || this.value.length > 18) {
                message.innerHTML = '密码长度错误，应为8~18位';
                message.className = 'message wrong';
            } else {
                message.innerHTML = '密码长度正确';
                message.className = 'message right';
            }
        }
    </script>
    <!-- install bottom HTML start -->


    <div class="modal fade" id="myModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">×</span>
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
        <p><a href="/../admin/terms.php" target="_blank">请勿上传违反中国政策的图片</a><i class="icon icon-smile"></i></p>
        <div>
            <!-- 对话框触发按钮 -->
            <a href="#" data-position="center" data-moveable="inside" data-moveable="true" data-toggle="modal" data-target="#myModal">
                <i class="icon icon-qrcode"></i>二维码 </a>
        </div>
        <?php echo 'Copyright © 2018-' . date('Y'); ?>
        <a href="https://img.545141.com/" target="_blank">EasyImage</a> By
        <a href="https://www.545141.com/902.html" target="_blank">Icret</a> Version:<a href="https://github.com/icret/EasyImages2.0" target="_blank"><?php echo $config['version']; ?></a>
    </footer>
</body>

</html>