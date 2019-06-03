<?php
require_once __DIR__.'/function.php';
echo '
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta charset="utf-8">
    <title>'.$config['title'].'</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="'.$config['keywords'].'" />
    <meta name="description" content="'.$config['description'].'" />
    <link rel="shortcut icon" href="../public/static/favicon.ico"  type="image/x-icon" />
    '.static_cdn().'
    <style>
        .uploader-files{
            min-height:160px;
            border-style:dashed;
        }
    </style>
</head>
<body class="container">
    '.showAD('top').'
    <div class="md-lg-12 header-dividing">
        <ul class="nav nav-pills">
            <li class="active"><a href="index.php">首页</a></li>
            <li><a href="https://github.com/icret/easyImages2.0" target="_blank">GitHub<span class="label label-badge label-success"></span></a></li>
            <li><a href="tinyfilemanager.php" target="_blank">管理<span class="label label-badge label-success"></span></a></li>
            <li><a class="dropdown-toggle hidden-xs" data-toggle="dropdown" href="#">二维码<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <input id="text" type="hidden" value=""/>
                    <li id="qrcode" style="width:100%;">扫描二维码使用手机上传</li>
                </ul>
            </li>
        </ul>
    </div>
<!-- 顶部导航栏END -->
    ';