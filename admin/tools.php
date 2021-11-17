<?php
/*
 * 快捷操作中心页面
 */
require_once '../application/header.php';
require_once APP_ROOT . '/config/api_key.php';
require_once APP_ROOT . '/api/application/apiFunction.php';

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

// 恢复图片
if (isset($_GET['reimg'])) {
	$name = $_GET['reimg'];
	re_checkImg($name);
}

?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="alert alert-primary">
				<h3 style="text-align:center">EasyImage2.0 快捷操作中心</h3>
				<hr />
				<h5>目录保存以 年/月/日/ 递进，非必要请勿修改！否则会导致部分操作不可用；</h5>
				<h5>本人仅为程序开源创作，如非法网站使用与本人无关，请勿用于非法用途；</h5>
				<h5>请为本人博客<a href="https://www.545141.com/" target="_blank">www.545141.com</a>加上网址链接，谢谢支持。作为开发者你可以对相应的后台功能进行扩展（增删改相应代码）,但请保留代码中相关来源信息（例如：本人博客，邮箱等）。</h5>
				<p>
					<button type="button" class="btn btn-mini" data-toggle="collapse" data-target="#collapseExample">服务信息<i class="icon icon-hand-down"></i></button>
					<a href="https://img.545141.com/sponsor/index.html" target="_blank"><button type="button" class="btn btn-danger btn-mini">打赏作者 <i class="icon icon-heart-empty"></i></button></a>
				</p>
				<div class="collapse" id="collapseExample">
					<div class="bg-danger with-padding">
						<h5>系统信息</h5>
						<hr />
						<p>服务器系统：<?PHP echo php_uname('s') . ' <small class="text-muted">' . php_uname() . '</small>'; ?></p>
						<p>WEB服务：<?PHP echo $_SERVER['SERVER_SOFTWARE']; ?></p>
						<p>服务器IP：<?PHP echo  GetHostByName($_SERVER['SERVER_NAME']) ?></p>
						<p>系统时间：<?PHP echo date("Y-m-d G:i:s"); ?></p>
						<p>已用空间：<?php echo  getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__)) . ' 剩余空间：' . getDistUsed(disk_free_space(__DIR__)); ?></p>
						<h5>PHP信息</h5>
						<hr />
						<p>PHP版本：<?php echo  phpversion(); ?></p>
						<p>GD版本：<?php echo (gd_info()["GD Version"]); ?></p>
						<p>PHP上传限制：<?PHP echo get_cfg_var("upload_max_filesize"); ?></p>
						<p>POST上传限制：<?php echo ini_get('post_max_size'); ?></p>
						<p>PHP最长执行时间：<?PHP echo get_cfg_var("max_execution_time") . "秒 "; ?></p>
						<p>PHP允许占用内存：<?PHP echo get_cfg_var("memory_limit") . "M "; ?></p>
						<h5>我的信息</h5>
						<hr />
						<p>浏览器：<?php echo $_SERVER['HTTP_USER_AGENT']; ?></p>
						<p>访问者IP：<?php echo  $_SERVER["REMOTE_ADDR"]; ?></p>
						<h5>图床信息</h5>
						<hr />
						<p><?php
							if (empty($config['TinyImag_key'])) {
								echo '压缩图片 TinyImag Key未填写，申请地址：<a href="https://tinypng.com/developers" target="_blank">https://tinypng.com/developers</a><br/>';
							} else {
								echo '压缩图片 TinyImag Key已填写<br/>';
							}
							if (empty($config['moderatecontent_key'])) {
								echo '图片检查 moderatecontent key未填写，申请地址： <a href="https://client.moderatecontent.com" target="_blank">https://client.moderatecontent.com/</a>';
							} else {
								echo '图片检查 moderatecontent key已填写';
							}
							?>
						</p>
						<p>当前版本：<?php echo $config['version']; ?>，Github版本：<a href="https://github.com/icret/EasyImages2.0/releases" target="_blank"><?php echo getVersion(); ?></a></p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="col-md-4">
				<form class="form-condensed" action="<?php $_SERVER['SCRIPT_NAME']; ?>" method="post">
					<label for="exampleInputMoney1">
						新Token需按要求填入
						<code>/config/api_key.php</code>
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
				<form class="form-condensed" action="<?php $_SERVER['SCRIPT_NAME']; ?>" method="post">
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
				<form class="form-condensed" method="get" action="../application/del.php" id="form" name="delForm" onSubmit="getStr();" target="_blank">
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
				<form action="../application/compressing.php" method="post" target="_blank">
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
				<form action="<?php $_SERVER['SCRIPT_NAME']; ?>" method="post">
					<div class="form-group">
						<label for="exampleInputInviteCode1" style="color:red">删除所选日期文件夹（删除之后无法恢复！）：</label>
						<input type="text" class="form-control form-date" name="delDir" value="<?php echo date('Y/m/d/'); ?>" readonly="">
					</div>
					<button type="submit" class="btn btn-mini btn-danger" onClick="return confirm('确认要删除？\n* 删除文件夹后将无法恢复！');">删除目录</button>
				</form>
			</div>
		</div>
		<div class="col-md-12">
			<hr>
			<div class="col-md-7">
				<p>
					<button type="button" class="btn" data-toggle="collapse" data-target="#lis_cache">疑似违规的图片<i class="icon icon-hand-down"></i></button>
				</p>
				<div class="collapse" id="lis_cache">
					<p>为了访问速度，仅显示最近20张图片；监黄需要在<code>config.php</code>中开启<code>checkImg</code>属性。</p>
					<p>key申请地址：<a href="https://client.moderatecontent.com/" target="_blank">https://client.moderatecontent.com/</a></p>
					<p>获得key后填入<code>/config/api_key.php</code>-><code>moderatecontent</code>属性</p>
					<div class="table-responsive">
						<table class="table table-hover table-bordered table-auto table-condensed table-striped">
							<thead>
								<tr>
									<th>序号</th>
									<th>缩略图</th>
									<th>文件名</th>
									<th>大小</th>
									<th>查看图片</th>
									<th>还原图片</th>
									<th>删除图片</th>
								</tr>
							</thead>
							<tbody>
								<?php
								// 获取被隔离的文件
								@$cache_dir = APP_ROOT . $config['path'] . 'suspic/';   							// cache目录
								@$cache_file = getFile($cache_dir);  												// 获取所有文件
								@$cache_num = count($cache_file);    												// 统计目录文件个数
								for ($i = 0; $i < $cache_num and $i < 21; $i++) {									// 循环输出文件
									$file_cache_path = APP_ROOT . $config['path'] . 'suspic/' . $cache_file[$i]; 	// 图片绝对路径
									$file_path =  $config['path'] . 'suspic/' . $cache_file[$i];					// 图片相对路径
									@$file_size =  getDistUsed(filesize($file_cache_path));                  		// 图片大小
									@$filen_name = $cache_file[$i];													// 图片名称
									$url = $config['imgurl'] . $config['path'] . 'suspic/' . $cache_file[$i];   	// 图片网络连接
									$unlink_img = $config['domain'] . '/application/del.php?url=' . $url;           // 图片删除连接
									// 缩略图文件
									$thumb_cache_file = $config['domain'] . '/application/thumb.php?img=' . $file_path . '&width=300&height=300';
									echo '
								<tr>
									<td>' . $i . '</td>
									<td><img data-toggle="lightbox" src="' . $thumb_cache_file . '" data-image="' . $thumb_cache_file . '" class="img-thumbnail" ></td>
									<td>' . $filen_name . '</td>
									<td>' . $file_size . '</td>
									<td><a class="btn btn-mini" href="' . $url  . '" target="_blank">查看原图</a></td>
									<td><a class="btn btn-mini btn-success" href="?reimg=' . $filen_name . '">恢复图片</a></td>
									<td><a class="btn btn-mini btn-danger" href="' . $unlink_img . '" target="_blank">删除图片</a></td>
								</tr>
									';
								}
								echo '
								<span class="label label-primary label-outline">总数：' . $cache_num . '</span>&nbsp;
								<form action="' . $_SERVER['SCRIPT_NAME'] . '" method="post">
									<input type="hidden" name="delDir" value="/suspic/" readonly="">
									<button class="btn btn-danger btn-mini" ">删除全部违规图片</button>
								</form>
								';
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<form class="form-inline" action="<?php $_SERVER['SCRIPT_NAME']; ?>" method="post">
					<span class="label label-badge label-primary label-outline">已缓存文件：<?php echo getFileNumber(APP_ROOT . $config['path'] . 'thumb/'); ?>
						占用<?php echo getDistUsed(getDirectorySize(APP_ROOT . $config['path'] . 'thumb/')); ?>
						<button type="submit" class="btn btn-mini btn-primary" name="delDir" value="thumb/" onClick="return confirm('确认要清理缓存？\n* 删除文件夹后将无法恢复！');">清理</button></span>
				</form>
			</div>
		</div>
	</div>
</div>
<link href="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<script src="<?php static_cdn(); ?>/public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
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
<?php require_once APP_ROOT . '/application/footer.php';
