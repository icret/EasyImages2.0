<?php
/*
 * API 页面管理
 */
require_once '../libs/header.php';
require_once APP_ROOT . '/config/api_key.php';
require_once APP_ROOT . '/api/libs//apiFunction.php';

// 检测登录
if (!is_online()) {
	checkLogin();
}

// 查找用户ID或者Token
if (isset($_POST['radio'])) {
	if ($_POST['radio'] == 'id') {
		$radio_value = '用户token：' . getIDToken($_POST['radio-value']);
	} elseif ($_POST['radio'] == 'token') {
		$radio_value = '用户ID：' . getID($_POST['radio-value']);
	} else {
		$radio_value = null;
	}
}
// 删除非空目录
if (isset($_POST['delDir'])) {
	$delDir = APP_ROOT . $config['path'] . $_POST['delDir'];
	if (deldir($delDir)) {
		echo '
		<script> new $.zui.Messager("删除成功！", {type: "success" // 定义颜色主题 
		}).show();</script>';
		header("refresh:1;"); // 1s后刷新当前页面
	} else {
		echo '
		<script> new $.zui.Messager("删除失败！", {type: "danger" // 定义颜色主题 
		}).show();</script>';
		header("refresh:1;"); // 1s后刷新当前页面
	}
}
?>

<div class="container">
</div class="row">
<div class="col-md-12">
	<div class="alert alert-primary">
		<h3 style="text-align:center">EasyImage2.0 快捷操作中心</h2>
			<hr />
			<h5>此页面为常用快捷操作，目录保存以 年/月/日/ 递进，非必要请勿修改！否则会导致部分操作不可用。</h5>
			<h5>当前软件环境信息：PHP版本：<?php echo  phpversion(); ?>；PHP上传最大值：<?php echo ini_get('upload_max_filesize'); ?>；POST上传最大值：<?php echo ini_get('post_max_size'); ?>；</h5>
			<h5><?php
				$yesterday =  date("Y/m/d/", strtotime("-1 day"));
				echo '今日上传数量：' . getFileNumber(APP_ROOT . config_path()) . ' 昨日上传数量：' . getFileNumber(APP_ROOT . $config['path'] . $yesterday) . ' 占用空间：' . getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__))	. ' 剩余空间：' . getDistUsed(disk_free_space(__DIR__)); ?>
	</div>
</div>
<div class="col-md-12">
	<div class="col-md-4">
		<form class="form-condensed" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
			<label for="exampleInputMoney1">
				新Token需按要求填入
				<code>
					/config/api_key.php
				</code>
				才生效
			</label>
			<div class="input-group">
				<span class="input-group-addon">
					New Token
				</span>
				<input type="text" class="form-control" id="exampleInputMoney1" value="<?php echo privateToken(); ?>">
			</div>
	</div>
	<div class="col-md-4">
		<form></form>
		<form class="form-condensed" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="form-group">
				<label for="exampleInputAccount6">根据ID/Token查找用户信息</label>
				<input type="text" name="radio-value" id="exampleInputAccount6" class="form-control" placeholder="输入信息" value="<?php echo @$radio_value; ?>">
				<div class="radio-primary"><input type="radio" name="radio" value="id" id="primaryradio1" checked="checked"><label for="primaryradio1">根据ID查找用户Token</label></div>
				<div class="radio-primary"><input type="radio" name="radio" value="token" id="primaryradio2"><label for="primaryradio2">根据Token查找用户ID</label></div>
				<button type="submit" class="btn btn-mini btn-primary">
					查找
				</button>
			</div>
		</form>
	</div>
	<div class="col-md-4">
		<div id="delimgurl"></div>
		<div id="title"></div>
		<form class="form-condensed" method="get" action="del.php" id="form" name="delForm" onSubmit="getStr();" target="_blank">
			<div class="form-group">
				<label for="del">
					删除图片
				</label>
				<input type="url" name="url" class="form-control" id="del" placeholder="请输入图片链接" />
			</div>
			<label>格式：<code>https://i1.100024.xyz/i/2021/05/04/10fn9ei.jpg</code></label>
			<input type="submit" class="btn btn-mini btn-primary" value="删除" />
		</form>
	</div>
</div>
<div class="col-md-12">

	<div class="col-md-4">
		<form action="../libs/compressing.php" method="post" target="_blank">
			<div class="form-group">
				<label for="exampleInputInviteCode1">压缩文件夹内图片(格式：2021/05/10/)：</label>
				<input type="text" class="form-control form-date" placeholder="" name="folder" value="<?php echo date('Y/m/d/'); ?>" readonly="">
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="type" value="Imgcompress" checked="checked"> 使用本地压缩(默认上传已压缩，不需重复压缩)
				</label>
			</div>
			<div class="radio">
				<label>
					<input type="radio" name="type" value="TinyImg"> 使用TinyImag压缩（需要申请key)
				</label>
			</div>
			<div>
				<label>
					* 如果页面长时间没有响应，表示正面正在压缩！
				</label>
				<label>
					两种压缩均为不可逆，并且非常占用硬件资源。
				</label>
			</div>
			<button type="submit" class="btn  btn-mini btn-success">开始压缩</button>
		</form>
	</div>
	<div class="col-md-4">
		<table class="table table-hover table-bordered table-condensed table-responsive">
			<thead>
				<tr>
					<th>当前可用Token列表：</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach ($tokenList as $value) {
					echo '<tr><td>' . $value . '</td></tr>';
				}
				?>
			</tbody>
		</table>
	</div>
	<div class="col-md-4">
		<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
			<div class="form-group">
				<label for="exampleInputInviteCode1" style="color:red">删除所选日期文件夹（删除之后无法恢复！）：</label>
				<input type="text" class="form-control form-date" name="delDir" value="<?php echo date('Y/m/d/'); ?>" readonly="">
			</div>

			<button type="submit" class="btn btn-mini btn-danger" onClick="return confirm('确认要删除？\n* 删除文件夹后将无法恢复！');">删除目录</button>
		</form>
	</div>
</div>
</div>
</div>
</div>
<link href="../public/static/zui/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<script src="../public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
<script>
	// 动态显示要删除的图片
	var oBtn = document.getElementById('del');
	var oTi = document.getElementById('title');
	if ('oninput' in oBtn) {
		oBtn.addEventListener("input", getWord, false);
	} else {
		oBtn.onpropertychange = getWord;
	}

	function getWord() {
		var delimgurl = document.getElementById("delimgurl");
		delimgurl.innerHTML += '<img src="' + oBtn.value + '" width="200" class="img-rounded" /><br />';
	}

	// 仅选择日期
	$(".form-date").datetimepicker({
		weekStart: 1,
		todayBtn: 1,
		autoclose: 1,
		todayHighlight: 1,
		startView: 2,
		minView: 2,
		forceParse: 0,
		format: "yyyy/mm/dd/"
	});

	// Title
	document.title = "管理中心 - <?php echo $config['title']; ?>";
</script>

<?php require_once APP_ROOT . '/libs/footer.php';
