<?php
/*
 * 登录页面
 */
require_once __DIR__ . '/../application/function.php';
require_once APP_ROOT . '/application/header.php';
require_once APP_ROOT . '/config/config.guest.php';

// 验证登录
header("Content-Type: text/html;charset=utf-8");
if (isset($_REQUEST['code'])) {
	session_start();

	if (strtolower($_REQUEST['code']) == $_SESSION['code']) {
		// 提交登录
		if (isset($_POST['password']) and isset($_POST['user'])) {

			$postUser = strip_tags($_POST['user']);
			$postPWD = strip_tags($_POST['password']);

			global $guestConfig;
			if ($postUser == $config['user'] || in_array($postPWD, $guestConfig)) {
				if ($postPWD == $config['password'] || $postPWD == $guestConfig[$postUser]) {
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
					exit(header("refresh:1;"));
				}
			} else {
				echo '
				<script> 
					$.zui.Messager("用户名错误", {type: "danger" // 定义颜色主题
					}).show();
				</script>';
				exit(header("refresh:2;"));
			}
		}
	} else {
		echo '
		<script>
            new $.zui.Messager("验证码错误!", {type: "danger" // 定义颜色主题 
            }).show();
        </script>';
	}
}

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
}
?>
<form class="form-horizontal" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return md5_post()">
	<div class="form-group">
		<label for="account" class="col-sm-2">账号</label>
		<div class="has-success col-md-3 col-sm-5">
			<input type="text" name="user" id="account" class="form-control" value="" placeholder="请输入登录账号">
		</div>
	</div>
	<div class="form-group">
		<label for="password" class="col-sm-2">密码</label>
		<div class="has-success col-md-3 col-sm-5">
			<input type="password" name="password" id="password" class="form-control" value="" placeholder="请输入登录密码">
		</div>
		<input type="hidden" name="password" id="md5_password">
	</div>
	<div class="form-group">
		<label class="col-sm-2">验证码</label>
		<div class="has-success col-md-3 col-sm-5">
			<label><img src="<?php echo $config["domain"] . "/application/captcha.php"; ?>" onClick="this.src='<?php echo $config["domain"] . "/application/captcha.php"; ?>?nocache='+Math.random()" title="点击换一张" /></label>
			<input class="form-control" type="text" name="code" value="" placeholder="请输入上方4位数验证码 - 不区分大小写" />
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label title="选不选都记得你，想退出就点击退出才可以哦!">
					<input type="checkbox" checked="checked"> 记住我
				</label>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button type="submit" class="btn btn-primary">登录</button>
		</div>
	</div>
</form>
<script src="<?php static_cdn(); ?>/public/static/md5.min.js"></script>
<script>
	function md5_post() {
		var password = document.getElementById('password');
		var md5pwd = document.getElementById('md5_password');
		md5pwd.value = md5(password.value);
		//可以校验判断表单内容，true就是通过提交，false，阻止提交
		return true;
	}
</script>
<?php
require_once APP_ROOT . '/application/footer.php';
