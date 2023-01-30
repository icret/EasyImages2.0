<!DOCTYPE html>
<html lang="zh-cn">
<?php require_once __DIR__ . '/function.php'; ?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="renderer" content="webkit" />
	<meta name="force-rendering" content="webkit" />
	<meta name="author" content="Icret EasyImage2.0">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo  $config['title']; ?></title>
	<meta name="keywords" content="<?php echo  $config['keywords']; ?>" />
	<meta name="description" content="<?php echo  $config['description']; ?>" />
	<link rel="shortcut icon" href="<?php static_cdn(); ?>/favicon.ico" type="image/x-icon" />
	<link href="<?php static_cdn(); ?>/public/static/zui/css/zui.min.css" rel="stylesheet">
	<link href="<?php static_cdn(); ?>/public/static/zui/theme/zui-theme-<?php echo $config['theme']; ?>.css" rel="stylesheet">
	<script src="<?php static_cdn(); ?>/public/static/zui/lib/jquery/jquery-3.6.0.min.js"></script>
	<script src="<?php static_cdn(); ?>/public/static/zui/js/zui.min.js"></script>
	<!--[if lt IE 9]>
    <script src="<?php static_cdn(); ?>/public/static/zui/lib/ieonly/html5shiv.js"></script>
    <script src="<?php static_cdn(); ?>/public/static/zui/lib/ieonly/respond.js"></script>
    <script src="<?php static_cdn(); ?>/public/static/zui/lib/ieonly/excanvas.js"></script>
  <![endif]-->
	<?php /** 自定义代码 */ if ($config['customize']) echo $config['customize']; ?>
</head>

<body class="container">
	<div class="page-header">
		<ul class="nav nav-pills">
			<li><a href="<?php echo $config['domain']; ?>"><i class="icon icon-home"></i> 首页</a></li>
			<?php if ($config['showSwitch'] || is_who_login('admin')) : /** 非管理或未开启不显示广场 */ ?>
				<li><a href="<?php echo $config['domain']; ?>/application/list.php"><i class="icon icon-th"></i> 广场<span class="label label-badge label-primary"><?php echo get_file_by_glob(APP_ROOT . config_path(), 'number'); ?></span></a></li>
			<?php endif; ?>
			<?php if ($config['history'] || is_who_login('admin')) : /** 非管理或未开启不显示上传历史 */ ?>
				<li><a href="<?php $config['domain']; ?>/application/history.php"><i class="icon icon-history"></i> 历史<span class="label label-badge label-primary"></span></a></li>
			<?php endif; ?>
			<?php if (is_who_login('admin')) : /** 非管理不显示设置 */ ?>
				<li><a href="<?php echo $config['domain']; ?>/admin/admin.inc.php"><i class="icon icon-cogs"></i> 设置</a></li>
			<?php endif; ?>
			<?php if ($config['chart_on'] && is_who_login('admin')) : /** 非管理或未开启不显示统计 */ ?>
				<li><a href="<?php echo $config['domain']; ?>/admin/chart.php"><i class="icon icon-pie-chart"></i> 统计</a></li>
			<?php endif; ?>

		</ul>
	</div>
	<!-- 顶部导航栏END -->