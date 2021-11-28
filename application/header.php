<?php
require_once __DIR__ . '/function.php';
require_once APP_ROOT . '/application/total_files.php';
?>

<!DOCTYPE html>
<html lang="zh-cn">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title><?php echo  $config['title']; ?></title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="keywords" content="<?php echo  $config['keywords']; ?>" />
	<meta name="description" content="<?php echo  $config['description']; ?>" />
	<link rel="shortcut icon" href="<?php echo $config['domain']; ?>/favicon.ico" type="image/x-icon" />
	<link rel="dns-prefetch" href="<?php echo $config['imgurl']; ?>" />
	<link rel="dns-prefetch" href="<?php echo $config['static_cdn_url']; ?>" />
	<link href="<?php static_cdn(); ?>/public/static/zui/css/zui.min.css?v1.9.2" rel="stylesheet">
	<link href="<?php static_cdn(); ?>/public/static/zui/lib/uploader/zui.uploader.min.css?v1.9.2" rel="stylesheet">
	<link href="<?php static_cdn(); ?>/public/static/nprogress.min.css?v0.2.0" rel="stylesheet">
	<script src="<?php static_cdn(); ?>/public/static/zui/lib/jquery/jquery-3.4.1.min.js?v3.4.1"></script>
	<script src="<?php static_cdn(); ?>/public/static/zui/js/zui.min.js?v1.9.2"></script>
	<script src="<?php static_cdn(); ?>/public/static/qrcode.min.js?v2.0"></script>
	<script src="<?php static_cdn(); ?>/public/static/zui/lib/clipboard/clipboard.min.js?vv1.5.5"></script>
	<script src="<?php static_cdn(); ?>/public/static/nprogress.min.js"></script>
	<style>
		.uploader-files {
			min-height: 160px;
			border-style: dashed;
		}

		@media screen and (min-width:960px) {
			.listNum img {
				width: 268px;
				height: 268px;
			}
		}
	</style>
</head>

<body class="container">
	<?php if ($config['ad_top']) {
		echo $config['ad_top_info'];
	} ?>
	<div class="md-lg-12 header-dividing">
		<ul class="nav nav-pills">
			<li class="<?php echo getActive('index'); ?>">
				<a href="<?php echo $config['domain']; ?>/index.php">
					<i class="icon icon-home"> 首页</i>
				</a>
			</li>
			<li class="<?php echo getActive('list'); ?>">
				<a href="<?php echo $config['domain']; ?>/application/list.php?date=<?php echo date('Y/m/d/') ?>&num=<?php echo $config['listNumber']; ?>">
					<i class="icon icon-list"> 广场</i>
					<span class="label label-badge label-success">
						<?php echo getFileNumber(APP_ROOT . config_path()); ?></span>
				</a>
			</li>
			<?php
			if (is_online()) {
				echo '
			<li class="dropdown dropdown-hover">
				<a class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-cogs"> 设置</i><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li class="' . getActive('admin.inc') . '";><a href="' . $config['domain'] . '/admin/admin.inc.php' . '"><i class="icon icon-desktop"> 网站设置</i></a></li>
					<li class="divider"></li>
					<li class="' . getActive('tools') . '";><a href="' . $config['domain'] . '/admin/tools.php' . '"><i class="icon icon-rocket"> 快捷工具</i></a></li>
					<li class="divider"></li>				
					<li class="' . getActive('counts') . '";><a href="' . $config['domain'] . '/admin/counts.php' . '"><i class="icon icon-pie-chart"> 上传统计</i></a></li>
				</ul>
			</li>
				';
			}
			?>

		</ul>
	</div>
	<!-- 顶部导航栏END -->