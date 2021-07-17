<?php
require_once __DIR__ . '/header.php';
if (!$config['showSwitch'] and !is_online()) {
	echo '<div class="alert alert-info">管理员关闭了预览哦~~</div>';
} else {

	$path = $_GET['date'] ??  date('Y/m/d/');
	$keyNum = $_GET['num'] ?? $config['listNumber'];
	$fileArr = getFile(APP_ROOT . config_path($path));
	if ($fileArr[0]) {
		foreach ($fileArr as $key => $value) {
			if ($key < $keyNum) {
				$boxImg = $config['imgurl'] . config_path($path) . $value;
				echo '<div class="col-md-4 col-sm-6 col-lg-3"><div class="card listNum">
			   <img data-toggle="lightbox"  data-image="' . $boxImg . '" src="../public/images/loading.svg" class="img-thumbnail" alt="简单图床-EasyImage" >
					<a href="' . $boxImg . '" target="_blank">		
						<div class="pull-left" style="margin-top:5px;">
						<span class="label label-success">查看大图</span>
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
		echo '<div class="alert alert-danger">今天还没有上传的图片哟~~ <br />快来上传第一张吧~！</div>';
	}
}

$yesterday =  date("Y/m/d/", strtotime("-1 day"));	// 昨日日期
$todayUpload =  getFileNumber(APP_ROOT . config_path());	// 今日上传数量
$yesterdayUpload = getFileNumber(APP_ROOT . $config['path'] . $yesterday);	// 昨日上传数量
$spaceUsed = getDistUsed(disk_total_space(__DIR__) - disk_free_space(__DIR__));		// 占用空间

// 当前日期全部上传
$allUploud = $_GET['date'] ?? date('Y/m/d/');
$allUploud = getFileNumber(APP_ROOT . $config['path'] . $allUploud);

@$httpUrl = array(
	'date' => $path,
	'num' => getFileNumber(APP_ROOT . config_path($path)),
);
?>
<style>
	* {
		list-style: none;
		border: 0;
	}

	#rocket-to-top div {
		left: 0;
		margin: 0;
		overflow: hidden;
		padding: 0;
		position: absolute;
		top: 0;
		width: 149px;
	}

	#rocket-to-top .level-2 {
		background: url("../public/images/rocket_button_up.png") no-repeat scroll -149px 0 transparent;
		display: none;
		height: 250px;
		opacity: 0;
		z-index: 1;
	}

	#rocket-to-top .level-3 {
		background: none repeat scroll 0 0 transparent;
		cursor: pointer;
		display: block;
		height: 150px;
		z-index: 2;
	}

	#rocket-to-top {
		background: url("../public/images/rocket_button_up.png") no-repeat scroll 0 0 transparent;
		cursor: default;
		display: block;
		height: 250px;
		margin: -125px 0 0;
		overflow: hidden;
		padding: 0;
		position: fixed;
		right: 0;
		top: 80%;
		width: 149px;
		z-index: 11;
	}
</style>
<script src="../public/static/lazyload.js"></script>
<link href="../public/static/zui/lib/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<script src="../public/static/zui/lib/datetimepicker/datetimepicker.min.js"></script>
<div class="col-md-12">
	<div class="col-md-6">
		<a href="list.php"><span class="label label-success label-outline"> 今日：<?php echo $todayUpload; ?>张</span></a>
		<a href="list.php?date=<?php echo $yesterday; ?>"><span class="label label-warning  label-outline"> 昨日：<?php echo $yesterdayUpload; ?>张</span></a>
		<a href="list.php?<?php echo http_build_query($httpUrl); ?>"><span class="label label-info  label-outline"> 当前上传：<?php echo $allUploud; ?>张</span></a>
		<span class="label label-danger  label-outline"> 占用：<?php echo $spaceUsed; ?></span>
	</div>
	<div class="col-md-6">
		<form class="form-inline" action="list.php" method="get">
			<div class="form-group">
				<label for="exampleInputInviteCode3">按日期：</label>
				<input type="text" class="form-control form-date" value="<?php echo date('Y/m/d/'); ?>" name="date" readonly="">
			</div>
			<button type="submit" class="btn btn-primary">跳转</button>
		</form>
	</div>
</div>
<!-- 返回顶部 -->
<div style="display: none;" id="rocket-to-top">
	<div style="opacity:0;display: block;" class="level-2"></div>
	<div class="level-3"></div>
</div>
<script>
	// 返回顶部
	$(function() {
		var e = $("#rocket-to-top"),
			t = $(document).scrollTop(),
			n,
			r,
			i = !0;
		$(window).scroll(function() {
				var t = $(document).scrollTop();
				t == 0 ? e.css("background-position") == "0px 0px" ? e.fadeOut("slow") : i && (i = !1, $(".level-2").css("opacity", 1), e.delay(100).animate({
						marginTop: "-1000px"
					},
					"normal",
					function() {
						e.css({
								"margin-top": "-125px",
								display: "none"
							}),
							i = !0
					})) : e.fadeIn("slow")
			}),
			e.hover(function() {
					$(".level-2").stop(!0).animate({
						opacity: 1
					})
				},
				function() {
					$(".level-2").stop(!0).animate({
						opacity: 0
					})
				}),
			$(".level-3").click(function() {
				function t() {
					var t = e.css("background-position");
					if (e.css("display") == "none" || i == 0) {
						clearInterval(n),
							e.css("background-position", "0px 0px");
						return
					}
					switch (t) {
						case "0px 0px":
							e.css("background-position", "-298px 0px");
							break;
						case "-298px 0px":
							e.css("background-position", "-447px 0px");
							break;
						case "-447px 0px":
							e.css("background-position", "-596px 0px");
							break;
						case "-596px 0px":
							e.css("background-position", "-745px 0px");
							break;
						case "-745px 0px":
							e.css("background-position", "-298px 0px");
					}
				}
				if (!i) return;
				n = setInterval(t, 50),
					$("html,body").animate({
						scrollTop: 0
					}, "slow");
			});
	});
</script>
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
		delay: 300
	})
</script>
<?php require_once APP_ROOT . '/libs/footer.php';
