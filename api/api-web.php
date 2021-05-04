<?php
/*
 * API 页面管理
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/header.php';
require_once 'libs/apiFunction.php';
require_once '../libs/function.php';

/*//////////////////////////////////////////////////////////*/

// 查找token
if (isset($_POST['token'])) {
	$getID = '用户ID:' . getID($_POST['token']);
} else {
	$getID = null;
}

// 提交登录
if (isset($_POST['password'])) {
	checkLogin();
	header("refresh:1;"); // 1s后刷新当前页面
}

if (!is_online()) {
	echo '
		<script src="../public/static/md5.min.js"></script>
		<center>
			<div class="alert alert-success">需登录后才能查看全部信息</div>
			<div class="center" style="margin: 40px;">			
				<form class="form-inline" action="' . $_SERVER['PHP_SELF'] . '" method="post" onsubmit="return md5_post()">
					<div class="form-group">
						<div class="has-success">
							<input type="password" name="password" id="password" class="form-control" placeholder="请输入登录密码">					
						</div>
						<input type="hidden" name="password" id="md5_password">
					</div>
					<button type="submit" class="btn btn-primary">登录</button>
				</form>
			</div>
		</center>
		<script>
			function md5_post() {
				var password = document.getElementById(\'password\');
				var md5pwd = document.getElementById(\'md5_password\');
				md5pwd.value = md5(password.value);
				//可以校验判断表单内容，true就是通过提交，false，阻止提交
				return true;
			}
		</script>
		';
} else {
	echo '
<div class="row">
	<div class="col-md-4">
		<form class="form-condensed" action="' . $_SERVER['PHP_SELF'] . '" method="post">
			<label for="exampleInputMoney1">
				生成Token，新Token需按要求填入
				<code>
					tokenList.php
				</code>
				才生效
			</label>
			<div class="input-group">
				<span class="input-group-addon">
					New Token
				</span>
				<input type="text" class="form-control" id="exampleInputMoney1" value="' . privateToken() . '">
			</div>
			<div class="form-group">
				<label for="exampleInputMoney6">
					根据Token查找用户ID
				</label>
				<input type="text" class="form-control" id="exampleInputMoney6" name="token"
				placeholder="输入Token" value="' . $getID . '">
			</div>
			<button type="submit" class="btn btn-primary">
				查找
			</button>
		</form>
	</div>
	<div class="col-md-4">
	<div id="title" style="margin: 10px;"></div>
		<form class="form-condensed" method="get" action="del.php" id="form" name="delForm" onSubmit="getStr();">
			<div class="form-group">
				<label>
					删除图片 - 格式：<br /><code>https://i1.100024.xyz/i/2021/05/04/10fn9ei.jpg</code>
				</label>
				<input type="url" name="url" class="form-control" id="del" placeholder="请输入图片链接" />
			</div>

			<button type="submit" class="btn btn-mini btn-primary">
				删除
			</button>
		</form>
	</div>
	';
}
?>
<div class="col-md-4">
	<form class="form-condensed" action="index.php" method="post" enctype="multipart/form-data">
		<div class="form-group">
			<label>
				API上传测试 - 选择图片
			</label>
			<div class="form-group">
				<input type="file" name="image" class="form-control" accept="image/*">
			</div>
			<div class="form-group">
				<label>
					输入Token
				</label>
				<input type="text" name="token" class="form-control" placeholder="请输入Token" />
			</div>
		</div>
		<!-- API上传的图片链接
			<div class="form-group">
				<label>
					API上传的图片链接
				</label>
				<div class="form-group">
					<input type="text" class="form-control" readonly>
				</div>
			<br />
			-->
		<button type="submit" class="btn btn-mini btn-primary">
			API上传
		</button>
	</form>
</div>

<!-- API上传测试
	<div class="col-md-4">
		<form action="index.php" method="post" enctype="multipart/form-data">
		<input type="file" class="form-control"  accept="image/*" name="image" >		
		<input type="text" name = "token" placeholder="请输入Token"/>
		<button type="submit" class="btn btn-mini btn-primary">API上传测试</button>
		</form>	
	</div>
</div>
-->


</div>
<script>
	var oBtn = document.getElementById('del');
	var oTi = document.getElementById('title');
	if ('oninput' in oBtn) {
		oBtn.addEventListener("input", getWord, false);
	} else {
		oBtn.onpropertychange = getWord;
	}

	function getWord() {
		oTi.innerHTML = '<img src="' + oBtn.value + '" width="200" class="img-rounded" /><br />';
	}
	/** 
	// 动态修改请求地址
	function getStr(string, str) {
	    string = oBtn.value;
	    str = 'images';
	    var str_before = string.split(str)[0];
	    document.delForm.action = str_before + 'del.php';
	}
	*/
</script>
<?php require_once './../libs/footer.php';
