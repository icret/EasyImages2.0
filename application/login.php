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
		if (isset($_POST['password'])) {
			checkLogin();
			header("refresh:2;url=" . $config['domain'] . "");
		}
	} else {
		echo '<script> alert("验证码错误");</script>';
	}
}

?>
<form class="form-horizontal" action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post" onsubmit="return md5_post()">
	<div class="form-group">
		<label for="account" class="col-sm-2">账号</label>
		<div class="has-success col-md-3 col-sm-5">
			<input type="text" name="account" id="account" class="form-control" value="Admin" placeholder="请输入登录账号" readonly>
		</div>
	</div>
	<div class="form-group">
		<label for="password" class="col-sm-2">密码</label>
		<div class="has-success col-md-3 col-sm-5">
			<input type="password" name="password" id="password" class="form-control" placeholder="请输入登录密码">
		</div>
		<input type="hidden" name="password" id="md5_password">
	</div>

	<div class="form-group">
		<label class="col-sm-2">验证码</label>
		<div class="has-success col-md-3 col-sm-5">
			<label><img src="<?php echo $config["domain"]."/application/captcha.php";?>" onClick="this.src='<?php echo $config["domain"]."/application/captcha.php";?>?nocache='+Math.random()" title="点击换一张" width="150px" height="40px" /></label>
			<input class="form-control" type="text" name="code" value="" placeholder="请输入上方4位数验证码 - 注意大小写" />
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<div class="checkbox">
				<label>
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
