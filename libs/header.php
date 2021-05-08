<?php
require_once __DIR__ . '/function.php';
echo '<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>' . $config['title'] . '</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="' . $config['keywords'] . '" />
    <meta name="description" content="' . $config['description'] . '" />
    <link rel="shortcut icon" href="//' . $_SERVER['HTTP_HOST'] . '/favicon.ico"  type="image/x-icon" />
    ' . static_cdn() . '
    <style>
        .uploader-files{
            min-height:160px;
            border-style:dashed;
        }
		
		@media screen and (min-width:960px){
			.listNum img {
				width:268px;
				height:268px;
				}
		}
    </style>
</head>
<body class="container">
    ' . showAD('top') . '
<div class="md-lg-12 header-dividing">
	<ul class="nav nav-pills">
		<li class="' . getActive('index') . '">
			<a href="//' . $_SERVER['HTTP_HOST'] . '/index.php">
			<i class="icon icon-home"> 首页</i>
			</a>
		</li>
		<li class="' . getActive('list') . '">
			<a href="//' . $_SERVER['HTTP_HOST'] . '/libs/list.php?date='. date('Y/m/d/').'&num='.$config['listNumber'].'">
				<i class="icon icon-list"> 广场</i>
				<span class="label label-badge label-success">
					' . getFileNumber(__DIR__ . '/../' . config_path()) . '</span>
			</a>
		</li>
		<li class="">
			<a href="https://github.com/icret/easyImages2.0" target="_blank">
			<i class="icon icon-github "> 源码下载</i>
			</a>
		</li>
	</ul>
</div>
<!-- 顶部导航栏END -->
    ';
