<?php
require_once __DIR__ . '/header.php';

if ($config['showSwitch']) {
	$path = $_GET['date'] ??  date('Y/m/d/');
	$keyNum = $_GET['num'] ?? $config['listNumber'];

	$fileArr = getFile(APP_ROOT . config_path($path));

	if ($fileArr[0]) {
		foreach ($fileArr as $key => $value) {
			if ($key < $keyNum) {
				$boxImg = $config['domain'] . config_path($path) . $value;
				echo '<div class="col-md-4 col-sm-6 col-lg-3"><div class="card listNum">
			   <img data-toggle="lightbox"  data-image="' . $boxImg . '" src="../public/images/Eclipse-1s-200px.svg" class="img-thumbnail" alt="简单图床-EasyImage" >
					<a href="' . $boxImg . '" target="_blank">		
						<div class="pull-left" style="margin-top:5px;">
						<span class="label label-success">打开原图</span>
						</div> 	
					</a>
					<a href="' . $config['domain'] . '/api/del.php?url=' . $boxImg . '" target="_blank">
						<div class="pull-right" style="margin-top:5px;">
							<span class="label label-primary">删除图片</span>
						</div> 	
					</a>		 
					</div>
				</div>';
			}
		}
	} else {
		echo '<p class="text-danger" style="center">今天还没有上传的图片哟~~ <br />快来上传第一张吧~！</p>';
	}
} else {
	echo '<p class="text-danger" style="center">管理员关闭了预览哦~~</p>';
}

$yesterday =  date("Y/m/d/", strtotime("-1 day"));	// 昨日日期
$todayUpload =  getFileNumber(APP_ROOT . config_path());	// 今日上传数量
$yesterdayUpload = getFileNumber(APP_ROOT . $config['path'] . $yesterday);	// 昨日上传数量
$spaceUsed = getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__));		// 占用空间

// 当前日期全部上传
$allUploud = $_GET['date'] ?: date('Y/m/d/');
$allUploud = getFileNumber(APP_ROOT . $config['path'] . $allUploud);

$httpUrl = array(
	'date' => $path,
	'num' => getFileNumber(APP_ROOT . config_path($path)),
);
?>

<script src="../public/static/lazyload.js"></script>
<link href="../public/static/zui/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<script src="../public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
<div class="col-md-12">
	<div class="col-md-6">
		<a href="list.php"><span class="label label-success label-outline"> 今日上传:<?php echo $todayUpload; ?>张</span></a>
		<a href="list.php?date=<?php echo $yesterday; ?>"><span class="label label-warning  label-outline"> 昨日上传:<?php echo $yesterdayUpload; ?>张</span></a>
		<a href="list.php?<?php echo http_build_query($httpUrl); ?>"><span class="label label-info  label-outline"> 当前日期共上传:<?php echo $allUploud; ?>张</span></a>
		<span class="label label-danger  label-outline"> 存储占用:<?php echo $spaceUsed; ?></span>
	</div>
	<div class="col-md-6">
		<form class="form-inline" action="list.php" method="get">
			<div class="form-group">
				<label for="exampleInputInviteCode3">按日期：</label>
				<input type="text" class="form-control form-date" value="2021/05/09/" name="date" readonly="">
			</div>
			<button type="submit" class="btn btn-primary">跳转</button>
		</form>
	</div>
</div>
<!-- 返回顶部 -->
<p id="back-top" style="display:none"><a href="#top"><span></span></a></p>
<style>
	#back-top {
		position: fixed;
		bottom: 10px;
		right: 8px;
		z-index: 99;
	}

	#back-top span {
		width: 70px;
		height: 140px;
		display: block;
		background: url(../public/images/top.png) no-repeat center center;
	}

	#back-top a {
		outline: none
	}
</style>

<script type="text/javascript">
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
	// 更改网页标题
	document.title = "图床广场 今日上传<?php echo $todayUpload; ?>张 昨日<?php echo $yesterdayUpload; ?>张 - <?php echo $config['title']; ?> "
	//懒加载
	var lazy = new Lazy({
		onload: function(elem) {
			console.log(elem)
		},
		delay: 2000
	})
	// 返回顶部
	$(function() {
		// hide #back-top first
		$("#back-top").hide();
		// fade in #back-top
		$(window).scroll(function() {
			if ($(this).scrollTop() > 400) {
				$('#back-top').fadeIn();
			} else {
				$('#back-top').fadeOut();
			}
		});
		// scroll body to 0px on click
		$('#back-top a').click(function() {
			$('body,html').animate({
				scrollTop: 0
			}, 500);
			return false;
		});
	});
</script>
<?php require_once APP_ROOT . '/libs/footer.php';
