<?php
/*
 * 登录页面
 */
require_once __DIR__ . '/../application/function.php';
require_once APP_ROOT . '/application/header.php';
require_once APP_ROOT . '/config/config.guest.php';
// 验证登录
header("Content-Type: text/html;charset=utf-8");


// 退出
if (isset($_GET['login'])) {
    if ($_GET['login'] = 'logout') {

        if (isset($_COOKIE['auth'])) {
            setcookie('auth', null, time() - 1, '/');
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
				window.setTimeout("window.location=\'./index.php\'",2000);
				</script>
        ';
        }
    }
    exit(require_once APP_ROOT . '/application/footer.php');
}

// 验证码
if ($config['captcha']) {
    if (isset($_REQUEST['code'])) {
        session_start();
        if (strtolower($_REQUEST['code']) !== $_SESSION['code']) {
            echo '
            <script>
                new $.zui.Messager("验证码错误!", {type: "danger" // 定义颜色主题 
                }).show();
                // 延时2s跳转
				window.setTimeout("window.location=\'./index.php\'",2000);
            </script>';

            exit(require_once APP_ROOT . '/application/footer.php');
        }
    }
}

// 提交登录
if (isset($_POST['password']) and isset($_POST['user'])) {

    global $guestConfig;
    $postUser = strip_tags($_POST['user']);
    $postPWD = strip_tags($_POST['password']);

    if ($postUser == $config['user'] || in_array($guestConfig[$postUser], $guestConfig)) {
        if ($postPWD == $config['password'] || $postPWD == $guestConfig[$postUser]['password']) {
            // 将账号密码序列化后存储
            $setCOK = serialize(array($postUser, $postPWD));

            setcookie('auth', $setCOK, time() + 3600 * 24 * 14, '/');
            echo '
                <script> 
                    new $.zui.Messager("登录成功", {type: "primary" // 定义颜色主题 
                    }).show();
                </script>';
            header("refresh:2;url=" . $config['domain'] . "");
        } else {
            echo '
                <script> 
                new $.zui.Messager("密码错误", {type: "danger" // 定义颜色主题
                }).show();
                </script>';
            header("refresh:2;");
        }
    } else {
        echo '
            <script> 
            new $.zui.Messager("账号不存在", {type: "danger" // 定义颜色主题
            }).show();
            </script>';
        header("refresh:2;");
    }
}
?>
<link href="<?php static_cdn(); ?>/public/static/login.css" rel="stylesheet">
<!-- 忘记密码 -->
<div class="modal fade" id="fogot">
    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title">
                    忘记账号/密码?
                </h4>
            </div>
            <div class="modal-body">
                <p class="text-primary">忘记账号可以打开<code>/config/config.php</code>文件找到user对应的键值->填入</p>
                <p class="text-success">忘记密码请将密码转换成MD5小写(<a href="<?php echo $config['domain'] . '/application/md5.php'; ?>" target="_blank" class="text-purple">转换网址</a>)->打开<code>/config/config.php</code>文件->找到password对应的键值->填入</p>
                <h4 class="text-danger">更改后会立即生效并重新登录,请务必牢记账号和密码! </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<section>
    <div class="container">
        <div class="user singinBx">
            <div class="imgBx">
                <img src="<?php echo $config['login_bg']; ?>" alt="简单图床登陆界面背景图" />
            </div>
            <div class="formBx">
                <form class="form-horizontal" action="/admin/index.php" method="post" onsubmit="return md5_post()">
                    <h2>登录</h2>
                    <label for="account" class="col-sm-2"></label>
                    <input type="text" name="user" id="account" class="form-control" value="" placeholder="输入登录账号" autocomplete="off" required="required">
                    <input type="password" name="password" id="password" class="form-control" value="" placeholder="输入登录密码" autocomplete="off" required="required">
                    <input type="hidden" name="password" id="md5_password">
                    <?php if ($config['captcha']) : ?>
                    <input class="form-control" type="text" name="code" value="" placeholder="输入下方4位数验证码" autocomplete="off" required="required" />
                    <div class="form-group">
                        <div class="col">
                            <label><img src="../application/captcha.php" onClick="this.src='../application/captcha.php?nocache='+Math.random()" title="点击换一张" /></label>
                        </div>
                    </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-block btn-primary">登 录</button>
                    <p class="signup">忘记账号或密码请查看<a href="#fogot" data-moveable="inside" data-remember-pos="false" data-toggle="modal" data-target="#fogot" data-position="center">帮助信息</a></p>
                </form>
            </div>
        </div>
        <div class="user singupBx">
            <div class="formBx">
                <form action="">
                    <h2>注册</h2>
                    <input type="text" name="telyzm" id="telyzm" placeholder="手机号">
                    <input type="email" name="" placeholder="邮箱地址">
                    <input type="password" name="" placeholder="设置密码">
                    <input type="password" name="" placeholder="再次输入密码">
                    <input type="submit" name="" value="注册">
                    <p class="signup">已有账号？<a href="#" onclick="topggleForm();">登录</a></p>
                </form>
            </div>
            <div class="imgBx"><img src="<?php echo $config['login_bg']; ?>" alt="简单图床登陆界面背景图" />
            </div>
        </div>
    </div>
</section>
</form>
<script src="<?php static_cdn(); ?>/public/static/md5/md5.min.js"></script>
<script>
    function md5_post() {
        var password = document.getElementById('password');
        var md5pwd = document.getElementById('md5_password');
        md5pwd.value = md5(password.value);
        //可以校验判断表单内容，true就是通过提交，false，阻止提交
        return true;
    }

    function topggleForm() {
        var container = document.querySelector('.container');
        container.classList.toggle('active');
    }
</script>
<?php require_once APP_ROOT . '/application/footer.php';
