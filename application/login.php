<?php
/*
 * 登录页面
 */
require_once 'function.php';
require_once APP_ROOT . '/application/header.php';

header("Content-Type: text/html;charset=utf-8");
if (isset($_REQUEST['code'])) {
	session_start();

	if ($_REQUEST['code'] == $_SESSION['code']) {
		// 提交登录
		if (isset($_POST['password']) and isset($_POST['user'])) {
			$postUser = $_POST['user'];
			$postPWD = $_POST['password'];
			if ($postUser == $config['user']) {
				if ($postPWD == $config['password']) {
					setcookie($postUser, $postPWD, time() + 3600 * 24 * 14, '/');
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

if (isset($_GET['install'])) {
	header('Location: login.php');
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
			<label><img src="<?php echo $config["domain"] . "/application/captcha.php"; ?>" onClick="this.src='<?php echo $config["domain"] . "/application/captcha.php"; ?>?nocache='+Math.random()" title="点击换一张" width="150px" height="40px" /></label>
			<input class="form-control" type="text" name="code" value="" placeholder="请输入上方4位数验证码 - 注意大小写" />
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
